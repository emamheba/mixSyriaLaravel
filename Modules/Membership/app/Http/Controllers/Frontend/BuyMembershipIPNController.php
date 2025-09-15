<?php

namespace Modules\Membership\app\Http\Controllers\Frontend;

use App\Helpers\PaymentGatewayCredential;
use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Models\Backend\AdminNotification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Membership\app\Models\MembershipHistory;
use Modules\Membership\app\Models\UserMembership;
use Xgenious\Paymentgateway\Facades\XgPaymentGateway;

class BuyMembershipIPNController extends Controller
{
    protected function cancel_page()
    {
        return redirect()->route('membership.buy.payment.cancel.static');
    }

    public function paypal_ipn_for_membership()
    {
        $paypal = PaymentGatewayCredential::get_paypal_credential();
        $payment_data = $paypal->ipn_response();
        return $this->common_ipn_data($payment_data);
    }


    public function paytm_ipn_for_membership()
    {
        $paytm = PaymentGatewayCredential::get_paytm_credential();
        $payment_data = $paytm->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function flutterwave_ipn_for_membership()
    {
        $flutterwave = PaymentGatewayCredential::get_flutterwave_credential();
        $payment_data = $flutterwave->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function stripe_ipn_for_membership()
    {
        $stripe = PaymentGatewayCredential::get_stripe_credential();
        $payment_data = $stripe->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function razorpay_ipn_for_membership()
    {
        $razorpay = PaymentGatewayCredential::get_razorpay_credential();
        $payment_data = $razorpay->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function paystack_ipn_for_membership()
    {
        $paystack = PaymentGatewayCredential::get_paystack_credential();
        $payment_data = $paystack->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function payfast_ipn_for_membership()
    {
        $payfast = PaymentGatewayCredential::get_payfast_credential();
        $payment_data = $payfast->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function mollie_ipn_for_membership()
    {
        $mollie = PaymentGatewayCredential::get_mollie_credential();
        $payment_data = $mollie->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function midtrans_ipn_for_membership()
    {
        $midtrans = PaymentGatewayCredential::get_midtrans_credential();
        $payment_data = $midtrans->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function cashfree_ipn_for_membership()
    {
        $cashfree = PaymentGatewayCredential::get_cashfree_credential();
        $payment_data = $cashfree->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function instamojo_ipn_for_membership()
    {
        $instamojo = PaymentGatewayCredential::get_instamojo_credential();
        $payment_data = $instamojo->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function marcadopago_ipn_for_membership()
    {
        $marcadopago = PaymentGatewayCredential::get_marcadopago_credential();
        $payment_data = $marcadopago->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function squareup_ipn_for_membership()
    {
        $squareup = PaymentGatewayCredential::get_squareup_credential();
        $payment_data = $squareup->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function cinetpay_ipn_for_membership()
    {
        $cinetpay = PaymentGatewayCredential::get_cinetpay_credential();
        $payment_data = $cinetpay->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function paytabs_ipn_for_membership()
    {
        $paytabs = PaymentGatewayCredential::get_paytabs_credential();
        $payment_data = $paytabs->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function billplz_ipn_for_membership()
    {
        $billplz = PaymentGatewayCredential::get_billplz_credential();
        $payment_data = $billplz->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function zitopay_ipn_for_membership()
    {
        $zitopay = PaymentGatewayCredential::get_zitopay_credential();
        $payment_data = $zitopay->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function toyyibpay_ipn_for_membership()
    {
        $toyyibpay = PaymentGatewayCredential::get_toyyibpay_credential();
        $payment_data = $toyyibpay->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    private function common_ipn_data($payment_data)
    {
        if (isset($payment_data['status']) && $payment_data['status'] === 'complete'){
            $order_id = $payment_data['order_id'];
            $user_id = session()->get('user_id');
            $membership_history_id = session()->get('membership_history_id');
            $upgrade_membership_id = session()->get('upgrade_membership_id');
            $this->update_database($order_id, $payment_data['transaction_id'], $membership_history_id, $upgrade_membership_id);
            $this->send_jobs_mail($order_id,$user_id);

           toastr_success(__('Membership purchase success'));
            return redirect()->route('user.membership.all');
        }

        return $this->cancel_page();
    }

    public function paystack_common_ipn_data($data)
    {
        return $this->common_ipn_data($data);
    }

    public function send_jobs_mail($last_membership_id,$user_id)
    {
        if(empty($last_membership_id)){ return redirect()->route('homepage');}
        $user = User::select(['id','first_name','last_name','email'])->where('id',$user_id)->first();
        $name = $user->first_name. ' ' .$user->last_name;
        $email = $user->email;

        $membership = UserMembership::find($last_membership_id);
        $membership_type = $membership->membership?->membership_type?->type;
        $membership_price = float_amount_with_currency_symbol($membership->price);
        $membership_expire_date = isset($membership->expire_date) ? Carbon::parse($membership->expire_date)->toFormattedDateString() : '';

        try {
            //Send membership email to user
            $subject = get_static_option('user_membership_purchase_email_subject') ?? __('Membership purchase email');
            $message = get_static_option('user_membership_purchase_message') ?? __('Your membership purchase successfully completed.');
            $message = str_replace(["@membership_id", "@membership_type", "@membership_price", "@membership_expire_date"], [$last_membership_id, $membership_type, $membership_price, $membership_expire_date], $message);
            Mail::to($user->email)->send(new BasicMail([
                'subject' => $subject,
                'message' => $message
            ]));

            //Send membership email to admin
            $subject = get_static_option('user_membership_purchase_email_subject') ?? __('Membership purchase email');
            $message = get_static_option('user_membership_purchase_message_for_admin') ?? __('A user just purchase a membership.');
            $message = str_replace(["@membership_id", "@membership_type", "@membership_price", "@membership_expire_date","@name","@email"],[$last_membership_id, $membership_type, $membership_price, $membership_expire_date, $name,$email], $message);
            Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                'subject' => $subject,
                'message' => $message
            ]));

        } catch (\Exception $e) {
            Log::error('Failed to clear session: ' . $e->getMessage());
        }
    }

    private function update_database($last_membership_id, $transaction_id, $membership_history_id, $upgrade_membership_id)
    {
        try {
            // Cast IDs to integers
            $last_membership_id = (int) $last_membership_id;
            $membership_history_id = (int) $membership_history_id;

              // Retrieve membership details
              $membership_details = UserMembership::find($last_membership_id);
              $membership_history = MembershipHistory::find($membership_history_id);

            $check_user_current_membership = UserMembership::where('id', $last_membership_id)
                ->where('user_id', $membership_details->user_id)
                ->where('payment_status', 'complete')
                ->where('status', 1)
                ->where('expire_date', '>', Carbon::now())
                ->exists();

            if (!$membership_details) {
                throw new \Exception("Membership not found for ID: $last_membership_id");
            }

             UserMembership::where('id', $last_membership_id)->where('user_id',$membership_details->user_id)
                ->update([
                    'payment_status' => 'complete',
                    'status' => 1,
                    'transaction_id' => $transaction_id,
                ]);

            MembershipHistory::where('id', $membership_history_id)->where('user_id',$membership_details->user_id)
                ->update([
                    'payment_status' => 'complete',
                    'status' => 1,
                    'transaction_id' => $transaction_id,
                ]);



            // if user current membership upgrade
            if ($membership_history_id && !empty($membership_history) && $check_user_current_membership) {
                // initialized value for if exits user current membership
                $user_current_listing_limit = 0;
                $user_current_gallery_images = 0;
                $user_current_featured_listing = 0;
                $user_current_enquiry_form = 0;
                $user_current_business_hour = 0;
                $user_current_membership_badge = 0;

                // Check if the membership has not expired
                if ($membership_details->expire_date > now()) {
                    $user_current_listing_limit = $membership_details->listing_limit;
                    $user_current_gallery_images = $membership_details->gallery_images;
                    $user_current_featured_listing = $membership_details->featured_listing;
                    $user_current_enquiry_form = $membership_details->enquiry_form;
                    $user_current_business_hour = $membership_details->business_hour;
                    $user_current_membership_badge = $membership_details->membership_badge;
                }

                // Parse existing expire dates as Carbon instances
                $current_expire_date = Carbon::parse($membership_details->expire_date);
                $new_history_expire_date = Carbon::parse($membership_history->expire_date);
                $current_days_to_expire = $current_expire_date->diffInDays(Carbon::now());
                $new_days_to_expire = $new_history_expire_date->diffInDays(Carbon::now());
                $expire_date = Carbon::now()->addDays($current_days_to_expire + $new_days_to_expire);

                // other info
                $payment_gateway_name = $membership_history->payment_gateway;
                $membership_history_price = $membership_history->price;

                // Calculate the new limits and features by adding the current user's limits and features
                $listing_limit = $membership_history->listing_limit + $user_current_listing_limit;
                $gallery_images = $membership_history->gallery_images + $user_current_gallery_images;
                $featured_listing = $membership_history->featured_listing + $user_current_featured_listing;

                $enquiry_form = ($membership_history->enquiry_form || $user_current_enquiry_form) ? 1 : 0;
                $business_hour = ($membership_history->business_hour || $user_current_business_hour) ? 1 : 0;
                $membership_badge = ($membership_history->membership_badge || $user_current_membership_badge) ? 1 : 0;

                // for upgrade membership add initial limit
                $initial_listing_limit = $membership_history->listing_limit;
                $initial_gallery_images = $membership_history->gallery_images;
                $initial_featured_listing = $membership_history->featured_listing;
                $initial_enquiry_form = $membership_history->enquiry_form;
                $initial_business_hour = $membership_history->business_hour;
                $initial_membership_badge = $membership_history->membership_badge;


                 $membership_history_updated = MembershipHistory::where('id', $membership_history_id)->where('user_id',$membership_details->user_id)
                    ->update([
                        'payment_status' => 'complete',
                        'payment_gateway' => $payment_gateway_name,
                        'status' => 1,
                        'transaction_id' => $transaction_id,
                        'price' => $membership_history_price,
                        'expire_date' => $expire_date,
                    ]);


                if ($membership_history_updated){
                    UserMembership::where('id', $last_membership_id)->where('user_id', $membership_details->user_id)->update([
                        'membership_id' => $upgrade_membership_id,
                        'payment_status' => 'complete',
                        'payment_gateway' => $payment_gateway_name,
                        'status' => 1,
                        'transaction_id' => $transaction_id,
                        'price' => $membership_history_price,
                        'expire_date' => $expire_date,
                        // limit info
                        'listing_limit' => $listing_limit,
                        'gallery_images' => $gallery_images,
                        'featured_listing' => $featured_listing,
                        'enquiry_form' => $enquiry_form,
                        'business_hour' => $business_hour,
                        'membership_badge' => $membership_badge,

                        // initial info
                        'initial_listing_limit' => $initial_listing_limit,
                        'initial_gallery_images' => $initial_gallery_images,
                        'initial_featured_listing' => $initial_featured_listing,
                        'initial_enquiry_form' => $initial_enquiry_form,
                        'initial_business_hour' => $initial_business_hour,
                        'initial_membership_badge' => $initial_membership_badge,
                        ]);
                }
            }

            AdminNotification::create([
                'identity'=>$last_membership_id,
                'user_id'=>$membership_details->user_id,
                'type'=>__('Buy Membership'),
                'message'=>__('User membership purchase'),
            ]);

            // Clear session data
            session()->forget('order_id');
            session()->forget('user_id');
            session()->forget('membership_history_id');
            session()->forget('upgrade_membership_id');

        } catch (\Exception $e) {
            Log::error('Failed to clear session: ' . $e->getMessage());
        }

    }
}
