<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Models\Backend\Listing;
use App\Models\Backend\MediaUpload;
use App\Models\Frontend\GuestListing;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminGuestListingManageController extends Controller
{
    public function all_guest_listings(){
        $all_guest_listings = Listing::with('guestListing')->guestListings()->latest()->paginate(10);
        return view('backend.pages.listings.guest-listings.all_guest_listings', compact('all_guest_listings'));
    }

    // search guest listing
    public function searchListingGuest(Request $request)
    {
        $all_guest_listings = Listing::with('guestListing')->guestListings()->where('title', 'LIKE', "%". strip_tags($request->string_search) ."%")->latest()->paginate(10);
        return $all_guest_listings->total() >= 1 ? view('backend.pages.listings.guest-listings.all_guest_listings',
            compact('all_guest_listings'))->render() : response()->json(['status'=>__('nothing')]);
    }

    // guest listing pagination
    function paginateGuest(Request $request)
    {
        if($request->ajax()){
            $all_guest_listings = Listing::with('guestListing')->guestListings()->latest()->paginate(10);
            return view('backend.pages.listings.guest-listings.search-listing', compact('all_guest_listings'))->render();
        }
    }

    public function guestListingsAllApproved(){
        // Fetch listings to be approved and their associated guest emails
        $listings = Listing::with('guestListing')->guestListings()->where('status', 0)->get();
        $guestEmails = $listings->pluck('guestListing.email')->unique();

        // Update all listings in a single query
        $listingIds = $listings->pluck('id')->toArray();

        Listing::whereIn('id', $listingIds)->update([
            'published_at' => now(),
            'is_published' => 1,
            'status' => 1
        ]);

        // Split emails into batches of 100
        $emailChunks = $guestEmails->chunk(100);

        // Send email to each batch of guests
        foreach ($emailChunks as $chunk) {
            try {
                $subject = __('Your Listing approved and published.');
                $message = __('Your listing has been approved and published. Thanks.');

                // Add "View Listing" button with link to the email message
                foreach ($listings as $listing) {
                    $route = route('frontend.listing.details', $listing->slug);
                    $button = '<a href="' . $route . '"><button class="btn btn-info"
                                style="background-color: #17a2b8;
                                border: none;
                                color: white;
                                padding: 10px 20px;
                                text-align: center;
                                text-decoration: none;
                                display: inline-block;
                                font-size: 16px;
                                border-radius: 5px;
                                margin: 4px 2px;
                                cursor: pointer;">' . __('View Listing') . '</button></a>';
                    $message .= $button . '<br><br>';
                }

                foreach ($chunk as $email) {
                    Mail::to($email)->send(new BasicMail([
                        'subject' => $subject,
                        'message' => $message
                    ]));
                }

            } catch (\Exception $e) {
                // Handle exceptions if needed
            }
        }

        return redirect()->back()->with(FlashMsg::item_new(__('Guest All Listings Approved Success')));
    }


    public function listingGuestDelete($id){
        try {
            $listing = Listing::findOrFail($id);
            if ($listing->user_id === 0 && !empty($listing->guestListing)) {
                GuestListing::where('listing_id', $listing->id)->delete();
                MediaUpload::where('id', $listing->image)->delete();
                $gallery_images_ids = explode('|', $listing->gallery_images);
                MediaUpload::whereIn('id', $gallery_images_ids)->delete();
            }

            // Delete listing reports
            $listing->listingReports()->delete();
            // Delete listing tags
            $listing->listingTags()->delete();
            // Delete favorite listings
            $listing->listingFavorites()->delete();
            // Finally, delete the listing itself
            $listing->delete();

            return redirect()->back()->with(FlashMsg::item_delete(__('Listing Deleted Success')));
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', __('Listing not found.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('An error occurred while deleting the listing.'));
        }
    }

    public function bulkGuestAction(Request $request){
        Listing::guestListings()->whereIn('id',$request->ids)->delete();
        return response()->json(['status' => 'ok']);
    }


}
