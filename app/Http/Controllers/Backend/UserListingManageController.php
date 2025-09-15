<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Models\Backend\AdminNotification;
use App\Models\Backend\Listing;
use App\Models\Backend\ListingTag;
use App\Models\Common\ListingReport;
use App\Models\Frontend\GuestListing;
use App\Models\Frontend\ListingFavorite;
use App\Models\Frontend\Review;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserListingManageController extends Controller
{


  public function all_listings(Request $request)
  {
    $query = Listing::userListings()->with(['category', 'user'])->latest();

    if ($request->filled('search')) {
      $searchTerm = $request->search;
      $query->where(function ($q) use ($searchTerm) {
        $q->where('title', 'LIKE', "%{$searchTerm}%")
          ->orWhere('description', 'LIKE', "%{$searchTerm}%")
          ->orWhereHas('category', function ($categoryQuery) use ($searchTerm) {
            $categoryQuery->where('name', 'LIKE', "%{$searchTerm}%");
          })
          ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
            $userQuery->where('first_name', 'LIKE', "%{$searchTerm}%")
              ->orWhere('last_name', 'LIKE', "%{$searchTerm}%")
              ->orWhere('username', 'LIKE', "%{$searchTerm}%")
              ->orWhere('email', 'LIKE', "%{$searchTerm}%");
          });
      });
    }

    if ($request->filled('status') && $request->status !== 'all') {
      $query->where('status', $request->status);
    }
    if ($request->filled('published') && $request->published !== 'all') {
      $query->where('is_published', $request->published);
    }
    if ($request->filled('category') && $request->category !== 'all') {
      $query->where('category_id', $request->category);
    }
    if ($request->filled('date_from')) {
      $query->whereDate('created_at', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
      $query->whereDate('created_at', '<=', $request->date_to);
    }

    $all_listings = $query->paginate(10)->withQueryString();
    $stats = [
      'total' => $all_listings->total(),
      'pending' => $query->clone()->where('status', 0)->count(),
      'approved' => $query->clone()->where('status', 1)->count(),
      'published' => $query->clone()->where('is_published', 1)->count(),
      'unpublished' => $query->clone()->where('is_published', 0)->count(),
    ];
    $categories = \App\Models\Backend\Category::where('status', 1)->get();

    return view('backend.pages.listings.all_listings', compact('all_listings', 'stats', 'categories'));
  }


  public function listingDetails($id)
  {
    $listing = Listing::with('tags', 'brand', 'guestListing', 'listing_attributes')->find($id);
    if (!$listing) {
      abort(404);
    }

    AdminNotification::where('identity', $id)->update(['is_read' => 1]);

    return view('backend.pages.listings.listing-details', compact('listing'));
  }

  public function userListingsAllApproved()
  {

    // Fetch listings to be approved and their associated guest emails
    $listings = Listing::with('user')->userListings()->where('status', 0)->get();
    $userEmails = $listings->pluck('user.email')->unique();

    // Update all listings in a single query
    $listingIds = $listings->pluck('id')->toArray();

    Listing::whereIn('id', $listingIds)->update([
      'published_at' => now(),
      'is_published' => 1,
      'status' => 1
    ]);

    // Split emails into batches of 100
    $emailChunks = $userEmails->chunk(100);

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

    return redirect()->back()->with(FlashMsg::item_new(__('User All Listings Approved Success')));
  }

  public function changeStatus($id)
  {
    $listing = Listing::where('id', $id)->first();
    if ($listing->status == 1) {
      Listing::where('id', $id)->update(['status' => 0]);
    } else {
      Listing::where('id', $id)->update([
        'published_at' => now(),
        'is_published' => 1,
        'status' => 1,
      ]);
    }

    // if listing status approve/Pending email send
    if ($listing->user_id === 0) {
      // sent email to Guest
      try {
        $subject = get_static_option('guest_listing_approve_subject') ?? __('A new listing has been created by a guest and is awaiting your approval.');
        $message = get_static_option('guest_listing_approve_message') ?? __('Your listing has been approved. Thanks.');
        $message = str_replace(["@listing_id"], [$listing->id], $message);
        Mail::to($listing->guestListing?->email)->send(new BasicMail([
          'subject' => $subject,
          'message' => $message
        ]));
      } catch (\Exception $e) {
      }
    } else {
      // sent email to user
      try {
        $subject = get_static_option('listing_approve_subject') ?? __('A new listing has been created by a guest and is awaiting your approval.');
        $message = get_static_option('listing_approve_message') ?? __('Your listing has been approved. Thanks.');
        $message = str_replace(["@listing_id"], [$listing->id], $message);
        Mail::to($listing->user?->email)->send(new BasicMail([
          'subject' => $subject,
          'message' => $message
        ]));
      } catch (\Exception $e) {
      }
    }

    return redirect()->back()->with(FlashMsg::item_new(__('Status Change Success')));
  }

  public function listingPublishedStatus($id)
  {

    // First check if the listing exists
    $listing = Listing::find($id);
    if (!$listing) {
      $message = __('Listing not found.');
      toastr()->error($message);
      return redirect()->back();
    }

    // listing publication status
    $listing->is_published = !$listing->is_published;
    $listing->save();

    // Show appropriate message
    if ($listing->is_published) {
      // Listing is published
      $message = __('Listing has been successfully published.');
      toastr()->success($message);
    } else {
      // Listing is unpublished
      $message = __('Listing has been successfully unpublished.');
      toastr()->warning($message);
    }

    // if listing status approve/Pending email send
    if ($listing->user_id === 0) {
      // sent email to Guest
      try {
        $subject = get_static_option('guest_listing_publish_subject') ?? __('Your listing has been published.');
        $message = get_static_option('guest_listing_publish_message') ?? __('Your listing has been published. Thanks.');
        $message = str_replace(["@listing_id"], [$listing->id], $message);
        Mail::to($listing->guestListing?->email)->send(new BasicMail([
          'subject' => $subject,
          'message' => $message
        ]));
      } catch (\Exception $e) {
      }
    } else {
      if ($listing->is_published) {
        // sent email to user for listing Published
        try {
          $subject = get_static_option('listing_publish_subject') ?? __('Your listing has been published.');
          $message = get_static_option('listing_publish_message') ?? __('Your listing has been published. Thanks.');
          $message = str_replace(["@listing_id"], [$listing->id], $message);
          Mail::to($listing->user?->email)->send(new BasicMail([
            'subject' => $subject,
            'message' => $message
          ]));
        } catch (\Exception $e) {
        }
      } else {
        // sent email to user for listing Unpublished
        try {
          $subject = get_static_option('listing_unpublished_subject') ?? __('Your listing has been unpublished.');
          $message = get_static_option('listing_unpublished_message') ?? __('Your listing has been unpublished. Thanks.');
          $message = str_replace(["@listing_id"], [$listing->id], $message);
          Mail::to($listing->user?->email)->send(new BasicMail([
            'subject' => $subject,
            'message' => $message
          ]));
        } catch (\Exception $e) {
        }
      }
    }

    return redirect()->back();
  }

  public function listingDelete($id)
  {
    try {
      $listing = Listing::findOrFail($id);

      // Delete guest listings if the user_id is 0
      if ($listing->user_id === 0 && !empty($listing->guestListing)) {
        $listing->guestListing()->delete();
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

  // search category
  // public function searchListing(Request $request)
  // {
  //     $all_listings = Listing::userListings()->where('title', 'LIKE', "%". strip_tags($request->string_search) ."%")->latest()->paginate(10);
  //     return $all_listings->total() >= 1 ? view('backend.pages.listings.search-listing',
  //         compact('all_listings'))->render() : response()->json(['status'=>__('nothing')]);
  // }

  // pagination
  // function paginate(Request $request)
  // {
  //     if($request->ajax()){
  //         $all_listings = Listing::userListings()->latest()->paginate(10);
  //         return view('backend.pages.listings.search-listing', compact('all_listings'))->render();
  //     }
  // }

  public function bulkAction(Request $request)
  {
    Listing::userListings()->whereIn('id', $request->ids)->delete();
    return response()->json(['status' => 'ok']);
  }

}
