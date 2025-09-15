<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Models\Backend\Admin;
use App\Models\Backend\Category;
use App\Models\Backend\ChildCategory;
use App\Models\Backend\IdentityVerification;
use App\Models\Backend\Listing;
use App\Models\Backend\ListingTag;
use App\Models\Backend\SubCategory;
use App\Models\Frontend\GuestListing;
use App\Models\Frontend\ListingAttribute;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\Blog\app\Models\Tag;
use Modules\Brand\app\Models\Brand;
use Modules\CountryManage\app\Models\City;
use Modules\CountryManage\app\Models\Country;
use Modules\CountryManage\app\Models\State;

class AdminListingController extends Controller
{

   public function adminAllListings(){
       $all_listings = Listing::adminListings()->latest()->paginate(10);
       return view('backend.pages.listings.admin-listings.admin-all-listings', compact('all_listings'));
   }

    public function adminChangeStatus($id){
        $listing = Listing::select('id','status')->where('id',$id)->first();
        if($listing->status==1){
            $status = 0;
        }else{
            $status = 1;
        }
        Listing::where('id',$id)->update(['status'=>$status]);
        return redirect()->back()->with(FlashMsg::item_new(__('Status Change Success')));
    }

    public function adminListingPublishedStatus($id)
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

        return redirect()->back();
    }

    public function adminListingDelete($id){
        try {
            $listing = Listing::findOrFail($id);
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
    public function adminSearchListing(Request $request)
    {
        $all_listings = Listing::adminListings()->where('title', 'LIKE', "%". strip_tags($request->string_search) ."%")->latest()->paginate(10);
        return $all_listings->total() >= 1 ? view('backend.pages.listings.admin-listings.search-listing',
            compact('all_listings'))->render() : response()->json(['status'=>__('nothing')]);
    }

    // pagination
    function adminPaginate(Request $request)
    {
        if($request->ajax()){
            $all_listings = Listing::adminListings()->latest()->paginate(10);
            return view('backend.pages.listings.admin-listings.search-listing', compact('all_listings'))->render();
        }
    }

    public function bulkAction(Request $request){
        Listing::adminListings()->whereIn('id',$request->ids)->delete();
        return response()->json(['status' => 'ok']);
    }

    public function adminAddListing(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'category_id' => 'required',
                'title' => 'required|max:191',
                'description' => 'required|min:150',
                'slug' => 'required|unique:listings',
                'price' => 'required|numeric',
                // Attributes Validation
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

            $admin = Admin::where('id', Auth::guard('admin')->user()->id)->first();
            $slug = !empty($request->slug) ? $request->slug : $request->title;

            if(get_static_option('listing_create_status_settings') == 'approved'){
                $status = 1;
            }else{
                $status = 0;
            }

            // video url
            $video_url = null;
            if(!empty($request->video_url)){
                $video_url = getYoutubeEmbedUrl($request->video_url);
            }


            // listing phone number
            $listing_phone = $request->country_code ?? $request->phone;;

            // Create a new listing
            $listing = new Listing();
            $listing->admin_id = $admin->id;
            $listing->category_id = $request->category_id;
            $listing->sub_category_id = $request->sub_category_id;
            $listing->child_category_id = $request->child_category_id;
            $listing->country_id = $request->country_id;
            $listing->state_id = $request->state_id;
            $listing->city_id = $request->city_id;
            $listing->brand_id = $request->brand_id;
            $listing->title = $request->title;
            $listing->slug = Str::slug(purify_html($slug),'-',null);
            $listing->description = $request->description;
            $listing->price = $request->price;
            $listing->negotiable = $request->negotiable ?? 0;
            $listing->phone = $listing_phone;
            $listing->phone_hidden = $request->phone_hidden ?? 0;
            $listing->condition = $request->condition;
            $listing->authenticity = $request->authenticity;
            $listing->image = $request->image;
            $listing->gallery_images = $request->gallery_images;
            $listing->video_url = $video_url;
            $listing->address = $request->address;
            $listing->is_featured =  $request->is_featured ?? 0;
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
            // Retrieve the last inserted ID
            $last_listing_id = $listing->id;

            // create Listing Attribute
            if ($request->filled('attributes_title')) {
                foreach ($request->input('attributes_title') as $index => $title) {
                    $description = $request->input('attributes_description')[$index] ?? null;
                    // Sanitize title and description
                    $sanitizedTitle = strip_tags($title);
                    $sanitizedDescription = strip_tags($description);
                    if(!is_null($sanitizedTitle) && !empty($sanitizedTitle)){
                        ListingAttribute::create([
                            'listing_id' => $last_listing_id,
                            'title' => $sanitizedTitle,
                            'description' => $sanitizedDescription,
                        ]);
                    }
                }
            }

            // create tags
            if ($request->filled('tags')) {
                foreach ($request->tags as $tagId) {
                    ListingTag::create([
                        'listing_id' => $last_listing_id,
                        'tag_id' => $tagId,
                    ]);
                }
            }

            return back()->with(toastr_success(__('Listing Added Success')));
        }


        $categories = Category::where('status', 1)->get();
        $sub_categories = SubCategory::where('status', 1);
        $all_countries = Country::all_countries();
        $all_states = State::all_states();
        $all_cities = City::all_cities();
        $tags = Tag::where('status', 'publish')->get();
        $user = Auth::guard('admin')->user();
        $brands = Brand::where('status', 1)->get();

        return view('backend.pages.listings.admin-listings.add-listing', compact('user', 'brands','categories', 'sub_categories', 'all_countries', 'all_states', 'all_cities', 'tags'));

    }

    public function adminEditListing(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'category_id' => 'required',
                'title' => 'required|max:191',
                'description' => 'required|min:150',
                'slug' => 'required|unique:listings,slug,' . $id . ',id',
                'price' => 'required|numeric',
                // Attributes Validation
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

            // country, state, city
            $admin = Admin::where('id', Auth::guard('admin')->user()->id)->first();
            $slug = !empty($request->slug) ? $request->slug : Str::slug($request->title);

            if(get_static_option('listing_create_status_settings') == 'approved'){
                $status = 1;
            }else{
                $status = 0;
            }

            // video url
            $video_url = null;
            if(!empty($request->video_url)){
                $video_url = getYoutubeEmbedUrl($request->video_url);
            }

            // listing phone number
            $listing_phone = $request->country_code ?? $request->phone;;

            // Edit listing
            $listing = Listing::with('listing_attributes')->findOrFail($id);
            $listing->admin_id = $admin->id;
            $listing->category_id = $request->category_id;
            $listing->sub_category_id = $request->sub_category_id;
            $listing->child_category_id = $request->child_category_id;
            $listing->country_id = $request->country_id;
            $listing->state_id = $request->state_id;
            $listing->city_id = $request->city_id;
            $listing->brand_id = $request->brand_id;
            $listing->title = $request->title;
            $listing->slug = Str::slug(purify_html($slug),'-',null);
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
            // Retrieve the last inserted ID
            $last_listing_id = $listing->id;


            // Edit attributes
            if ($listing->listing_attributes()->count() > 0) {
                $listing->listing_attributes()->delete();
            }
            if ($request->filled('attributes_title')) {
                foreach ($request->input('attributes_title') as $index => $title) {
                    $description = $request->input('attributes_description')[$index] ?? null;
                    $sanitizedTitle = strip_tags($title);
                    $sanitizedDescription = strip_tags($description);
                    if(!is_null($sanitizedTitle) && !empty($sanitizedTitle)){
                        ListingAttribute::create([
                            'listing_id' => $last_listing_id,
                            'title' => $sanitizedTitle,
                            'description' => $sanitizedDescription,
                        ]);
                    }
                }
            }

            // Edit tags
            if ($request->filled('tags')) {
                $listing->tags()->detach();
                foreach ($request->tags as $tagId) {
                    ListingTag::create([
                        'listing_id' => $last_listing_id,
                        'tag_id' => $tagId,
                    ]);
                }
            }

            return back()->with(toastr_success(__('Listing Updated Success')));
        }


        $listing = Listing::findOrFail($id);
        $categories = Category::where('status', 1)->get();
        $sub_categories = SubCategory::where('status', 1)->get();
        $child_categories = ChildCategory::where('status', 1)->get();
        $all_countries = Country::all_countries();
        $all_states = State::all_states();
        $all_cities = City::all_cities();
        $brands = Brand::where('status', 1)->get();
        $tags = Tag::where('status', 'publish')->get();

        return view('backend.pages.listings.admin-listings.edit-listing', compact('listing', 'brands', 'categories', 'sub_categories', 'child_categories','all_countries', 'all_states', 'all_cities', 'tags'));

    }
}
