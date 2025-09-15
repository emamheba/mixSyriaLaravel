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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Modules\Membership\app\Models\Membership;
use Modules\Membership\app\Models\MembershipHistory;
use Modules\Membership\app\Models\UserMembership;
use Modules\Wallet\app\Models\Wallet;
use Modules\Membership\app\Http\Services\MembershipService;

class BuyMembershipController extends Controller
{
    protected $membershipService;
    private float $total;
    private string $title;
    private string $description;
    private int $last_membership_id;
    private const CANCEL_ROUTE = 'membership.buy.payment.cancel.static';

    public function __construct()
    {
         $this->membershipService = app()->make(MembershipService::class);
    }

    public function membership_payment_cancel_static()
    {
        return view('membership::frontend.membership.cancel');
    }

    //buy membership
    public function buy_membership(Request $request)
    {

        if(isset($request->membership_id)){

            if (!Auth::guard('web')->check()) {
                toastr_error(__('Please login before buying a membership.'));
                return redirect()->back();
            }

            $user = Auth::guard('web')->user();
            $membership_details = Membership::with('membership_type:id,validity')
                ->where('id',$request->membership_id)
                ->where('status','1')->first();

            if (empty($membership_details)){
                toastr_error(__('We could not find the Membership Plan you were looking for. Please check the ID and try again. If the problem persists, contact support.'));
                return redirect()->back();
            }

            if(isset($membership_details->id)) {
                // if membership payment gateway not select
                if ($membership_details->price > 0) {
                    if (empty($request->selected_payment_gateway) || is_null($request->selected_payment_gateway)) {
                        toastr_error(__('Payment gateway is missing. Please try again.'));
                        return redirect()->back();
                    }
                }

                if ($membership_details->price == 0) {
                    //check it price is zero, check it already has membership for one time in zero price
                    $check_already_has_free_membership_in_this_month = UserMembership::where([
                        'user_id' => auth('web')->id(),
                        'membership_id' => $membership_details->id,
                        'payment_status' => 'complete',
                    ])->limit(1)->latest()->first();

                    if (!empty($check_already_has_free_membership_in_this_month)) {
                        $expire_date = Carbon::parse($check_already_has_free_membership_in_this_month->expire_date)->subDays(2);
                        if (Carbon::today()->lessThan($expire_date)) {
                            toastr_error(__('You have already received the free plan once, You can only take it once.'));
                            return redirect()->back();
                        }
                    }else{
                        // Create free membership if the user is eligible
                        if ($user && empty($check_already_has_free_membership_in_this_month)) {
                           $this->membershipService->createFreeMembership($user);

                            toastr_success(__('Congratulations! Your free membership has been activated'));
                            return redirect()->back();
                        }
                    }
                }
            }


            // Check if the user already has a membership
            $user_membership_exits =  UserMembership::where('user_id', $user->id)->first();
            $user_membership_updated = false;

            // initialized value for if exits user current membership
            $user_current_listing_limit = 0;
            $user_current_gallery_images = 0;
            $user_current_featured_listing = 0;
            $user_current_enquiry_form = 0;
            $user_current_business_hour = 0;
            $user_current_membership_badge = 0;

            if ($user_membership_exits) {
                // Check if the membership IDs are different
                if ($user_membership_exits->membership_id !== $membership_details->id) {
                    // Check if the membership has not expired
                    if ($user_membership_exits->expire_date > now()) {
                        $user_current_listing_limit = $user_membership_exits->listing_limit;
                        $user_current_gallery_images = $user_membership_exits->gallery_images;
                        $user_current_featured_listing = $user_membership_exits->featured_listing;
                        $user_current_enquiry_form = $user_membership_exits->enquiry_form;
                        $user_current_business_hour = $user_membership_exits->business_hour;
                        $user_current_membership_badge = $user_membership_exits->membership_badge;
                    }
                }
            }


            if(!empty($membership_details)){

                // Calculate the expiration date based on the membership validity
                $expire_date = Carbon::now()->addDays($membership_details?->membership_type?->validity);

                // if user membership exists and not expired
                if(!empty($user_membership_exits) && $user_membership_exits->expire_date > Carbon::now()) {
                    $current_expire_date = Carbon::parse($user_membership_exits->expire_date);
                    $expire_date = $current_expire_date->addDays($membership_details->membership_type->validity ?? 0);

                    // for manual payment admin approve then update expire date other wise basic date add
                    $expire_date_for_manual_payment = Carbon::now()->addDays($membership_details?->membership_type?->validity);
                    $expire_date_for_user_exits_membership = Carbon::now()->addDays($membership_details?->membership_type?->validity);
                }

                // Set the title and total price for the membership purchase
                $this->title = __('Buy Membership');
                $this->total = $membership_details->price;

               // Calculate the new limits and features by adding the current user's limits and features
                $listing_limit = $membership_details->listing_limit + $user_current_listing_limit;
                $gallery_images = $membership_details->gallery_images + $user_current_gallery_images;
                $featured_listing = $membership_details->featured_listing + $user_current_featured_listing;

                $enquiry_form = ($membership_details->enquiry_form || $user_current_enquiry_form) ? 1 : 0;
                $business_hour = ($membership_details->business_hour || $user_current_business_hour) ? 1 : 0;
                $membership_badge = ($membership_details->membership_badge || $user_current_membership_badge) ? 1 : 0;

                $name = $user->first_name.' '.$user->last_name;
                $email = $user->email;

                $payment_status = $request->selected_payment_gateway === 'wallet' ? 'complete' : 'pending';
                $status = $request->selected_payment_gateway === 'wallet' ? 1 : 0;
                session()->put('user_id',$user->id);

                // if manual payment gateway
                if($request->selected_payment_gateway === 'manual_payment')
                {
                    $request->validate([
                            'trasaction_attachment' => 'required|mimes:jpg,jpeg,png,pdf,webp'
                    ]);

                    if($request->hasFile('trasaction_attachment')){
                        $manual_payment_image = $request->trasaction_attachment;
                        $img_ext = $manual_payment_image->extension();
                        $manual_payment_image_name = 'trasaction_attachment_'.time().'.'.$img_ext;

                        if(in_array($img_ext,['jpg','jpeg','png','pdf','webp'])){
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
                                $manual_payment_image->move($manual_image_path,$manual_payment_image_name);
                            } // Image scan end

                            // for user membership update
                            if(!empty($user_membership_exits)) {
                                // Create membership history
                               $new_membership_history = MembershipHistory::create([
                                    'membership_id' => $membership_details->id,
                                    'user_id' => $user->id,
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

                                // membership history ID in session
                                if ($new_membership_history) {
                                    session()->put('membership_history_id', $new_membership_history->id);
                                }

                                $membership_history_id = $new_membership_history->id;
                                $this->last_membership_id = $membership_details->id;
                                $this->adminNotification($this->last_membership_id,$user->id);
                                $this->sendEmailToUpgrade($name,$this->last_membership_id,$membership_history_id,$email);
                                return redirect()->route('user.membership.all')->with(toastr_warning(__('Membership upgrade success. Your membership will be usable after admin approval')));

                            }else{
                                // create membership
                                $buy_membership = UserMembership::create([
                                    'user_id' => $user->id,
                                    'membership_id' => $membership_details->id,
                                    'price' => $this->total,
                                    'initial_listing_limit' => $listing_limit,
                                    'initial_gallery_images' => $gallery_images,
                                    'initial_featured_listing' => $featured_listing,
                                    'initial_enquiry_form' => $enquiry_form,
                                    'initial_business_hour' => $business_hour,
                                    'initial_membership_badge' => $membership_badge,
                                    'listing_limit' => $listing_limit,
                                    'gallery_images' => $gallery_images,
                                    'featured_listing' => $featured_listing,
                                    'enquiry_form' => $enquiry_form,
                                    'business_hour' => $business_hour,
                                    'membership_badge' => $membership_badge,
                                    'expire_date' => $expire_date,
                                    'payment_gateway' => $request->selected_payment_gateway,
                                    'transaction_id' => $request->trasaction_id,
                                    'manual_payment_image' => $manual_payment_image_name,
                                    'payment_status' => $payment_status,
                                    'status' => $status,
                                ]);

                                // Check if the membership was successfully updated
                                if ($buy_membership) {
                                    $user_membership = $buy_membership;
                                    // Create membership history
                                    $new_membership_history = MembershipHistory::create([
                                        'membership_id' => $user_membership->membership_id,
                                        'user_id' => $user_membership->user_id,
                                        'payment_status' => $user_membership->payment_status,
                                        'payment_gateway' => $user_membership->payment_gateway,
                                        'transaction_id' => $user_membership->transaction_id,
                                        'manual_payment_image' => $manual_payment_image_name,
                                        'expire_date' => $user_membership->expire_date,
                                        'listing_limit' => $user_membership->listing_limit,
                                        'gallery_images' => $user_membership->gallery_images,
                                        'featured_listing' => $user_membership->featured_listing,
                                        'enquiry_form' => $user_membership->enquiry_form,
                                        'business_hour' => $user_membership->business_hour,
                                        'membership_badge' => $user_membership->membership_badge,
                                        'price' => $this->total,
                                        'status' => $status,
                                    ]);

                                    // membership history ID in session
                                    if ($new_membership_history) {
                                        session()->put('membership_history_id', $new_membership_history->id);
                                    }

                                    if($user_membership_updated) {
                                        // Fetch the updated membership
                                        $updated_membership = UserMembership::find($user_membership_exits->id);
                                        $membership_id = $updated_membership->id;
                                    }
                                    $this->last_membership_id = $membership_id ?? $buy_membership->id;
                                    $this->adminNotification($this->last_membership_id,$user->id);
                                    $this->sendEmail($name,$this->last_membership_id,$email);
                                }
                            }

                        }else{
                            return back()->with(toastr_warning(__('Image type not supported')));
                        }
                    }

                    return redirect()->route('user.membership.all')->with(toastr_warning(__('Membership purchase success. Your membership will be usable after admin approval')));

                }elseif($request->selected_payment_gateway === 'wallet'){
                    // if user wallet payment gateway
                    $wallet_balance = Wallet::select('balance')->where('user_id',$user->id)->first();

                    if(isset($wallet_balance) && $wallet_balance->balance > $this->total){
                       if(!empty($user_membership_exits)) {
                           // Update existing membership
                           $user_membership_updated = $user_membership_exits->update([
                               'user_id' => $user->id,
                               'membership_id' => $membership_details->id,
                               'price' => $this->total,
                               'listing_limit' => $listing_limit,
                               'gallery_images' => $gallery_images,
                               'featured_listing' => $featured_listing,
                               'enquiry_form' => $enquiry_form,
                               'business_hour' => $business_hour,
                               'membership_badge' => $membership_badge,

                               // initial stat
                               'initial_listing_limit' => $listing_limit,
                               'initial_gallery_images' => $gallery_images,
                               'initial_featured_listing' => $featured_listing,
                               'initial_enquiry_form' => $enquiry_form,
                               'initial_business_hour' => $business_hour,
                               'initial_membership_badge' => $membership_badge,
                               // initial end

                               'expire_date' => $expire_date,
                               'payment_gateway' => $request->selected_payment_gateway,
                               'payment_status' => $payment_status,
                               'status' => $status,
                           ]);

                           // Check if the membership was successfully updated
                           if ($user_membership_updated) {
                               $user_membership = UserMembership::find($user_membership_exits->id);
                               // Create membership history
                               $new_membership_history = MembershipHistory::create([
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
                                   'price' => $this->total,
                                   'status' => $status,
                               ]);

                               // membership history ID in session
                               if ($new_membership_history) {
                                   session()->put('membership_history_id', $new_membership_history->id);
                               }

                               //Buy membership notification to admin
                               AdminNotification::create([
                                   'identity'=> $user_membership->membership_id,
                                   'user_id'=> $user_membership->user_id,
                                   'type'=>'Buy Membership',
                                   'message'=>__('User membership Upgrade'),
                               ]);
                           }

                       }else{
                           // create membership
                           $buy_membership = UserMembership::create([
                               'user_id' => $user->id,
                               'membership_id' => $membership_details->id,
                               'price' => $this->total,
                               'initial_listing_limit' => $listing_limit,
                               'initial_gallery_images' => $gallery_images,
                               'initial_featured_listing' => $featured_listing,
                               'initial_enquiry_form' => $enquiry_form,
                               'initial_business_hour' => $business_hour,
                               'initial_membership_badge' => $membership_badge,
                               'listing_limit' => $listing_limit,
                               'gallery_images' => $gallery_images,
                               'featured_listing' => $featured_listing,
                               'enquiry_form' => $enquiry_form,
                               'business_hour' => $business_hour,
                               'membership_badge' => $membership_badge,
                               'expire_date' => $expire_date,
                               'payment_gateway' => $request->selected_payment_gateway,
                               'payment_status' => $payment_status,
                               'status' => $status,
                           ]);

                           // Check if the membership was successfully updated
                           if ($buy_membership) {
                               $user_membership = $buy_membership;
                               // Create membership history
                               $new_membership_history = MembershipHistory::create([
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
                                   'price' => $this->total,
                                   'status' => $status,
                               ]);

                               // membership history ID in session
                               if ($new_membership_history) {
                                   session()->put('membership_history_id', $new_membership_history->id);
                               }

                               //Buy membership notification to admin
                               AdminNotification::create([
                                   'identity'=> $user_membership->membership_id,
                                   'user_id'=> $user_membership->user_id,
                                   'type'=>'Buy Membership',
                                   'message'=>__('User membership purchase'),
                               ]);

                           }
                       }

                        if($user_membership_updated) {
                            // Fetch the updated membership
                            $updated_membership = UserMembership::find($user_membership_exits->id);
                            $membership_id = $updated_membership->id;
                        }

                        $this->last_membership_id = $membership_id ?? $buy_membership->id;
                        Wallet::where('user_id',$user->id)->update(['balance'=> $wallet_balance->balance - $this->total]);
                        $this->sendEmail($name,$this->last_membership_id,$email);

                    }else{
                        return back()->with(toastr_warning(__('Please deposit to your wallet and try again')));
                    }
                    $this->sendEmail($name,$this->last_membership_id,$email);
                    toastr_success('Membership purchase success.');
                    return redirect()->route('user.membership.all');

                }else {
                    if(!empty($user_membership_exits)) {
                        // notes:: payment gateway pay if user membership already exits create membership history then updater if payment status done user membership table update
                        if (!empty($membership_details)) {
                            // Create membership history
                            $new_membership_history =  MembershipHistory::create([
                                'membership_id' => $membership_details->id,
                                'user_id' => Auth::guard('web')->user()->id,
                                'payment_status' => '',
                                'payment_gateway' => $request->selected_payment_gateway,
                                'expire_date' => $expire_date_for_user_exits_membership,
                                'listing_limit' => $membership_details->listing_limit,
                                'gallery_images' => $membership_details->gallery_images,
                                'featured_listing' => $membership_details->featured_listing,
                                'enquiry_form' => $membership_details->enquiry_form,
                                'business_hour' => $membership_details->business_hour,
                                'membership_badge' => $membership_details->membership_badge,
                                'price' => $this->total,
                                'status' => $status,
                            ]);

                            $buy_membership = $new_membership_history;

                            // membership history ID in session
                            if ($new_membership_history) {
                                session()->put('membership_history_id', $new_membership_history->id);
                                session()->put('upgrade_membership_id', $membership_details->id);
                            }
                        }

                     }else{
                        // create membership
                        $buy_membership = UserMembership::create([
                            'user_id' => $user->id,
                            'membership_id' => $membership_details->id,
                            'price' => $this->total,
                            'initial_listing_limit' => $listing_limit,
                            'initial_gallery_images' => $gallery_images,
                            'initial_featured_listing' => $featured_listing,
                            'initial_enquiry_form' => $enquiry_form,
                            'initial_business_hour' => $business_hour,
                            'initial_membership_badge' => $membership_badge,
                            'listing_limit' => $listing_limit,
                            'gallery_images' => $gallery_images,
                            'featured_listing' => $featured_listing,
                            'enquiry_form' => $enquiry_form,
                            'business_hour' => $business_hour,
                            'membership_badge' => $membership_badge,
                            'expire_date' => $expire_date,
                            'payment_gateway' => $request->selected_payment_gateway,
                        ]);

                        // Check if the membership was successfully updated
                        if ($buy_membership) {
                            $user_membership = $buy_membership;
                            // Create membership history
                            $new_membership_history = MembershipHistory::create([
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
                                'price' => $this->total,
                                'status' => $status,
                            ]);

                            // membership history ID in session
                            if ($new_membership_history) {
                                session()->put('membership_history_id', $new_membership_history->id);
                            }
                        }
                    }

                    // for buy new membership
                    $this->last_membership_id = $membership_id ?? $buy_membership->id;

                    // if user user current membership upgrade
                      if(!empty($user_membership_exits)) {
                        if (!empty($membership_details)) {
                            if (!empty($buy_membership)) {
                                $this->last_membership_id = $user_membership_exits->id;
                            }
                        }
                      }

                    $this->description = sprintf(__('Order id #%1$d Email: %2$s, Name: %3$s'),$this->last_membership_id,$email,$name);

                    // all payment gateway check start
                    $payment_gateway = $request->selected_payment_gateway;
                    $credential_function = 'get_' . $payment_gateway . '_credential';

                    if (!method_exists((new PaymentGatewayCredential()), $credential_function))
                    {
                        $custom_data['request'] = $request->all();
                        $custom_data['payment_details'] = $buy_membership->toArray();
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
                    // all payment gateway check end
                }
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
            $ipn_route = route('user.' . strtolower($payment_gateway_name) . '.ipn.membership');
        } else {
            $ipn_route = route('user.' . strtolower($payment_gateway_name) . '.ipn.membership');
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
            $subject = get_static_option('user_membership_purchase_email_subject') ?? __('Membership purchase email');
            $message = get_static_option('user_membership_purchase_message') ?? __('Your membership purchase successfully completed.');
            $message = str_replace(["@membership_id", "@membership_type", "@membership_price", "@membership_expire_date"], [$last_membership_id, $membership_type, $membership_price, $membership_expire_date], $message);
            Mail::to($email)->send(new BasicMail([
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

    //send email
    private function sendEmailToUpgrade($name,$last_membership_id,$membership_history_id, $email)
    {

         $membership_history = MembershipHistory::find($membership_history_id);
         $membership = Membership::find($last_membership_id);
         $membership_type = $membership?->membership_type?->type;
         $membership_price = float_amount_with_currency_symbol($membership_history->price);
         $membership_expire_date = isset($membership_history->expire_date) ? Carbon::parse($membership_history->expire_date)->toFormattedDateString() : '';

        try {
            //Send membership email to user
            $subject = get_static_option('user_membership_purchase_email_subject') ?? __('Membership purchase email');
            $message = get_static_option('user_membership_purchase_message') ?? __('Your membership purchase successfully completed.');
            $message = str_replace(["@membership_id", "@membership_type", "@membership_price", "@membership_expire_date"], [$last_membership_id, $membership_type, $membership_price, $membership_expire_date], $message);
            Mail::to($email)->send(new BasicMail([
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

    //admin notification
    private function adminNotification($last_membership_id,$user_id)
    {
        AdminNotification::create([
            'identity'=>$last_membership_id,
            'user_id'=>$user_id,
            'type'=> __('Buy Membership'),
            'message'=> __('User membership purchase')
        ]);
    }
}
