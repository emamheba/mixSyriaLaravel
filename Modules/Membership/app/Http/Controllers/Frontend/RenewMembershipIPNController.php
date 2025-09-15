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
use Illuminate\Support\Facades\Mail;
use Modules\Membership\app\Models\MembershipHistory;
use Modules\Membership\app\Models\UserMembership;

class RenewMembershipIPNController extends Controller
{
    protected function cancel_page()
    {
        return redirect()->route('membership.renew.payment.cancel.static');
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
            $history_id = session()->get('history_id');
            $user_id = session()->get('user_id');
            $this->update_database($order_id, $history_id, $payment_data['transaction_id']);
            $this->send_jobs_mail($order_id,$user_id);

            toastr_success(__('Membership Renew success'));
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


        //Send subscription email to user
        try {
            $subject = get_static_option('user_membership_renew_email_subject') ?? __('Membership renew email.');
            $message = get_static_option('user_membership_renew_message') ?? __('Your membership purchase successfully completed.');
            $message = str_replace(["@name","@membership_id"],[$user->first_name.' '.$user->last_name, $last_membership_id], $message);
            Mail::to($user->email)->send(new BasicMail([
                'subject' => $subject,
                'message' => $message
            ]));
        } catch (\Exception $e) {}

        //Send subscription email to admin
        try {
            $subject = get_static_option('user_membership_renew_email_subject') ?? __('Membership renew email.');
            $message = get_static_option('user_membership_renew_message_for_admin') ?? __('A user just renew a membership.');
            $message = str_replace(["@name","@membership_id"],[$user->first_name.' '.$user->last_name, $last_membership_id], $message);
            Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                'subject' => $subject,
                'message' => $message
            ]));
        } catch (\Exception $e) {}
    }
    private function update_database($last_membership_id, $history_id, $transaction_id)
    {
        $membership_details = UserMembership::find($last_membership_id);
        $membership_history = MembershipHistory::find($history_id);

        // Parse existing expire dates as Carbon instances
        $current_expire_date = Carbon::parse($membership_details->expire_date);
        $new_history_expire_date = Carbon::parse($membership_history->expire_date);
        $current_days_to_expire = $current_expire_date->diffInDays(Carbon::now());
        $new_days_to_expire = $new_history_expire_date->diffInDays(Carbon::now());
        $expire_date = Carbon::now()->addDays($current_days_to_expire + $new_days_to_expire);

        UserMembership::where('id', $last_membership_id)->where('user_id',$membership_details->user_id)
            ->update([
                'payment_status' => 'complete',
                'transaction_id' => $transaction_id,
                'expire_date' => $expire_date,
                'status' => 1,
                'price' => $membership_history->price,
                // limit info
                'listing_limit' => $membership_history->listing_limit,
                'gallery_images' => $membership_history->gallery_images,
                'featured_listing' => $membership_history->featured_listing,
                'enquiry_form' => $membership_history->enquiry_form,
                'business_hour' => $membership_history->business_hour,
                'membership_badge' => $membership_history->membership_badge,
            ]);

        if (!empty($membership_history)){
            MembershipHistory::where('id', $history_id)->update([
                'payment_status' => 'complete',
                'transaction_id' => $transaction_id,
                'expire_date' => $expire_date,
                'status' => 1,
            ]);
        }

        AdminNotification::create([
            'identity'=>$last_membership_id,
            'user_id'=>$membership_details->user_id,
            'type'=>__('Renew Membership'),
            'message'=>__('User membership renew'),
        ]);
    }
}
