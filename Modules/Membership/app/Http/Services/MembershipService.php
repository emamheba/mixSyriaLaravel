<?php

namespace Modules\Membership\app\Http\Services;

use App\Mail\BasicMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Modules\Membership\app\Models\Membership;
use Modules\Membership\app\Models\MembershipHistory;
use Modules\Membership\app\Models\UserMembership;

class MembershipService
{
    public function createFreeMembership(User $user)
    {
        $membership_details = Membership::with('membership_type:id,validity')
            ->where('id', get_static_option('register_membership'))
            ->where('status', '1')->first();

        $expire_date = Carbon::now()->addDays($membership_details?->membership_type?->validity);

        // initial value
        $initial_listing_limit = $membership_details->listing_limit;
        $initial_gallery_images = $membership_details->gallery_images;
        $initial_featured_listing = $membership_details->featured_listing;
        $initial_enquiry_form = $membership_details->enquiry_form;
        $initial_business_hour = $membership_details->business_hour;
        $initial_membership_badge = $membership_details->membership_badge;

        $listing_limit = $membership_details->listing_limit;
        $gallery_images = $membership_details->gallery_images;
        $featured_listing = $membership_details->featured_listing;
        $enquiry_form = $membership_details->enquiry_form;
        $business_hour = $membership_details->business_hour;
        $membership_badge = $membership_details->membership_badge;

        $user_membership = UserMembership::create([
            'user_id' => $user->id,
            'membership_id' => $membership_details->id,
            'price' => $membership_details->price,
            'initial_listing_limit' => $initial_listing_limit,
            'initial_gallery_images' => $initial_gallery_images,
            'initial_featured_listing' => $initial_featured_listing,
            'initial_enquiry_form' => $initial_enquiry_form,
            'initial_business_hour' => $initial_business_hour,
            'initial_membership_badge' => $initial_membership_badge,
            'listing_limit' => $listing_limit,
            'gallery_images' => $gallery_images,
            'featured_listing' => $featured_listing,
            'enquiry_form' => $enquiry_form,
            'business_hour' => $business_hour,
            'membership_badge' => $membership_badge,
            'expire_date' => $expire_date,
            'payment_gateway' => 'Trial',
            'manual_payment_payment' => '',
            'payment_status' => 'complete',
            'status' => 1,
        ]);

        // create membership history
        if ($user_membership){
            MembershipHistory::create([
                'user_id' => $user_membership->user_id,
                'membership_id' => $user_membership->membership_id,
                'price' => $user_membership->price,
                'listing_limit' => $user_membership->listing_limit,
                'gallery_images' => $user_membership->gallery_images,
                'featured_listing' => $user_membership->featured_listing,
                'enquiry_form' => $user_membership->enquiry_form,
                'business_hour' => $user_membership->business_hour,
                'membership_badge' => $user_membership->membership_badge,
                'expire_date' => $user_membership->expire_date,
                'payment_gateway' => $user_membership->payment_gateway,
                'manual_payment_payment' => $user_membership->manual_payment_payment,
                'payment_status' => $user_membership->payment_status,
                'status' => $user_membership->status,
            ]);
        }

        //user register free membership auto create to user
        try {
            $last_membership_id = $user_membership->id;
            $membership = UserMembership::find($last_membership_id);
            $membership_type = $membership->membership?->membership_type?->type;
            $membership_price = float_amount_with_currency_symbol($membership->price);
            $membership_expire_date = isset($membership->expire_date) ? Carbon::parse($membership->expire_date)->toFormattedDateString() : '';

            $subject = get_static_option('user_membership_free_email_subject') ?? __('Free Membership email');
            $message = get_static_option('user_membership_free_message') ?? __('Congratulations! Your free membership has been activated');
            $message = str_replace(["@membership_id", "@membership_type", "@membership_price", "@membership_expire_date"], [$last_membership_id, $membership_type, $membership_price, $membership_expire_date], $message);
            Mail::to($user->email)->send(new BasicMail([
                'subject' => $subject,
                'message' => $message
            ]));
        }
        catch (\Exception $e) {}

        return $user_membership;
    }
}
