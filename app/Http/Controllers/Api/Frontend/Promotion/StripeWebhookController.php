<?php

namespace App\Http\Controllers\Api\Frontend\Promotion;


use App\Http\Controllers\Controller;
use App\Models\ListingPromotion;
use Illuminate\Http\Request;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Stripe\PaymentIntent;
use Stripe\Checkout\Session as StripeCheckoutSession;
use UnexpectedValueException;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->server('HTTP_STRIPE_SIGNATURE');
        $webhookSecret = config('services.stripe.webhook.secret');
        $event = null;

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (UnexpectedValueException $e) {
            Log::error('Stripe Webhook Error: Invalid payload. ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe Webhook Error: Invalid signature. ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object; // StripeCheckoutSession object
                $this->handleCheckoutSessionCompleted($session);
                break;
            case 'payment_intent.succeeded':
                // $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
                // This is often preferred as it's more direct about payment success
                // If using this, ensure your checkout session creates a payment_intent
                // $this->handlePaymentIntentSucceeded($paymentIntent);
                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentFailed($paymentIntent);
                break;
            // ... handle other event types
            default:
                Log::info('Received unhandled Stripe event type: ' . $event->type);
        }

        return response()->json(['status' => 'success']);
    }

    protected function handleCheckoutSessionCompleted(StripeCheckoutSession $session)
    {
        $listingPromotionId = $session->metadata->listing_promotion_id ?? null;

        if (!$listingPromotionId) {
            Log::error('Stripe Webhook: Missing listing_promotion_id in checkout session metadata.', ['session_id' => $session->id]);
            return;
        }

        $listingPromotion = ListingPromotion::find($listingPromotionId);

        if (!$listingPromotion) {
            Log::error('Stripe Webhook: ListingPromotion not found.', ['id' => $listingPromotionId, 'session_id' => $session->id]);
            return;
        }

        if ($listingPromotion->payment_status === 'paid') {
            Log::info('Stripe Webhook: Promotion already marked as paid.', ['id' => $listingPromotionId, 'session_id' => $session->id]);
            return;
        }

        if ($session->payment_status === 'paid') {
            $listingPromotion->payment_status = 'paid';
            $listingPromotion->payment_confirmed_at = now();
            $listingPromotion->transaction_id = $session->payment_intent ?? $session->id; // Store payment_intent if available
            $listingPromotion->save();

            app(ListingPromotionController::class)->activatePromotion($listingPromotion);

            Log::info('Stripe Webhook: Promotion activated successfully.', ['id' => $listingPromotionId, 'session_id' => $session->id]);
        } else {
            Log::warning('Stripe Webhook: Checkout session completed but payment status is not "paid".', [
                'id' => $listingPromotionId,
                'session_id' => $session->id,
                'payment_status' => $session->payment_status
            ]);
            if ($listingPromotion->payment_status !== 'paid') {
                $listingPromotion->payment_status = 'failed';
                $listingPromotion->save();
            }
        }
    }

    protected function handlePaymentIntentFailed(PaymentIntent $paymentIntent)
    {
        $listingPromotion = ListingPromotion::where('transaction_id', $paymentIntent->id)->first();

        // Or if you retrieve it via metadata from the payment intent (if set up during creation)
        // $listingPromotionId = $paymentIntent->metadata->listing_promotion_id ?? null;
        // $listingPromotion = ListingPromotion::find($listingPromotionId);

        if ($listingPromotion && $listingPromotion->payment_status !== 'paid') {
            $listingPromotion->payment_status = 'failed';
            $listingPromotion->admin_notes = 'Stripe payment failed: ' . ($paymentIntent->last_payment_error->message ?? 'Unknown error');
            $listingPromotion->save();
            Log::warning('Stripe Webhook: Payment intent failed.', ['id' => $listingPromotion->id, 'payment_intent_id' => $paymentIntent->id]);
        }
    }
}
