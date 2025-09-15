<?php

namespace App\Http\Controllers\Backend\Promotion;

use App\Http\Controllers\Controller;
use App\Models\PromotionPackage;
use App\Models\ListingPromotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PromotionController extends Controller
{

    public function packages()
    {
        $packages = PromotionPackage::query()
            ->when(request('search'), function ($q) {
                $q->where('name', 'like', '%' . request('search') . '%')
                  ->orWhere('description', 'like', '%' . request('search') . '%');
            })
            ->when(request('status') !== 'all' && request('status') !== null, function ($q) {
                $q->where('is_active', request('status'));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total' => PromotionPackage::count(),
            'active' => PromotionPackage::where('is_active', 1)->count(),
            'inactive' => PromotionPackage::where('is_active', 0)->count(),
        ];

        return view('backend.promotions.packages.index', compact('packages', 'stats'));
    }

      public function createPackage()
    {
        return view('backend.promotions.packages.create');
    }

    public function storePackage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:promotion_packages,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'stripe_price_id' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            PromotionPackage::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'duration_days' => $request->duration_days,
                'stripe_price_id' => $request->stripe_price_id,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            return redirect()->route('backend.promotions.packages')
                ->with('success', __('Promotion package created successfully.'));
        } catch (\Exception $e) {
            Log::error('Error creating promotion package: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', __('Failed to create promotion package.'))
                ->withInput();
        }
    }


    public function editPackage(PromotionPackage $package)
    {
        return view('backend.promotions.packages.edit', compact('package'));
    }


    public function updatePackage(Request $request, PromotionPackage $package)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:promotion_packages,name,' . $package->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'stripe_price_id' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $package->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'duration_days' => $request->duration_days,
                'stripe_price_id' => $request->stripe_price_id,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            return redirect()->route('backend.promotions.packages')
                ->with('success', __('Promotion package updated successfully.'));
        } catch (\Exception $e) {
            Log::error('Error updating promotion package: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', __('Failed to update promotion package.'))
                ->withInput();
        }
    }


    public function deletePackage(PromotionPackage $package)
    {
        try {
            // Check if package has active promotions
            $activePromotions = ListingPromotion::where('promotion_package_id', $package->id)
                ->whereIn('payment_status', ['pending', 'paid'])
                ->count();

            if ($activePromotions > 0) {
                return redirect()->back()
                    ->with('error', __('Cannot delete package with active promotions.'));
            }

            $package->delete();

            return redirect()->route('backend.promotions.packages')
                ->with('success', __('Promotion package deleted successfully.'));
        } catch (\Exception $e) {
            Log::error('Error deleting promotion package: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', __('Failed to delete promotion package.'));
        }
    }


    public function togglePackageStatus(PromotionPackage $package)
    {
        try {
            $package->update(['is_active' => !$package->is_active]);

            return redirect()->back()
                ->with('success', __('Package status updated successfully.'));
        } catch (\Exception $e) {
            Log::error('Error toggling package status: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', __('Failed to update package status.'));
        }
    }

    public function requests()
    {
        $requests = ListingPromotion::query()
            ->with(['user', 'listing', 'promotionPackage'])
            ->when(request('search'), function ($q) {
                // $q->whereHas('user', function ($query) {
                //     $query->where('name', 'like', '%' . request('search') . '%')
                //         ->orWhere('email', 'like', '%' . request('search') . '%');
                // })
                $q->whereHas('listing', function ($query) {
                    $query->where('title', 'like', '%' . request('search') . '%');
                })
                ->orWhereHas('promotionPackage', function ($query) {
                    $query->where('name', 'like', '%' . request('search') . '%');
                });
            })
            ->when(request('status') !== 'all' && request('status') !== null, function ($q) {
                $q->where('payment_status', request('status'));
            })
            ->when(request('payment_method') !== 'all' && request('payment_method') !== null, function ($q) {
                $q->where('payment_method', request('payment_method'));
            })
            ->when(request('date_from'), function ($q) {
                $q->whereDate('created_at', '>=', request('date_from'));
            })
            ->when(request('date_to'), function ($q) {
                $q->whereDate('created_at', '<=', request('date_to'));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total' => ListingPromotion::count(),
            'pending' => ListingPromotion::where('payment_status', 'pending')->count(),
            'paid' => ListingPromotion::where('payment_status', 'paid')->count(),
            'failed' => ListingPromotion::where('payment_status', 'failed')->count(),
            'bank_transfer' => ListingPromotion::where('payment_method', 'bank_transfer')->count(),
            'stripe' => ListingPromotion::where('payment_method', 'stripe')->count(),
        ];

        return view('backend.promotions.requests.index', compact('requests', 'stats'));
    }


    public function showRequest(ListingPromotion $request)
    {
        $request->load(['user', 'listing', 'promotionPackage']);
        return view('backend.promotions.requests.show', compact('request'));
    }


    public function approveBankTransfer(Request $request, ListingPromotion $promotion)
    {
        if ($promotion->payment_method !== 'bank_transfer') {
            return redirect()->back()
                ->with('error', __('This promotion was not initiated via bank transfer.'));
        }

        if ($promotion->payment_status !== 'pending') {
            return redirect()->back()
                ->with('error', __('This promotion is not awaiting confirmation.'));
        }

        $validator = Validator::make($request->all(), [
            'admin_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $promotion->update([
                'payment_status' => 'paid',
                'payment_confirmed_at' => Carbon::now(),
                'admin_notes' => $request->admin_notes ?: 'Bank transfer approved by admin.',
            ]);

            $this->activatePromotion($promotion);

            return redirect()->back()
                ->with('success', __('Bank transfer approved and promotion activated successfully.'));
        } catch (\Exception $e) {
            Log::error('Error approving bank transfer: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', __('Failed to approve bank transfer.'));
        }
    }


    public function rejectBankTransfer(Request $request, ListingPromotion $promotion)
    {
        if ($promotion->payment_method !== 'bank_transfer') {
            return redirect()->back()
                ->with('error', __('This promotion was not initiated via bank transfer.'));
        }

        if ($promotion->payment_status !== 'pending') {
            return redirect()->back()
                ->with('error', __('This promotion is not awaiting confirmation.'));
        }

        $validator = Validator::make($request->all(), [
            'admin_notes' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $promotion->update([
                'payment_status' => 'failed',
                'admin_notes' => $request->admin_notes,
            ]);

            return redirect()->back()
                ->with('success', __('Bank transfer rejected successfully.'));
        } catch (\Exception $e) {
            Log::error('Error rejecting bank transfer: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', __('Failed to reject bank transfer.'));
        }
    }


    public function deleteRequest(ListingPromotion $request)
    {
        try {
            if ($request->bank_transfer_proof_path && Storage::disk('public')->exists($request->bank_transfer_proof_path)) {
                Storage::disk('public')->delete($request->bank_transfer_proof_path);
            }

            $request->delete();

            return redirect()->route('backend.promotions.requests')
                ->with('success', __('Promotion request deleted successfully.'));
        } catch (\Exception $e) {
            Log::error('Error deleting promotion request: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', __('Failed to delete promotion request.'));
        }
    }

    public function bulkApproveBankTransfers()
    {
        try {
            $pendingPromotions = ListingPromotion::where('payment_method', 'bank_transfer')
                ->where('payment_status', 'pending')
                ->get();

            $count = 0;
            foreach ($pendingPromotions as $promotion) {
                $promotion->update([
                    'payment_status' => 'paid',
                    'payment_confirmed_at' => Carbon::now(),
                    'admin_notes' => 'Bulk approved by admin.',
                ]);

                $this->activatePromotion($promotion);
                $count++;
            }

            return redirect()->back()
                ->with('success', __('Successfully approved :count bank transfer promotions.', ['count' => $count]));
        } catch (\Exception $e) {
            Log::error('Error bulk approving bank transfers: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', __('Failed to bulk approve bank transfers.'));
        }
    }

    private function activatePromotion(ListingPromotion $listingPromotion)
    {
        if ($listingPromotion->payment_status !== 'paid') {
            Log::warning('Attempted to activate a non-paid promotion.', ['id' => $listingPromotion->id]);
            return;
        }

        if (!$listingPromotion->promotionPackage || !$listingPromotion->listing) {
            Log::error('Cannot activate promotion due to missing package or listing.', ['id' => $listingPromotion->id]);
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
