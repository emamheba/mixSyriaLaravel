<?php

namespace Modules\Membership\app\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Models\Backend\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Modules\Membership\app\Models\MembershipHistory;
use Modules\Membership\app\Models\UserMembership;

class UserMembershipController extends Controller
{
    //user memberships
    public function all_membership()
    {
        $all_memberships = UserMembership::whereHas('user')->latest()->paginate(10);
        $active_membership = UserMembership::whereHas('user')->where('status',1)->count();
        $inactive_membership = UserMembership::whereHas('user')->where('status',0)->count();
        $manual_membership = UserMembership::whereHas('user')->where('payment_gateway','manual_payment')->count();
        $route = route("admin.user.membership.paginate.data");

        return view('membership::backend.user-membership.all-membership',compact(['all_memberships','active_membership','inactive_membership','manual_membership','route']));
    }

    // pagination
    function pagination(Request $request)
    {
        if($request->ajax()){
            $all_memberships = $request->string_search == ''
                ? UserMembership::whereHas('user')->with('membership:id,membership_type_id')->latest()->paginate(10)
                : UserMembership::whereHas('user')->with('membership:id,membership_type_id')->latest()->$this->query__($request);

            $route = route("admin.user.membership.paginate.data");

            return view('membership::backend.user-membership.search-result', compact('all_memberships', 'route'))->render();
        }
    }

    // search string
    public function search_membership(Request $request)
    {
        $query = UserMembership::whereHas('user')->latest();
        if($request->filter_val != ''){
            if($request->filter_val == 1){
                $query->where('status',1);
            }
            if($request->filter_val == 0){
                $query->where('status',0);
            }
            if($request->filter_val == 'manual_payment'){
                $query->where('payment_gateway','manual_payment');
            }
        }

        $all_memberships = $query->where(function($q) use($request){
            $q->where('id', 'LIKE', "%". strip_tags($request->string_search) ."%")
                ->orWhere('user_id', 'LIKE', "%". strip_tags($request->string_search) ."%")
                ->orWhere('created_at', 'LIKE', "%". strip_tags($request->string_search) ."%")
                ->orWhere('expire_date', 'LIKE', "%". strip_tags($request->string_search) ."%");
        })->paginate(10);

        $route = route("admin.user.membership.search");

        return $all_memberships->total() >= 1 ? view('membership::backend.user-membership.search-result', compact('all_memberships', 'route'))->render() : response()->json(['status'=>__('nothing')]);
    }

    //change status
    public function change_status($id)
    {
        $membership = UserMembership::find($id);
        $user_firstname = $membership->user?->first_name ?? '';
        $user_email = $membership->user?->email ?? '';
        $status = $membership->status == 1 ? 0 : 1;

        $last_membership_id = $membership->id;
        $membership = UserMembership::find($last_membership_id);
        $membership_type = $membership->membership?->membership_type?->type;
        $membership_price = float_amount_with_currency_symbol($membership->price);
        $membership_expire_date = isset($membership->expire_date) ? Carbon::parse($membership->expire_date)->toFormattedDateString() : '';

        if($status == 0){
            // send to user
            try {
                $subject = get_static_option('user_membership_inactive_email_subject') ?? __('membership Inactive');
                $message = get_static_option('user_membership_inactive_message') ?? __('Your membership status changed from active to inactive.');
                $message = str_replace(["@membership_id", "@membership_type", "@membership_price", "@membership_expire_date"], [$last_membership_id, $membership_type, $membership_price, $membership_expire_date], $message);
                Mail::to($user_email)->send(new BasicMail([
                    'subject' => $subject,
                    'message' => $message
                ]));
            } catch (\Exception $e) {}
        }else{
            // send to user
            try {
                $subject = get_static_option('user_membership_active_email_subject') ?? __('membership Active');
                $message = get_static_option('user_membership_active_message') ?? __('Your membership status changed from inactive to active.');
                $message = str_replace(["@membership_id", "@membership_type", "@membership_price", "@membership_expire_date"], [$last_membership_id, $membership_type, $membership_price, $membership_expire_date], $message);
                Mail::to($user_email)->send(new BasicMail([
                    'subject' => $subject,
                    'message' => $message
                ]));
            } catch (\Exception $e) {}
        }
        UserMembership::where('id',$id)->update(['status'=>$status]);
        return back()->with(FlashMsg::item_new(__('Status successfully changed')));
    }

    //active membership
    public function active_membership(Request $request)
    {
        $all_memberships = $request->string_search == ''
            ? UserMembership::whereHas('user')->where('status',1)->paginate(10)
            : UserMembership::whereHas('user')->latest()->where('status',1)->$this->query__($request);

        $route = route("admin.user.membership.active");

        return $all_memberships->total() >= 1 ? view('membership::backend.user-membership.search-result', compact('all_memberships', 'route'))->render() : response()->json(['status'=>__('nothing')]);
    }

    //inactive membership
    public function inactive_membership(Request $request)
    {
        $all_memberships = $request->string_search == ''
            ? UserMembership::whereHas('user')->where('status',0)->paginate(10)
            : UserMembership::whereHas('user')->latest()->where('status',0)->$this->query__($request);
        $route = route("admin.user.membership.active");

        return $all_memberships->total() >= 1 ? view('membership::backend.user-membership.search-result', compact('all_memberships', 'route'))->render() : response()->json(['status'=>__('nothing')]);
    }

    //manual membership
    public function manual_membership(Request $request)
    {
        $all_memberships = $request->string_search == ''
            ? UserMembership::whereHas('user')->where('payment_gateway','manual_payment')->paginate(10)
            : UserMembership::whereHas('user')->latest()->where('payment_gateway','manual_payment')->$this->query__($request);
        $route = route("admin.user.membership.active");

        return $all_memberships->total() >= 1 ? view('membership::backend.user-membership.search-result', compact('all_memberships', 'route'))->render() : response()->json(['status'=>__('nothing')]);
    }

    //read unread
    public function read_unread($id)
    {
        AdminNotification::where('identity',$id)->update(['is_read'=>1]);
        return redirect()->route('admin.user.membership.all');
    }

    //update manual payment
    public function update_manual_payment(Request $request)
    {
        $membership_details = UserMembership::where('id',$request->membership_id)->first();
        $history_membership_details = MembershipHistory::query()
            ->where('user_id', $membership_details->user_id)
            ->where('payment_status', 'pending')
            ->where('payment_gateway', 'manual_payment')
            ->first();

        // user membership payment status update
        $payment_status = $membership_details->payment_status == 'pending' ? 'complete' : 'pending';
        UserMembership::where('id',$request->membership_id)->update(['payment_status'=>$payment_status,'status'=> 1]);

        // user membership history payment status update
        $history_payment_status = $history_membership_details->payment_status == 'pending' ? 'complete' : 'pending';
        $history_membership_details->update(['payment_status'=>$history_payment_status,'status'=> 1]);

        $last_membership_id = $request->membership_id;
        $membership = UserMembership::find($last_membership_id);
        $membership_type = $membership->membership?->membership_type?->type;
        $membership_price = float_amount_with_currency_symbol($membership->price);
        $membership_expire_date = isset($membership->expire_date) ? Carbon::parse($membership->expire_date)->toFormattedDateString() : '';
        $name = optional($membership->user)->first_name.' '.optional($membership->user)->last_name;
        $email = $request->user_email;

        //Send manual membership payment complete email to user
        try {
            $subject = get_static_option('user_membership_manual_payment_complete_email_subject') ?? __('Manual membership payment complete email');
            $message = get_static_option('user_membership_manual_payment_complete_message') ?? __('Your manual membership payment successfully completed.');
            $message = str_replace(["@membership_id", "@membership_type", "@membership_price", "@membership_expire_date"], [$last_membership_id, $membership_type, $membership_price, $membership_expire_date], $message);
            Mail::to($request->user_email)->send(new BasicMail([
                'subject' => $subject,
                'message' => $message
            ]));
        } catch (\Exception $e) {}

        //Send manual membership payment complete email to admin
        try {
            $subject = get_static_option('user_membership_manual_payment_complete_to_admin_email_subject') ?? __('Manual membership payment complete');
            $message = get_static_option('user_membership_manual_payment_complete_to_admin_message') ?? __('A manual membership payment successfully completed.');
            $message = str_replace(["@membership_id", "@membership_type", "@membership_price", "@membership_expire_date","@name","@email"],[$last_membership_id, $membership_type, $membership_price, $membership_expire_date, $name,$email], $message);
            Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                'subject' => $subject,
                'message' => $message
            ]));
        } catch (\Exception $e) {}

        return redirect()->back()->with(FlashMsg::item_new(__('Payment Successfully Changed')));
    }

    public function history_update_manual_payment(Request $request)
    {
        $user_membership_history = MembershipHistory::find($request->membership_history_id);
        if (empty($user_membership_history)){
            return back()->with(FlashMsg::item_delete(__('Membership History Not Found')));
        }

        $last_membership_id = $user_membership_history->membership_id;
        $user_id = $user_membership_history->user_id;

        // Check if the user already has a membership
        $user_membership_exits = UserMembership::where('user_id', $user_id)->first();

        // initialized value for if exits user current membership
        $user_current_listing_limit = 0;
        $user_current_gallery_images = 0;
        $user_current_featured_listing = 0;
        $user_current_enquiry_form = 0;
        $user_current_business_hour = 0;
        $user_current_membership_badge = 0;

        // if not expire membership
        if ($user_membership_exits && $user_membership_exits->expire_date > now()) {
            $user_current_listing_limit = $user_membership_exits->listing_limit;
            $user_current_gallery_images = $user_membership_exits->gallery_images;
            $user_current_featured_listing = $user_membership_exits->featured_listing;
            $user_current_enquiry_form = $user_membership_exits->enquiry_form;
            $user_current_business_hour = $user_membership_exits->business_hour;
            $user_current_membership_badge = $user_membership_exits->membership_badge;
        }

        // Calculate the new limits and features by adding the current user's limits and features
        $listing_limit = $user_membership_history->listing_limit + $user_current_listing_limit;
        $gallery_images = $user_membership_history->gallery_images + $user_current_gallery_images;
        $featured_listing = $user_membership_history->featured_listing + $user_current_featured_listing;

        $enquiry_form = ($user_membership_history->enquiry_form || $user_current_enquiry_form) ? 1 : 0;
        $business_hour = ($user_membership_history->business_hour || $user_current_business_hour) ? 1 : 0;
        $membership_badge = ($user_membership_history->membership_badge || $user_current_membership_badge) ? 1 : 0;


        // Parse existing expire dates as Carbon instances
        $current_expire_date = Carbon::parse($user_membership_exits->expire_date);
        $new_history_expire_date = Carbon::parse($user_membership_history->expire_date);
        // current expiration date
        $current_days_to_expire = $current_expire_date->diffInDays(Carbon::now());
        // new history expiration date
        $new_days_to_expire = $new_history_expire_date->diffInDays(Carbon::now());
        // new expiration date by adding the two periods
        $expire_date = Carbon::now()->addDays($current_days_to_expire + $new_days_to_expire);

        // Update existing user membership
        $user_membership_exits->update([
            'membership_id' => $user_membership_history->membership_id,
            'price' => $user_membership_history->price,
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
            'payment_gateway' => $user_membership_history->payment_gateway,
            'transaction_id' => $user_membership_history->transaction_id,
            'manual_payment_image' => $user_membership_history->manual_payment_image,
            'payment_status' => 'complete',
            'status' => 1,
        ]);

        // Update existing user membership history
        $user_membership_history->update([
            'expire_date' => $expire_date,
            'payment_status' => 'complete',
            'status' => 1,
        ]);

        $membership_type = $user_membership_exits->membership?->membership_type?->type;
        $membership_price = float_amount_with_currency_symbol($user_membership_exits->price);
        $membership_expire_date = isset($user_membership_exits->expire_date) ? Carbon::parse($user_membership_exits->expire_date)->toFormattedDateString() : '';
        $name = optional($user_membership_exits->user)->first_name.' '.optional($user_membership_exits->user)->last_name;
        $email = $request->user_email;


        //Send manual membership payment complete email to user
        try {
            $subject = get_static_option('user_membership_manual_payment_complete_email_subject') ?? __('Manual membership payment complete email');
            $message = get_static_option('user_membership_manual_payment_complete_message') ?? __('Your manual membership payment successfully completed.');
            $message = str_replace(["@membership_id", "@membership_type", "@membership_price", "@membership_expire_date"], [$last_membership_id, $membership_type, $membership_price, $membership_expire_date], $message);
            Mail::to($request->user_email)->send(new BasicMail([
                'subject' => $subject,
                'message' => $message
            ]));
        } catch (\Exception $e) {}

        //Send manual membership payment complete email to admin
        try {
            $subject = get_static_option('user_membership_manual_payment_complete_to_admin_email_subject') ?? __('Manual membership payment complete');
            $message = get_static_option('user_membership_manual_payment_complete_to_admin_message') ?? __('A manual membership payment successfully completed.');
            $message = str_replace(["@membership_id", "@membership_type", "@membership_price", "@membership_expire_date","@name","@email"],[$last_membership_id, $membership_type, $membership_price, $membership_expire_date, $name,$email], $message);
            Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                'subject' => $subject,
                'message' => $message
            ]));
        } catch (\Exception $e) {}

        return redirect()->back()->with(FlashMsg::item_new(__('Payment Successfully Changed')));
    }



    private function query__($request)
    {
        UserMembership::where(function($query) use($request){
            $query->where('id', 'LIKE', "%". strip_tags($request->string_search) ."%")
                ->orWhere('user_id', 'LIKE', "%". strip_tags($request->string_search) ."%")
                ->orWhere('created_at', 'LIKE', "%". strip_tags($request->string_search) ."%")
                ->orWhere('expire_date', 'LIKE', "%". strip_tags($request->string_search) ."%");
        })->paginate(10);
    }

    public function send_email_to_user($id=null)
    {
        $user = UserMembership::find($id);
        $user_email = optional($user->user)->email;
        $expire_date = date('d-m-Y', strtotime($user->expire_date));

        //Send to user
        try {
            $message_body_user = __('Dear user,').'</br>'
                .'<span class="verify-code">'.__('Your membership will be expired on').' '.$expire_date.'</br>'
                .'</span>';

            Mail::to($user_email)->send(new BasicMail([
                'subject' => __('Membership Reminder'),
                'message' => $message_body_user
            ]));

        } catch (\Exception $e) {
            \Toastr::error($e->getMessage());
        }
        return redirect()->back()->with(FlashMsg::item_new(__('Email Send Success')));
    }
}
