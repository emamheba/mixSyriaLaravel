<?php

namespace App\Http\Controllers\Api\Frontend\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Listing\ListingResource;
use App\Http\Resources\User\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\Backend\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use phpDocumentor\Reflection\Types\Boolean;
use App\Actions\Media\v1\MediaHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class ProfileController extends Controller
{

     public function __construct(
        private MediaHelper $mediaHelper
    ) {
    }

    public function fetch(Request $request)
    {
        return ApiResponse::success('succssfully', new UserResource($request->user()));
    }
      public function update(Request $request)
    {
        $user = $request->user();
        $oldImageId = null;

        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'username' => 'nullable|string|unique:users,username,' . $user->id,
            'phone' => 'required|unique:users,phone,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'address' => 'nullable|string|max:255',
            'about' => 'nullable|string|max:5000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::beginTransaction();

        try {
            if ($request->hasFile('image')) {
                $oldImageId = $user->getRawOriginal('image');
                $newImage = $this->mediaHelper->uploadMedia($request->file('image'), 'web');
                if ($newImage) {
                    $data['image'] = $newImage->id;
                }
            }

            $user->update($data);

            DB::commit();

            if ($oldImageId) {
                try {
                    $this->mediaHelper->deleteMediaImage($oldImageId, 'web');
                } catch (\Exception $e) {
                    Log::error("Failed to delete old profile image ID {$oldImageId}: " . $e->getMessage());
                }
            }

            return ApiResponse::success('Profile updated successfully', new UserResource($user->fresh()));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Profile update failed: ' . $e->getMessage());
            return ApiResponse::error('Failed to update profile.', [], 500);
        }
    }


    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($data['old_password'], $request->user()->password)) {
            return ApiResponse::error('Password is invild', [], 401);
        }

        $request->user()->update([
            'password' => bcrypt($data['password']),
        ]);

        return ApiResponse::success('succssfully');
    }


    public function deleteAccount(Request $request)
    {
        $request->user()->delete();

        return ApiResponse::success('succssfully');
    }


    // public function getFavorites(Request $request)
    // {
    //     $favorites = $request->user()->listingFavorite()->with('listing')->get();
    //     $listings = $favorites->pluck('listing');

    //     // $listings = $favorites->map(function($favorite){
    //     //     return $favorite->listing;
    //     // });
    //     return ApiResponse::success('succssfully', ListingResource::collection($listings));
    // }

    public function getFavorites(Request $request)
    {
        $favorites = $request->user()->listingFavorite()->with('listing')->get();

        $listings = $favorites->map(function($favorite) {
            return $favorite->listing;
        })->filter();

        return ApiResponse::success('successfully', ListingResource::collection($listings));
    }

    public function setFavorite(Request $request, $listingId)
    {
        $listing = Listing::find($listingId);

        if(!$listing) {
            return ApiResponse::error('Listing not found', [], 404);
        }

        if($request->user()->listingFavorite()->where('listing_id', $listingId)->first()) {
            return ApiResponse::error('Listing is favorited', [], 302);
        }

        $favorites = $request->user()->listingFavorite()->create([
            'listing_id' => $listingId,
        ]);

        return ApiResponse::success('succssfully', [
            'favorites' => $favorites
        ]);
    }



    public function unfavorite(Request $request, $listingId)
    {
        $favorite = $request->user()->listingFavorite()->where('listing_id', $listingId)->first();

        if (!$favorite) {
            return ApiResponse::error('Favorite not found', [], 404);
        }

        $favorite->delete();

        return ApiResponse::success('succssfully');
    }

    public function checkIsFavorites(Request $request, $listingId)
    {
        $favorite = $request->user()->listingFavorite()->where('listing_id', $listingId)->first();

        if(!$favorite) {
            return ApiResponse::error('Favorite not found', [], 404);
        }
        return ApiResponse::success('succssfully');
    }


    public function updateLocation(Request $request)
    {
        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lon' => 'required|numeric|between:-180,180',
        ]);

        $user = request()->user();
        $user->lat = $validated['lat'];
        $user->lon = $validated['lon'];
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User location updated successfully.',
            'user' => $user,
        ]);
    }

}
