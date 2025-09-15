<?php

namespace App\Repositories\Frontend\GuestListing;

use App\Http\Requests\Api\GuestListing\StoreGuestListingRequest;
use App\Models\Backend\Listing;
use App\Models\Backend\ListingTag;
use App\Models\Frontend\GuestListing;
use App\Models\Frontend\ListingAttribute;
use App\Repositories\Main\EloquentMainRepository;
use Illuminate\Support\Str;

class GuestListingRepository extends EloquentMainRepository
{
    public function __construct(Listing $model)
    {
        parent::__construct($model);
    }

    /**
     * Create a new listing
     * 
     * @param StoreGuestListingRequest $request
     * @param mixed $user
     * @return Listing
     */
    public function createListing(StoreGuestListingRequest $request, $user = null): Listing
    {
        $slug = !empty($request->slug) ? $request->slug : $request->title;
        
        // Determine listing status based on settings
        $status = (get_static_option('listing_create_status_settings') == 'approved') ? 1 : 0;
        
        // Process video URL if provided
        $videoUrl = null;
        if (!empty($request->video_url)) {
            $videoUrl = getYoutubeEmbedUrl($request->video_url);
        }
        
        // Format phone number with country code
        $listingPhone = $request->country_code ?? $request->phone;
        
        // Create listing data array
        $listingData = [
            'user_id' => $user ? $user->id : 0,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'child_category_id' => $request->child_category_id,
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'brand_id' => $request->brand_id ?? 0,
            'title' => $request->title,
            'slug' => Str::slug(purify_html($slug), '-', null),
            'description' => $request->description,
            'price' => $request->price,
            'negotiable' => $request->negotiable ?? 0,
            'phone' => $listingPhone,
            'phone_hidden' => $request->phone_hidden ?? 0,
            'condition' => $request->condition,
            'authenticity' => $request->authenticity,
            'image' => $request->image,
            'gallery_images' => $request->gallery_images,
            'video_url' => $videoUrl,
            'address' => $request->address,
            'lat' => $request->latitude,
            'lon' => $request->longitude,
            'is_featured' => $request->is_featured ?? 0,
            'status' => $status
        ];
        
        // Create the listing
        return $this->store($listingData);
    }
    
    /**
     * Create listing attributes
     * 
     * @param int $listingId
     * @param StoreGuestListingRequest $request
     * @return void
     */
    public function createListingAttributes(int $listingId, StoreGuestListingRequest $request): void
    {
        foreach ($request->input('attributes_title') as $index => $title) {
            $description = $request->input('attributes_description')[$index] ?? null;
            
            // Sanitize inputs
            $sanitizedTitle = strip_tags($title);
            $sanitizedDescription = strip_tags($description);
            
            if (!is_null($sanitizedTitle) && !empty($sanitizedTitle)) {
                ListingAttribute::create([
                    'listing_id' => $listingId,
                    'title' => $sanitizedTitle,
                    'description' => $sanitizedDescription,
                ]);
            }
        }
    }
    
    /**
     * Create listing tags
     * 
     * @param int $listingId
     * @param array $tagIds
     * @return void
     */
    public function createListingTags(int $listingId, array $tagIds): void
    {
        foreach ($tagIds as $tagId) {
            ListingTag::create([
                'listing_id' => $listingId,
                'tag_id' => $tagId,
            ]);
        }
    }
    
    /**
     * Create guest listing record
     * 
     * @param int $listingId
     * @param StoreGuestListingRequest $request
     * @return GuestListing
     */
    public function createGuestListingRecord(int $listingId, StoreGuestListingRequest $request): GuestListing
    {
        $guestPhoneNumber = $request->guest_country_code ?? $request->guest_phone;
        
        return GuestListing::create([
            'listing_id' => $listingId,
            'first_name' => $request->guest_first_name,
            'last_name' => $request->guest_last_name,
            'email' => $request->guest_email,
            'phone' => $guestPhoneNumber,
            'status' => 0,
            'terms_condition' => 1,
        ]);
    }
}