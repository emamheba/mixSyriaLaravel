<?php

namespace Modules\Membership\app\Http\Controllers\Frontend;

use App\Helpers\PaymentGatewayCredential;
use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Models\Backend\AdminNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Modules\Membership\app\Models\Membership;
use Modules\Membership\app\Models\MembershipHistory;
use Modules\Membership\app\Models\UserMembership;
use Modules\Wallet\app\Models\Wallet;

class RenewMembershipController extends Controller
{
    private float $total;
    private string $title;
    private string $description;
    private int $last_membership_id;
    private const CANCEL_ROUTE = 'membership.buy.payment.cancel.static';
    public function renew_membership_payment_cancel_static()
    {
        return view('membership::frontend.membership.renew-cancel');
    }

    public function renew_membership(Request $request)
    {

        $request->validate([
            'selected_payment_gateway' => 'required',
        ]);

        // find membership
        $membership_details = Membership::with('membership_type:id,validity')
            ->where('id',$request->membership_id)
            ->where('status','1')->first();

        if (!isset($membership_details)) {
            toastr_warning(__('Membership not found'));
            return back();
        }
        // find user membership
        $user = Auth::guard('web')->user();
        $user_membership = UserMembership::where('membership_id', $membership_details->id)->where('user_id', $user->id)->first();
        if (!isset($user_membership)) {
            toastr_warning(__('Membership not found'));
            return back();
        }

        // check free plan
        if ($user_membership->price == 0) {
            toastr_warning(__('You have already received the free plan. Free plans are not eligible for renewal.'));
            return back();
        }

        // Check if membership exists and expire date is within the specified renewal days
        if (!empty($user_membership)) {
            $expireDate = Carbon::parse($user_membership->expire_date);
            $currentDate = now();
            if($expireDate <= $currentDate) {
                toastr_warning(__('Your Membership Has Expired'));
                return back();
            }
        } else {
            toastr_warning(__('Membership Not Found'));
            return back();
        }

        // update start
        $expire_date = Carbon::now()->addDays($user_membership?->membership_type?->validity);
        // if user membership exists and not expired
        if(!empty($user_membership) && $user_membership->expire_date > Carbon::now()) {
            $current_expire_date = Carbon::parse($user_membership->expire_date);
            $expire_date = $current_expire_date->addDays($membership_details->membership_type->validity ?? 0);

            // for manual payment admin approve then update expire date other wise basic date add
            $expire_date_for_manual_payment = Carbon::now()->addDays($membership_details?->membership_type?->validity);
            $expire_date_for_gateway_pay = Carbon::now()->addDays($membership_details?->membership_type?->validity);
        }

        $this->title = __('Renew Membership');
        $this->total = $user_membership->price;
        $listing_limit = $user_membership->listing_limit;
        $gallery_images = $user_membership->gallery_images;
        $featured_listing = $user_membership->featured_listing;

        $enquiry_form = $user_membership->enquiry_form ? 1 : 0;
        $business_hour = $user_membership->business_hour ? 1 : 0;
        $membership_badge = $user_membership->membership_badge ? 1 : 0;

        $name = $user->first_name.' '.$user->last_name;
        $email = $user->email;
        $payment_gateway = $request->selected_payment_gateway;
        $payment_status = $request->selected_payment_gateway === 'wallet' ? 'complete' : 'pending';
        $status = $request->selected_payment_gateway === 'wallet' ? 1 : 0;
        session()->put('user_id',$user->id);

        //check payment gateway is wallet or not
        if (moduleExists("Wallet")){
            if ($request->selected_payment_gateway === 'wallet') {
                $wallet_balance = Wallet::select('balance')->where('user_id', $user->id)->first();

                //check wallet has or not
                if(is_null($wallet_balance)){
                    toastr_warning(__('wallet not enabled. make your initial deposit to enable your wallet'));
                    return back();
                }

                // membership update
                if ($wallet_balance->balance >= $user_membership->price) {
                    // Renew membership update
                    $user_membership->update([
                        'price' => $this->total,
                        'payment_status' => $payment_status,
                        'payment_gateway' => $payment_gateway,
                        'expire_date' => $expire_date,
                        'listing_limit' => ($membership_details->listing_limit + $listing_limit),
                        'gallery_images' => ($membership_details->gallery_images + $gallery_images),
                        'featured_listing' => ($membership_details->featured_listing + $featured_listing),
                        'enquiry_form' => $enquiry_form,
                        'business_hour' => $business_hour,
                        'membership_badge' => $membership_badge,
                        'status' => $status,
                    ]);

                    // membership history create
                    if (!empty($user_membership)){
                        MembershipHistory::create([
                            'membership_id' => $user_membership->membership_id,
                            'user_id' => $user_membership->user_id,
                            'payment_status' => $user_membership->payment_status,
                            'payment_gateway' => $user_membership->payment_gateway,
                            'expire_date' => $user_membership->expire_date,
                            'listing_limit' => $user_membership->listing_limit,
                            'gallery_images' => $user_membership->gallery_images,
                            'featured_listing' => $user_membership->featured_listing,
                            'enquiry_form' => $user_membership->enquiry_form,
                            'business_hour' => $user_membership->business_hour,
                            'membership_badge' => $user_membership->membership_badge,
                            'price' =>$this->total,
                            'status' => $status,
                        ]);
                    }

                     // update wallet balance
                        Wallet::where('user_id', $user->id)->update([
                            'balance' => $wallet_balance->balance - $this->total,
                        ]);

                    // send email
                    $this->last_membership_id = $user_membership->id;
                    $this->sendEmail($name,$this->last_membership_id,$email);
                    $this->adminNotification($this->last_membership_id,$user->id);
                    toastr_success('Your membership renewed successfully');
                    return back();
                } else {
                    toastr_warning(__('Your wallet balance is not sufficient to renew this membership'));
                }
                return back();
            }
        }

        // if Manual payment and other payment gateway
        if($request->selected_payment_gateway === 'manual_payment') {
            $request->validate([
                'trasaction_attachment' => 'required|mimes:jpg,jpeg,png,pdf,webp'
            ]);

            if ($request->hasFile('trasaction_attachment')) {
                $manual_payment_image = $request->trasaction_attachment;
                $img_ext = $manual_payment_image->extension();
                $manual_payment_image_name = 'trasaction_attachment_' . time() . '.' . $img_ext;

                if (in_array($img_ext, ['jpg', 'jpeg', 'png', 'pdf', 'webp'])) {
                    $manual_image_path = 'assets/uploads/manual-payment/membership';

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

                    // membership history create
                    MembershipHistory::create([
                        'membership_id' => $user_membership->membership_id,
                        'user_id' => $user_membership->user_id,
                        'payment_status' => $payment_status,
                        'payment_gateway' => $request->selected_payment_gateway,
                        'transaction_id' => $request->trasaction_id,
                        'manual_payment_image' => $manual_payment_image_name,
                        'expire_date' => $expire_date_for_manual_payment,
                        'listing_limit' => $membership_details->listing_limit,
                        'gallery_images' => $membership_details->gallery_images,
                        'featured_listing' => $membership_details->featured_listing,
                        'enquiry_form' => $membership_details->enquiry_form,
                        'business_hour' => $membership_details->business_hour,
                        'membership_badge' => $membership_details->membership_badge,
                        'price' => $this->total,
                        'status' => $status,
                    ]);

                    $this->last_membership_id = $user_membership->id;
                    $this->sendEmail($name, $this->last_membership_id, $email);
                    $this->adminNotification($this->last_membership_id, $user->id);
                    return redirect()->route('user.membership.all')->with(toastr_warning(__('Membership purchase success. Your membership will be usable after admin approval')));
                } else {
                    return back()->with(toastr_warning(__('Image type not supported')));
                }
            }
        }else{
            // for payment gateway
            // membership history create
            if (!empty($user_membership)) {

                $create_history =  MembershipHistory::create([
                    'membership_id' => $user_membership->membership_id,
                    'user_id' => $user_membership->user_id,
                    'payment_status' => '',
                    'payment_gateway' => $request->selected_payment_gateway,
                    'expire_date' => $expire_date_for_gateway_pay,
                    'listing_limit' => $user_membership->listing_limit + $user_membership->initial_listing_limit,
                    'gallery_images' => $user_membership->gallery_images + $user_membership->initial_gallery_images,
                    'featured_listing' => $user_membership->featured_listing + $user_membership->initial_featured_listing,
                    'enquiry_form' => $user_membership->initial_enquiry_form,
                    'business_hour' => $user_membership->initial_business_hour,
                    'membership_badge' => $user_membership->initial_membership_badge,
                    'price' => $this->total,
                    'status' => $status,
                ]);
            }

            $last_membership_history = $create_history->id;
            $this->last_membership_id = $user_membership->id;

            session()->put('history_id',$last_membership_history);

            $this->description = sprintf(__('Order id #%1$d Email: %2$s, Name: %3$s'),$this->last_membership_id,$email,$name);

            // all payment gateway check start
            $payment_gateway = $request->selected_payment_gateway;
            $credential_function = 'get_' . $payment_gateway . '_credential';

            if (!method_exists((new PaymentGatewayCredential()), $credential_function))
            {
                $custom_data['request'] = $request->all();
                $custom_data['payment_details'] = $user_membership->toArray();
                $custom_data['total'] = $this->total;
                $custom_data['payment_type'] = "deposit";
                $custom_data['payment_for'] = "membership";
                $custom_data['cancel_url'] = route(self::CANCEL_ROUTE, random_int(111111,999999).$custom_data['payment_details']['id'].random_int(111111,999999));
                $custom_data['success_url'] = route('user.membership.all');

                $charge_customer_class_namespace = getChargeCustomerMethodNameByPaymentGatewayNameSpace($payment_gateway);
                $charge_customer_method_name = getChargeCustomerMethodNameByPaymentGatewayName($payment_gateway);

                $custom_charge_customer_class_object = new $charge_customer_class_namespace;
                if(class_exists($charge_customer_class_namespace) && method_exists($custom_charge_customer_class_object, $charge_customer_method_name)){
                    return $custom_charge_customer_class_object->$charge_customer_method_name($custom_data);
                } else {
                    return back()->with(toastr_error('Incorrect Class or Method'));
                }
            } else {
                return $this->payment_with_gateway($request->selected_payment_gateway);
            }
        }
        toastr_warning(__('not found membership'));
        return back();
    }

    public function payment_with_gateway($payment_gateway_name)
    {
        try {
            $gateway_function = 'get_' . $payment_gateway_name . '_credential';
            $gateway = PaymentGatewayCredential::$gateway_function();

            $redirect_url = $gateway->charge_customer(
                $this->common_charge_customer_data($payment_gateway_name)
            );
            session()->put('order_id', $this->last_membership_id);
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

        if ($payment_gateway_name === 'paystack'){
            $ipn_route = route('user.' . strtolower($payment_gateway_name) . '.ipn.membership.renew');
        } else {
            $ipn_route = route('user.' . strtolower($payment_gateway_name) . '.ipn.membership.renew');
        }

        return [
            'amount' => $this->total,
            'title' => $this->title,
            'description' => $this->description,
            'ipn_url' => $ipn_route,
            'order_id' => $this->last_membership_id,
            'track' => Str::random(36),
            'cancel_url' => route(self::CANCEL_ROUTE, $this->last_membership_id),
            'success_url' => route('user.membership.all'),
            'email' => $email,
            'name' => $name,
            'payment_type' => 'deposit',
        ];
    }

    //send email
    private function sendEmail($name,$last_membership_id,$email)
    {
        $membership = UserMembership::find($last_membership_id);
        $membership_type = $membership->membership?->membership_type?->type;
        $membership_price = float_amount_with_currency_symbol($membership->price);
        $membership_expire_date = isset($membership->expire_date) ? Carbon::parse($membership->expire_date)->toFormattedDateString() : '';

        try {
            //Send membership email to user
            $subject = get_static_option('user_membership_renew_email_subject') ?? __('Membership renew email');
            $message = get_static_option('user_membership_renew_message') ?? __('Your membership renew successfully completed.');
            $message = str_replace(["@membership_id", "@membership_type", "@membership_price", "@membership_expire_date"], [$last_membership_id, $membership_type, $membership_price, $membership_expire_date], $message);
            Mail::to($email)->send(new BasicMail([
                'subject' => $subject,
                'message' => $message
            ]));

            //Send membership email to admin
            $subject = get_static_option('user_membership_renew_email_subject') ?? __('Membership renew email');
            $message = get_static_option('user_membership_renew_message_for_admin') ?? __('A user just renew a membership.');
            $message = str_replace(["@membership_id", "@membership_type", "@membership_price", "@membership_expire_date","@name","@email"],[$last_membership_id, $membership_type, $membership_price, $membership_expire_date, $name,$email], $message);
            Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                'subject' => $subject,
                'message' => $message
            ]));

        } catch (\Exception $e) {
            \Toastr::error($e->getMessage());
        }
    }

    //admin notification
    private function adminNotification($last_membership_id,$user_id)
    {
        AdminNotification::create([
            'identity'=>$last_membership_id,
            'user_id'=>$user_id,
            'type'=>__('Renew Membership'),
            'message'=>__('User membership renew'),
        ]);
    }
}
