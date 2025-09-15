<?php

namespace App\Http\Controllers\Api\Frontend\Promotion;

use App\Http\Controllers\Controller;
use App\Http\Resources\Promotion\ListingPromotionResource;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;

use App\Models\Backend\Listing;
use App\Models\ListingPromotion;
use App\Models\PromotionPackage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeCheckoutSession;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ListingPromotionController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        Stripe::setApiVersion('2022-11-15');
    }

    public function userPromotions(Request $request)
    {
        try {
            $promotions = ListingPromotion::where('user_id', Auth::id())
                ->with(['listing', 'promotionPackage'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return ApiResponse::success('User promotions retrieved successfully.', ListingPromotionResource::collection($promotions));
        } catch (\Exception $e) {
            Log::error('Error fetching user promotions: ' . $e->getMessage(), ['user_id' => Auth::id()]);
            return ApiResponse::error('Failed to retrieve promotions.', ['server_error' => $e->getMessage()], 500);
        }
    }


    public function initiatePromotion(Request $request, Listing $listing)
    {
        if ($listing->user_id !== Auth::id()) {
            return ApiResponse::forbidden('You are not authorized to promote this listing.');
        }

        $validator = Validator::make($request->all(), [
            'promotion_package_id' => 'required|exists:promotion_packages,id,is_active,1',
            'payment_method' => 'required|in:bank_transfer,stripe',
            'bank_transfer_proof' => 'required_if:payment_method,bank_transfer|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validation failed.', $validator->errors()->toArray(), 422);
        }

        $package = PromotionPackage::find($request->promotion_package_id);

        // if (!$package || !$package->is_active) {
        //     return ApiResponse::notFound('Promotion package not found or not active.');
        // }


        $existingPromotion = ListingPromotion::where('listing_id', $listing->id)
            ->where('promotion_package_id', $package->id)
            ->whereIn('payment_status', ['pending', 'paid'])
            ->where(function ($query) {
                $query->where('payment_status', 'pending')
                    ->orWhere(function($q) {
                        $q->where('payment_status', 'paid')
                            ->where('expires_at', '>', Carbon::now());
                    });
            })
            ->first();

        if ($existingPromotion) {
            if ($existingPromotion->payment_status === 'pending') {
                return ApiResponse::error(
                    'A pending promotion with this package already exists for this listing. Please complete or cancel it.',
                    ['type' => 'pending_promotion_exists'],
                    409
                );
            } else {
                return ApiResponse::error(
                    'This listing is already actively promoted with this package.',
                    ['type' => 'active_promotion_exists'],
                    409
                );
            }
        }


        $listingPromotion = new ListingPromotion([
            'user_id' => Auth::id(),
            'listing_id' => $listing->id,
            'promotion_package_id' => $package->id,
            'payment_method' => $request->payment_method,
            'amount_paid' => $package->price,
            'payment_status' => 'pending',
        ]);

        try {
            if ($request->payment_method === 'bank_transfer') {
                if ($request->hasFile('bank_transfer_proof')) {
                    $filePath = $request->file('bank_transfer_proof')->store('bank_proofs', 'public');
                    $listingPromotion->bank_transfer_proof_path = $filePath;
                } else {

                    return ApiResponse::error('Bank transfer proof is required.', ['bank_transfer_proof' => ['The bank transfer proof field is required when payment method is bank transfer.']], 422);
                }
                $listingPromotion->save();
                return ApiResponse::success(
                    'Promotion request submitted. Awaiting bank transfer confirmation.',
                    new ListingPromotionResource($listingPromotion->load('promotionPackage')),
                    201
                );
            }

            if ($request->payment_method === 'stripe') {
                if (empty($package->stripe_price_id)) {
                    return ApiResponse::error('Stripe payment is not configured for this package.', ['package_error' => 'Stripe configuration missing.'], 400);
                }

                $listingPromotion->save();

                $checkout_session = StripeCheckoutSession::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price' => $package->stripe_price_id,
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => url('/payment/success?session_id={CHECKOUT_SESSION_ID}'),
                    'cancel_url' => url('/payment/cancel'),
                    'client_reference_id' => Auth::id(),
                    'metadata' => [
                        'listing_promotion_id' => $listingPromotion->id,
                        'listing_id' => $listing->id,
                        'package_id' => $package->id,
                        'user_id' => Auth::id()
                    ]
                ]);

                $listingPromotion->transaction_id = $checkout_session->id;
                $listingPromotion->save();

                return ApiResponse::success('Redirecting to Stripe for payment.', [
                    'checkout_url' => $checkout_session->url,
                    'session_id' => $checkout_session->id,
                ]);
            }
        } catch (ApiErrorException $e) {
            Log::error('Stripe API Error during promotion initiation: ' . $e->getMessage(), [
                'listing_id' => $listing->id,
                'user_id' => Auth::id(),
                'stripe_error' => $e->getError()->message ?? $e->getMessage()
            ]);
            if (isset($listingPromotion) && $listingPromotion->exists && $listingPromotion->payment_status === 'pending') {
                $listingPromotion->delete();
            }
            return ApiResponse::error('Could not initiate Stripe payment. Please try again later.', ['stripe_error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::error('Error initiating promotion: ' . $e->getMessage(), [
                'listing_id' => $listing->id,
                'user_id' => Auth::id(),
                'payment_method' => $request->payment_method
            ]);
            if (isset($listingPromotion) && $listingPromotion->exists && $listingPromotion->payment_status === 'pending') {
                $listingPromotion->delete();
            }
            return ApiResponse::error('An unexpected error occurred. Please try again.', ['server_error' => $e->getMessage()], 500);
        }

        return ApiResponse::error('Invalid payment method specified.', [], 400);
    }


    public function confirmBankTransfer(Request $request, ListingPromotion $listingPromotion)
    {




        if ($listingPromotion->payment_method !== 'bank_transfer') {
            return ApiResponse::error('This promotion was not initiated via bank transfer.', [], 400);
        }
        if ($listingPromotion->payment_status !== 'pending') {
            return ApiResponse::error('This promotion is not awaiting confirmation or has already been processed.', ['current_status' => $listingPromotion->payment_status], 400);
        }

        $validator = Validator::make($request->all(), [
            'transaction_reference' => 'nullable|string|max:255',
            'admin_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validation failed.', $validator->errors()->toArray(), 422);
        }

        try {
            $listingPromotion->payment_status = 'paid';
            $listingPromotion->payment_confirmed_at = Carbon::now();
            $listingPromotion->transaction_id = $request->input('transaction_reference', $listingPromotion->transaction_id ?: 'manual_confirm_'.Str::random(8));
            $listingPromotion->admin_notes = $request->input('admin_notes', $listingPromotion->admin_notes ?: 'Bank transfer confirmed by admin.');
            $listingPromotion->save();

            $this->activatePromotion($listingPromotion);

            return ApiResponse::success(
                'Bank transfer confirmed and promotion activated successfully.',
                new ListingPromotionResource($listingPromotion->load(['listing', 'promotionPackage']))
            );
        } catch (\Exception $e) {
            Log::error('Error confirming bank transfer: ' . $e->getMessage(), ['listing_promotion_id' => $listingPromotion->id]);
            return ApiResponse::error('Failed to confirm bank transfer.', ['server_error' => $e->getMessage()], 500);
        }
    }


    public function activatePromotion(ListingPromotion $listingPromotion)
    {
        if ($listingPromotion->payment_status !== 'paid') {
            Log::warning('Attempted to activate a non-paid promotion.', ['id' => $listingPromotion->id, 'status' => $listingPromotion->payment_status]);
            return;
        }

        if (!$listingPromotion->promotionPackage || !$listingPromotion->listing) {
            Log::error('Cannot activate promotion due to missing package or listing.', ['id' => $listingPromotion->id]);
            $listingPromotion->admin_notes = ($listingPromotion->admin_notes ?? '') . ' Error: Missing package/listing during activation.';
            $listingPromotion->payment_status = 'failed';
            $listingPromotion->save();
            return;
        }

        $package = $listingPromotion->promotionPackage;
        $listing = $listingPromotion->listing;

        $now = Carbon::now();

        if (!$listingPromotion->starts_at) {
            $listingPromotion->starts_at = $now;
        }

        $listingPromotion->expires_at = Carbon::parse($listingPromotion->starts_at)->addDays($package->duration_days);
        $listingPromotion->save();

        $listing->is_featured = true;
        $listing->promoted_until = $listingPromotion->expires_at;
        $listing->save();

        Log::info('Promotion activated.', ['listing_promotion_id' => $listingPromotion->id, 'listing_id' => $listing->id]);
    }
}
