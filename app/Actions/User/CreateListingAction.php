<?php

namespace App\Actions\User;

use App\Models\Backend\IdentityVerification;
use App\Models\Backend\ListingTag;
use App\Models\Frontend\ListingAttribute;
use App\Models\User;
use App\Models\Backend\Listing;
use Modules\Blog\app\Models\Tag;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Brand;
use Modules\Membership\app\Models\UserMembership;
use App\Models\Backend\AdminNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CreateListingAction
{
    public function handle($request)
    {
        // Step 1: Validate user membership and verification
        $this->validateMembershipAndVerification($request);

        // Step 2: Validate request data
        $validatedData = $this->validateRequestData($request);

        // Step 3: Prepare listing data
        $listing = $this->prepareListingData($request, $validatedData);

        // Step 4: Save listing attributes and tags
        $this->saveListingAttributesAndTags($listing, $request);

        // Step 5: Update user membership limits
        $this->updateUserMembershipLimits($request);

        // Step 6: Notify admin and send email if needed
        $this->notifyAdminAndSendEmail($listing, $request);

        return redirect()->route('user.all.listing')->with(toastr_success(__('Listing Added Success')));
    }

    private function validateMembershipAndVerification($request)
    {
        // Check Membership Status
        if (moduleExists('Membership') && membershipModuleExistsAndEnable('Membership')) {
            $user_membership_check = UserMembership::where('user_id', Auth::guard('web')->user()->id)->first();
            if ($user_membership_check && ($user_membership_check->status === 0 || $user_membership_check->payment_status == 'pending')) {
                toastr_error(__('Your membership plan is inactive. Please activate your plan before creating listings.'));
                return redirect()->back();
            }
        }

        // Verify user identity
        if (get_static_option('listing_create_settings') == 'verified_user') {
            $user_identity = IdentityVerification::select('user_id', 'status')->where('user_id', Auth::guard('web')->user()->id)->first();
            $user_verified_status = $user_identity?->status ?? 0;
            if ($user_verified_status != 1) {
                toastr_error(__('You are not verified. To add listings, you must verify your account first.'));
                return redirect()->back();
            }
        }

        // Check membership package existence
        if (moduleExists('Membership')) {
            if (membershipModuleExistsAndEnable('Membership')) {
                $user_membership = UserMembership::where('user_id', Auth::guard('web')->user()->id)->first();
                if (is_null($user_membership)) {
                    toastr_error(__('You have to purchase a membership package to create listings.'));
                    return redirect()->back();
                }
            }
        }
    }

    private function validateRequestData($request)
    {
        return $request->validate([
            'category_id' => 'required',
            'title' => 'required|max:191',
            'description' => 'required|min:150',
            'slug' => 'required|max:255|unique:listings',
            'price' => 'required|numeric',
            'attributes_title' => 'nullable|array',
            'attributes_title.*' => 'nullable|string|max:255',
            'attributes_description' => 'nullable|array',
            'attributes_description.*' => 'nullable|string|max:1000',
        ], [
            'title.required' => __('The title field is required.'),
            'title.max' => __('The title must not exceed 191 characters.'),
            'description.required' => __('The description field is required.'),
            'description.min' => __('The description must be at least 150 characters.'),
            'slug.required' => __('The slug field is required.'),
            'slug.unique' => __('The slug has already been taken.'),
            'price.required' => __('The price field is required.'),
            'price.numeric' => __('The price must be a numeric value.')
        ]);
    }

    private function prepareListingData($request, $validatedData)
    {
        $user = User::where('id', Auth::guard('web')->user()->id)->first();
        $slug = !empty($request->slug) ? $request->slug : $request->title;
        $status = get_static_option('listing_create_status_settings') == 'approved' ? 1 : 0;

        $video_url = null;
        if (!empty($request->video_url)) {
            $video_url = getYoutubeEmbedUrl($request->video_url);
        }

        $listing_phone = $request->country_code ?? $request->phone;

        $listing = new Listing();
        $listing->user_id = $user->id;
        $listing->category_id = $request->category_id;
        $listing->sub_category_id = $request->sub_category_id;
        $listing->child_category_id = $request->child_category_id;
        $listing->country_id = $request->country_id;
        $listing->state_id = $request->state_id;
        $listing->city_id = $request->city_id;
        $listing->brand_id = $request->brand_id;
        $listing->title = $request->title;
        $listing->slug = Str::slug(purify_html($slug), '-', null);
        $listing->description = $request->description;
        $listing->price = $request->price;
        $listing->negotiable = $request->negotiable ?? 0;
        $listing->condition = $request->condition;
        $listing->authenticity = $request->authenticity;
        $listing->phone = $listing_phone;
        $listing->phone_hidden = $request->phone_hidden ?? 0;
        $listing->image = $request->image;
        $listing->gallery_images = $request->gallery_images;
        $listing->video_url = $video_url;
        $listing->address = $request->address;
        $listing->lat = $request->latitude;
        $listing->lon = $request->longitude;
        $listing->is_featured = $request->is_featured ?? 0;
        $listing->status = $status;

        $tags_name = '';
        if (!empty($request->tags)) {
            $tags_name = Tag::whereIn('id', $request->tags)->pluck('name')->implode(', ');
        }

        $Metas = [
            'meta_title' => purify_html($request->title),
            'meta_tags' => purify_html($tags_name),
            'meta_description' => substr(strip_tags(purify_html($request->description)), 0, 100),
            'facebook_meta_tags' => purify_html($tags_name),
            'facebook_meta_description' => substr(strip_tags(purify_html($request->description)), 0, 100),
            'facebook_meta_image' => $request->image,
            'twitter_meta_tags' => purify_html($tags_name),
            'twitter_meta_description' => substr(strip_tags(purify_html($request->description)), 0, 100),
            'twitter_meta_image' => $request->image,
        ];

        $listing->save();

        return $listing;
    }

    private function saveListingAttributesAndTags($listing, $request)
    {
        // Save attributes
        if ($request->filled('attributes_title')) {
            foreach ($request->input('attributes_title') as $index => $title) {
                $description = $request->input('attributes_description')[$index] ?? null;
                $sanitizedTitle = strip_tags($title);
                $sanitizedDescription = strip_tags($description);

                if (!is_null($title)) {
                    ListingAttribute::create([
                        'listing_id' => $listing->id,
                        'title' => $sanitizedTitle,
                        'description' => $sanitizedDescription,
                    ]);
                }
            }
        }

        // Save tags
        if ($request->filled('tags')) {
            foreach ($request->tags as $tagId) {
                ListingTag::create([
                    'listing_id' => $listing->id,
                    'tag_id' => $tagId,
                ]);
            }
        }
    }

    private function updateUserMembershipLimits($request)
    {
        if (moduleExists('Membership') && membershipModuleExistsAndEnable('Membership')) {
            $user_id = Auth::guard('web')->user()->id;

            // Decrement listing limit
            UserMembership::where('user_id', $user_id)->update([
                'listing_limit' => DB::raw(sprintf("listing_limit - %s", (int)strip_tags(1))),
            ]);

            // Decrement featured listing limit if applicable
            $user_membership_check = UserMembership::where('user_id', $user_id)->first();
            if ($user_membership_check->initial_featured_listing != 0 && !empty($request->is_featured)) {
                UserMembership::where('user_id', $user_id)->update([
                    'featured_listing' => DB::raw(sprintf("featured_listing - %s", (int)strip_tags(1))),
                ]);
            }
        }
    }

    private function notifyAdminAndSendEmail($listing, $request)
    {
        // Create admin notification
        AdminNotification::create([
            'identity' => $listing->id,
            'user_id' => Auth::guard('web')->user()->id,
            'type' => 'Create Listing',
            'message' => __('A new listing has been created'),
        ]);

        // Send email to admin if listing requires approval
        if (get_static_option('listing_create_status_settings') == 'pending') {
            try {
                $subject = get_static_option('listing_approve_subject') ?? __('New Listing Approve Request');
                $message = get_static_option('listing_approve_message');
                $message = str_replace(["@listing_id"], [$listing->id], $message);
                Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                    'subject' => $subject,
                    'message' => $message
                ]));
            } catch (\Exception $e) {
                // Handle exception silently
            }
        }
    }




    public function handleApi($request): array
{
    try {
        $this->validateMembershipAndVerificationApi($request);
        
        $validatedData = $this->validateRequestData($request);
        $listing = $this->prepareListingData($request, $validatedData);
        
        $this->saveListingAttributesAndTags($listing, $request);
        $this->updateUserMembershipLimits($request);
        $this->notifyAdminAndSendEmail($listing, $request);

        return [
            'status' => true,
            'code' => 201,
            'message' => __('Listing Added Success'),
            'data' => $listing
        ];

    } catch (\Illuminate\Validation\ValidationException $e) {
        throw $e;
    } catch (\Exception $e) {
        return [
            'status' => false,
            'code' => 500,
            'message' => $e->getMessage(),
            'data' => []
        ];
    }
}

private function validateMembershipAndVerificationApi($request): void
{
    $user = Auth::user();

    // التحقق من العضوية
    if (moduleExists('Membership') && membershipModuleExistsAndEnable('Membership')) {
        $userMembership = UserMembership::where('user_id', $user->id)->first();
        
        if (!$userMembership || $userMembership->status === 0 || $userMembership->payment_status === 'pending') {
            throw new \Exception(__('Your membership plan is inactive.'));
        }
    }

    // التحقق من الهوية
    if (get_static_option('listing_create_settings') === 'verified_user') {
        $identity = IdentityVerification::where('user_id', $user->id)->first();
        
        if (!$identity || $identity->status !== 1) {
            throw new \Exception(__('Account verification required.'));
        }
    }
}
}
