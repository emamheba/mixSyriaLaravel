<?php

namespace app\Http\Services;
use App\Models\Backend\Category;
use App\Models\Backend\ChildCategory;
use App\Models\Backend\Listing;
use App\Models\Backend\Page;
use App\Models\Backend\SubCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Blog\app\Models\Tag;
use Modules\Brand\app\Models\Brand;
use Modules\CountryManage\app\Models\City;
use Modules\CountryManage\app\Models\Country;
use Modules\CountryManage\app\Models\State;
use Modules\Membership\app\Models\UserMembership;

class ListingService
{
    public function prepareListingData(array $validated, $user): array
    {
        $slug = !empty($validated['slug']) ? $validated['slug'] : $validated['title'];
        $status = (get_static_option('listing_create_status_settings') == 'approved') ? 1 : 0;
        $video_url = !empty($validated['video_url']) ? getYoutubeEmbedUrl($validated['video_url']) : null;
        $listing_phone = $validated['country_code'] ?? $validated['phone'] ?? null;

        return [
            'user_id'           => $user->id,
            'category_id'       => $validated['category_id'],
            'sub_category_id'   => $validated['sub_category_id'] ?? null,
            'child_category_id' => $validated['child_category_id'] ?? null,
            'country_id'        => $validated['country_id'] ?? null,
            'state_id'          => $validated['state_id'] ?? null,
            'city_id'           => $validated['city_id'] ?? null,
            'brand_id'          => $validated['brand_id'] ?? null,
            'title'             => $validated['title'],
            'slug'              => Str::slug(purify_html($slug), '-', null),
            'description'       => $validated['description'],
            'price'             => $validated['price'],
            'negotiable'        => $validated['negotiable'] ?? 0,
            'condition'         => $validated['condition'] ?? null,
            'authenticity'      => $validated['authenticity'] ?? null,
            'phone'             => $listing_phone,
            'phone_hidden'      => $validated['phone_hidden'] ?? 0,
            'image'             => $validated['image'] ?? null,
            'gallery_images'    => $validated['gallery_images'] ?? null,
            'video_url'         => $video_url,
            'address'           => $validated['address'] ?? null,
            'lat'               => $validated['latitude'] ?? null,
            'lon'               => $validated['longitude'] ?? null,
            'is_featured'       => $validated['is_featured'] ?? 0,
            'status'            => $status,
        ];
    }

    public function prepareListingEditData(Listing $listing): array
    {
        return [
            'listing'                      => $listing,
            'brands'                       => Brand::where('status', 1)->get(),
            'categories'                   => Category::where('status', 1)->get(),
            'sub_categories'               => SubCategory::where('status', 1)->get(),
            'child_categories'             => ChildCategory::where('status', 1)->get(),
            'all_countries'                => Country::all_countries(),
            'all_states'                   => State::all_states(),
            'all_cities'                   => City::all_cities(),
            'tags'                         => Tag::where('status', 'publish')->get(),
            'membership_page_url'          => $this->getMembershipPageUrl(),
            'user_featured_listing_enable' => $this->checkUserFeaturedListingStatus(),
            'user_listing_limit_check'     => $this->checkUserListingLimit(),
        ];
    }

    public function sanitizeListingAttributes(array $validated): array
    {
        $attributes = [];
        foreach ($validated['attributes_title'] as $index => $title) {
            $description = $validated['attributes_description'][$index] ?? null;
            $sanitizedTitle = strip_tags($title);
            $sanitizedDescription = strip_tags($description);
            
            if (!empty($sanitizedTitle)) {
                $attributes[] = [
                    'title'       => $sanitizedTitle,
                    'description' => $sanitizedDescription,
                ];
            }
        }
        return $attributes;
    }

    private function getMembershipPageUrl(): string
    {
        return get_static_option('membership_plan_page')
            ? Page::select('slug')->find(get_static_option('membership_plan_page'))->slug
            : '';
    }

    private function checkUserFeaturedListingStatus(): bool
    {
        if (!moduleExists('Membership') || !membershipModuleExistsAndEnable('Membership')) {
            return false;
        }

        $user_membership = UserMembership::where('user_id', Auth::guard('api')->user()->id)->first();
        return !empty($user_membership) ? ($user_membership->featured_listing != 0) : false;
    }

    private function checkUserListingLimit(): bool
    {
        if (!moduleExists('Membership') || !membershipModuleExistsAndEnable('Membership')) {
            return false;
        }

        $user_membership = UserMembership::where('user_id', Auth::guard('api')->user()->id)->first();
        return !empty($user_membership) ? ($user_membership->listing_limit === 0) : false;
    }
}