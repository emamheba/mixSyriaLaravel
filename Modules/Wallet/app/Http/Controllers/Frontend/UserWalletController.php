<?php

namespace Modules\Wallet\app\Http\Controllers\Frontend;

use App\Helpers\PaymentGatewayCredential;
use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;
use Modules\Wallet\app\Models\Wallet;
use Modules\Wallet\app\Models\WalletHistory;
use Xgenious\Paymentgateway\Facades\XgPaymentGateway;

class UserWalletController extends Controller
{
    private const CANCEL_ROUTE = 'user.wallet.deposit.payment.cancel.static';
    private float $total;
    private string $title;
    private string $description;
    private int $last_deposit_id;

    public function deposit_payment_cancel_static()
    {
        return view('wallet::frontend.user.payment-cancel-static');
    }
    public function deposit_payment_success()
    {
        return back()->with(['type' => 'success', __('Your Deposit is Successful')]);
    }

    // pagination
    function pagination(Request $request)
    {
        if($request->ajax()){
            $user_id = Auth::guard('web')->user()->id;
            $user_wallet_histories = WalletHistory::where('user_id',$user_id)->latest()->paginate(10);
            return view('wallet::user.wallet.search-result', compact('user_wallet_histories'))->render();
        }
    }

    // search history
    public function search_history(Request $request)
    {
        $user_wallet_histories = WalletHistory::where('user_id',Auth::guard('web')->user()->id)->where('created_at', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
        return $user_wallet_histories->total() >= 1
            ? view('wallet::user.wallet.search-result', compact('user_wallet_histories'))->render()
            : response()->json(['status'=>__('nothing')]);
    }

    public function wallet_history(Request $request)
    {
        $user_id = Auth::guard('web')->user()->id;
        $user_wallet_histories = WalletHistory::where('user_id',$user_id)->latest()->paginate(10);
        $wallet_balance = Wallet::where('user_id',$user_id)->first();
        $total_wallet_balance = $wallet_balance->balance ?? 0;
        return view('wallet::user.wallet.wallet-history',compact(['user_wallet_histories','total_wallet_balance']));
    }

    public function deposit(Request $request)
    {
        $limit_deposit_amount = get_static_option('deposit_amount_limitation_for_user') ?? 50000;
        $request->validate([
            'amount' => 'required|integer|min:10|max:"'.$limit_deposit_amount.'"',
            'selected_payment_gateway' => 'required',
        ]);

        if ($request->selected_payment_gateway === 'manual_payment') {
            $request->validate([
                'trasaction_attachment' => 'required|mimes:jpg,jpeg,png,svg,pdf,webp'
            ],
                [
                    'The manual payment image must be a file of type - jpg, jpeg, png, webp, svg and pdf.'
                ]);

            $file_extention = $request->trasaction_attachment->getClientOriginalExtension();
            $imageTypes = ['jpeg', 'png', 'jpg', 'svg', 'pdf', 'webp'];
            if (!in_array($file_extention, $imageTypes)) {
                return back()->withErrors('Please insert a valid image attachment');
            }
        }

        //deposit amount
        $this->total = $request->amount;
        $user = Auth::guard('web')->user();
        $user_id = $user->id;
        $name = $user->name;
        $email = $user->email;
        if ($request->selected_payment_gateway == 'manual_payment') {
            $payment_status = 'pending';
        } else {
            $payment_status = '';
        }

        $buyer = Wallet::where('user_id', $user_id)->first();
        if (empty($buyer)) {
            Wallet::create([
                'user_id' => $user_id,
                'balance' => 0,
                'status' => 0,
            ]);
        }

        $deposit = WalletHistory::create([
            'user_id' => $user_id,
            'amount' => $this->total,
            'payment_gateway' => $request->selected_payment_gateway,
            'payment_status' => $payment_status,
            'status' => 1,
        ]);

        $this->last_deposit_id = $deposit->id;
        $this->title = __('Deposit To Wallet');
        $this->description = sprintf(__('Order id #%1$d Email: %2$s, Name: %3$s'), $this->last_deposit_id, $email, $name);

        $payment_gateway = $request->selected_payment_gateway;
        if ($request->selected_payment_gateway === 'manual_payment') {
            if ($request->hasFile('trasaction_attachment')) {
                $manual_payment_image = $request->trasaction_attachment;
                $img_ext = $manual_payment_image->extension();

                $manual_payment_image_name = 'manual_attachment_' . time() . '.' . $img_ext;
                if (in_array($img_ext, ['jpg', 'jpeg', 'png', 'svg', 'pdf','webp'])) {
                    $manual_image_path = 'assets/uploads/deposit_payment_attachments/';

                    // Image scan start
                    $uploaded_file = $manual_payment_image;
                    $file_extension = $uploaded_file->getClientOriginalExtension();
                    if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $processed_image = Image::make($uploaded_file);
                        $image_default_width = $processed_image->width();
                        $image_default_height = $processed_image->height();
                        $processed_image->resize($image_default_width, $image_default_height, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $processed_image->save($manual_image_path . $manual_payment_image_name);
                    }else{
                        $manual_payment_image->move($manual_image_path, $manual_payment_image_name);
                    } // Image scan end

                    WalletHistory::where('id', $this->last_deposit_id)->update([
                        'manual_payment_image' => $manual_payment_image_name
                    ]);
                } else {
                    return back()->with(['msg' => __('image type not supported'), 'type' => 'danger']);
                }
            }


            // sent email
            try {
                //Send deposit email to User
                $subject = get_static_option('user_deposit_to_wallet_subject') ?? __('Deposit Amount');
                $message = __('Manual deposit success. Your wallet will credited after admin approval #') . $this->last_deposit_id. ' '. __('Deposit Confirmation');
                $message = str_replace(["@name","@deposit_id"],[$user->first_name.' '.$user->last_name, $this->last_deposit_id], $message);
                Mail::to($user->email)->send(new BasicMail([
                    'subject' => $subject,
                    'message' => $message
                ]));

                //Send deposit email to admin
                $subject = get_static_option('user_deposit_to_wallet_subject') ?? __('Deposit Amount');
                $message = __('Hello a buyer just deposit to his wallet. Please check and confirm') . '</br>' . '<span class="verify-code">' . __('Deposit ID: ') . $this->last_deposit_id . '</span>';
                $message = str_replace(["@name","@deposit_id"],[$user->first_name.' '.$user->last_name, $this->last_deposit_id], $message);
                Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                    'subject' => $subject,
                    'message' => $message
                ]));
            } catch (\Exception $e) {

            }

            return back()->with(toastr_success(__('Manual deposit success. Your wallet will credited after admin approval')));

        } else {
            $credential_function = 'get_' . $payment_gateway . '_credential';
            if (!method_exists((new PaymentGatewayCredential()), $credential_function)){
                $custom_data['request'] = $request->all();
                $custom_data['payment_details'] = $deposit->toArray();
                $custom_data['total'] = $this->total;
                $custom_data['payment_type'] = "deposit";
                $custom_data['payment_for'] = "user";
                $custom_data['cancel_url'] = route(self::CANCEL_ROUTE, random_int(111111,999999).$custom_data['payment_details']['id'].random_int(111111,999999));
                $custom_data['success_url'] = route('user.wallet.history');

                $charge_customer_class_namespace = getChargeCustomerMethodNameByPaymentGatewayNameSpace($payment_gateway);
                $charge_customer_method_name = getChargeCustomerMethodNameByPaymentGatewayName($payment_gateway);

                $custom_charge_customer_class_object = new $charge_customer_class_namespace;
                if(class_exists($charge_customer_class_namespace) && method_exists($custom_charge_customer_class_object, $charge_customer_method_name))
                {
                    return $custom_charge_customer_class_object->$charge_customer_method_name($custom_data);
                } else {
                    return back()->with(toastr_success(__('Incorrect Class or Method')));
                }
            } else {
                return $this->payment_with_gateway($request->selected_payment_gateway);
            }
        }
    }

    public function payment_with_gateway($payment_gateway_name)
    {
        try {
            $gateway_function = 'get_' . $payment_gateway_name . '_credential';
            $gateway = PaymentGatewayCredential::$gateway_function();

            $redirect_url = $gateway->charge_customer(
                $this->common_charge_customer_data($payment_gateway_name)
            );

            session()->put('order_id', $this->last_deposit_id);

            return $redirect_url;
        } catch (\Exception $e) {
            return back()->with(['msg' => $e->getMessage(), 'type' => 'danger']);
        }
    }

    public function common_charge_customer_data($payment_gateway_name)
    {
        $user = Auth::guard('web')->user();
        $email = $user->email;
        $name = $user->fullname;

        session()->put('user_id', $user->id);

        if ($payment_gateway_name === 'paystack')
        {
            $ipn_route = route('user.' . strtolower($payment_gateway_name) . '.ipn.wallet');
        } else {
            $ipn_route = route('user.' . strtolower($payment_gateway_name) . '.ipn.wallet');
        }

        return [
            'amount' => $this->total,
            'title' => $this->title,
            'description' => $this->description,
            'ipn_url' => $ipn_route,
            'order_id' => $this->last_deposit_id,
            'track' => \Str::random(36),
            'cancel_url' => route(self::CANCEL_ROUTE, $this->last_deposit_id),
            'success_url' => route('user.wallet.deposit.payment.success'),
            'email' => $email,
            'name' => $name,
            'payment_type' => 'deposit',
        ];
    }


}
