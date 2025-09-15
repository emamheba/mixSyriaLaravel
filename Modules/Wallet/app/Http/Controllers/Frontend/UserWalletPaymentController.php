<?php

namespace Modules\Wallet\app\Http\Controllers\Frontend;

use App\Helpers\FlashMsg;
use App\Helpers\PaymentGatewayCredential;
use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Models\Backend\Admin;
use App\Models\Backend\AdminNotification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Modules\Wallet\app\Models\Wallet;
use Modules\Wallet\app\Models\WalletHistory;
use Xgenious\Paymentgateway\Facades\XgPaymentGateway;

class UserWalletPaymentController extends Controller
{
    protected function cancel_page()
    {
        return redirect()->route('user.wallet.deposit.payment.cancel.static');
    }

    public function paypal_ipn_for_wallet()
    {
        $paypal = PaymentGatewayCredential::get_paypal_credential();
        $payment_data = $paypal->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function paytm_ipn_for_wallet()
    {
        $paytm = PaymentGatewayCredential::get_paytm_credential();
        $payment_data = $paytm->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function flutterwave_ipn_for_wallet()
    {
        $flutterwave = PaymentGatewayCredential::get_flutterwave_credential();
        $payment_data = $flutterwave->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function stripe_ipn_for_wallet()
    {
        $stripe = PaymentGatewayCredential::get_stripe_credential();
        $payment_data = $stripe->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function razorpay_ipn_for_wallet()
    {
        $razorpay = PaymentGatewayCredential::get_razorpay_credential();
        $payment_data = $razorpay->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function paystack_ipn_for_wallet()
    {
        $paystack = PaymentGatewayCredential::get_paystack_credential();
        $payment_data = $paystack->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function payfast_ipn_for_wallet()
    {
        $payfast = PaymentGatewayCredential::get_payfast_credential();
        $payment_data = $payfast->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function mollie_ipn_for_wallet()
    {
        $mollie = PaymentGatewayCredential::get_mollie_credential();
        $payment_data = $mollie->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function midtrans_ipn_for_wallet()
    {
        $midtrans = PaymentGatewayCredential::get_midtrans_credential();
        $payment_data = $midtrans->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function cashfree_ipn_for_wallet()
    {
        $cashfree = PaymentGatewayCredential::get_cashfree_credential();
        $payment_data = $cashfree->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function instamojo_ipn_for_wallet()
    {
        $instamojo = PaymentGatewayCredential::get_instamojo_credential();
        $payment_data = $instamojo->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function marcadopago_ipn_for_wallet()
    {
        $marcadopago = PaymentGatewayCredential::get_marcadopago_credential();
        $payment_data = $marcadopago->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function squareup_ipn_for_wallet()
    {
        $squareup = PaymentGatewayCredential::get_squareup_credential();
        $payment_data = $squareup->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function cinetpay_ipn_for_wallet()
    {
        $cinetpay = PaymentGatewayCredential::get_cinetpay_credential();
        $payment_data = $cinetpay->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function paytabs_ipn_for_wallet()
    {
        $paytabs = PaymentGatewayCredential::get_paytabs_credential();
        $payment_data = $paytabs->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function billplz_ipn_for_wallet()
    {
        $billplz = PaymentGatewayCredential::get_billplz_credential();
        $payment_data = $billplz->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function zitopay_ipn_for_wallet()
    {
        $zitopay = PaymentGatewayCredential::get_zitopay_credential();
        $payment_data = $zitopay->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function toyyibpay_ipn_for_wallet()
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
            $this->update_database($order_id, $payment_data['transaction_id'],$history_id);
            $this->send_jobs_mail($order_id,$user_id);
            $new_order_id =  $order_id;

            return redirect()->route('user.wallet.history')->with(toastr_success(__('Your wallet successfully credited')));
        }

        return $this->cancel_page();
    }

    public function paystack_common_ipn_data($data)
    {
        return $this->common_ipn_data($data);
    }

    public function send_jobs_mail($last_deposit_id, $user_id)
    {
        if(empty($last_deposit_id)){
            return redirect()->route('homepage');
        }
        $user = User::select(['id','first_name','last_name','email'])->where('id',$user_id)->first();
        try {
            //Send deposit email to User
            $subject = get_static_option('user_deposit_to_wallet_subject') ?? __('Deposit Amount');
            $message = get_static_option('user_deposit_to_wallet_message') ?? __('Your deposit amount successfully credited to your wallet.');
            $message = str_replace(["@name","@deposit_id"],[$user->first_name.' '.$user->last_name, $last_deposit_id], $message);
            Mail::to($user->email)->send(new BasicMail([
                'subject' => $subject,
                'message' => $message
            ]));

            //Send deposit email to admin
            $subject = get_static_option('user_deposit_to_wallet_subject') ?? __('Deposit Amount');
            $message = get_static_option('user_deposit_to_wallet_message_admin') ?? __('A user deposit to his wallet.');
            $message = str_replace(["@name","@deposit_id"],[$user->first_name.' '.$user->last_name, $last_deposit_id], $message);
            Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                'subject' => $subject,
                'message' => $message
            ]));

        } catch (\Exception $e) {

        }
    }

    private function update_database($last_deposit_id, $transaction_id)
    {
        DB::beginTransaction();
        try {
            $deposit_details = WalletHistory::find($last_deposit_id);
            if (!$deposit_details) {
                throw new \Exception(__('Deposit details not found.'));
            }

            $wallet = Wallet::where('user_id', $deposit_details->user_id)->lockForUpdate()->first();
            if (!$wallet) {
                throw new \Exception(__('Wallet not found.'));
            }

            // Update WalletHistory
            WalletHistory::where('id', $last_deposit_id)->update([
                'payment_status' => 'complete',
                'transaction_id' => $transaction_id,
                'status' => 1,
            ]);

            // Update Wallet balance
            $wallet->balance += $deposit_details->amount;
            $wallet->save();

            // Create AdminNotification
            AdminNotification::create([
                'identity' => $last_deposit_id,
                'user_id' => $deposit_details->user_id,
                'type' => __('Deposit Amount'),
                'message' => __('User wallet deposit'),
            ]);

            DB::commit();
            return redirect()->route('user.wallet.history')->with(FlashMsg::item_new('success', 'Deposit successfully completed.'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->route('user.wallet.history')->with(FlashMsg::error('danger', $exception->getMessage()));
        }
    }

}
