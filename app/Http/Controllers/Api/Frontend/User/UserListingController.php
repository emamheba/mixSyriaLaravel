<?php

namespace App\Http\Controllers\Api\Frontend\User;

use App\Actions\Media\v1\MediaHelper;
use App\Exceptions\ListingRefreshException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\Listing\AddListingRequest;
use App\Http\Requests\Api\User\Listing\UpdateListingRequest;
use App\Http\Resources\Listing\ListingResource;
use App\Services\Listing\ListingRefreshService;
use App\Http\Responses\ApiResponse;
use App\Models\Backend\Listing;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserListingController extends Controller
{
  public function __construct(
    private MediaHelper $mediaHelper
  ) {
  }

  public function fetch(): JsonResponse
  {
    try {
      $listings = auth()->user()->listings()
        ->latest()
        ->get();

      return ApiResponse::success(
        'User listings retrieved successfully',
        ListingResource::collection($listings)
      );
    } catch (\Exception $e) {
      Log::error('Listings fetch error: ' . $e->getMessage());
      return ApiResponse::error(
        'Failed to retrieve listings',
        ['error' => $e->getMessage()],
        500
      );
    }
  }

  public function store(AddListingRequest $request): JsonResponse
  {
    $user = auth()->user();

    $creationPermission = get_static_option('listing_create_settings', 'all_user');

    if ($creationPermission === 'verified_user' && is_null($user->email_verified_at)) {
      return ApiResponse::error(
        'Only verified users are allowed to create listings. Please verify your email.',
        [],
        403
      );
    }

    $defaultStatusSetting = get_static_option('listing_create_status_settings', 'pending');

    $statusValue = 0;
    $isPublishedValue = 0;

    if ($defaultStatusSetting === 'approved') {
      $statusValue = 1;       // status = 1 (approved)
      $isPublishedValue = 1;  // is_published = 1 (published)
    }

    $mainImageId = null;
    $galleryImageIds = [];
    $listing = null;

    try {
      $listingData = $this->prepareListingData($request, $user, $statusValue, $isPublishedValue);

      $listing = Listing::create($listingData);

      $this->handleMediaUploads($request, $user, $listing, $mainImageId, $galleryImageIds);

      return ApiResponse::success(
        'Listing created successfully',
        ListingResource::make($listing)
      );

    } catch (\Exception $e) {
      $this->rollbackOperations($listing, $mainImageId, $galleryImageIds);
      Log::error('Listing creation error: ' . $e->getMessage());
      return ApiResponse::error(
        'Failed to create listing',
        ['error' => $e->getMessage()],
        500
      );
    }
  }

  public function update(UpdateListingRequest $request, Listing $listing): JsonResponse
  {
    DB::beginTransaction();

    try {
      $this->authorize('update', $listing);
      $user = auth()->user();

      $data = $this->prepareUpdateData($request);
      $oldMainImageId = null;

      // Handle main image
      if ($request->hasFile('image')) {
        $oldMainImageId = $listing->image;
        $mainImage = $this->mediaHelper->uploadMedia($request->file('image'), 'web');
        $data['image'] = $mainImage->id ?? null;
      }

      // Handle gallery images
      $currentGallery = $this->getCurrentGallery($listing);
      $newGallery = $this->processGalleryUpdates($request, $currentGallery, $user);

      $data['gallery_images'] = implode('|', $newGallery);
      $listing->update($data);

      DB::commit();

      // Cleanup old media
      $this->cleanupMedia($oldMainImageId, $request->input('deleted_images', []), $currentGallery);

      return ApiResponse::success(
        'Listing updated successfully',
        ListingResource::make($listing->fresh())
      );

    } catch (AuthorizationException $e) {
      return ApiResponse::error(
        'You are not authorized to update this listing',
        [],
        403
      );
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Listing update failed: ' . $e->getMessage());
      return ApiResponse::error(
        'Failed to update listing',
        ['error' => $e->getMessage()],
        500
      );
    }
  }

  public function refresh(Listing $listing): JsonResponse
  {
    try {
      $this->authorize('update', $listing);
      $refreshService = new ListingRefreshService();
      $refreshService->refresh($listing);

      return ApiResponse::success(
        'Listing refreshed successfully',
        ListingResource::make($listing->fresh()->load('mainImage', 'galleryImages'))
      );

    } catch (ListingRefreshException $e) {
      return ApiResponse::error(
        $e->getMessage(),
        [],
        400
      );
    } catch (AuthorizationException $e) {
      return ApiResponse::error(
        'You are not authorized to refresh this listing',
        [],
        403
      );
    } catch (\Exception $e) {
      Log::error('Listing refresh error: ' . $e->getMessage());
      return ApiResponse::error(
        'Failed to refresh listing',
        ['error' => $e->getMessage()],
        500
      );
    }
  }

  public function show(Listing $listing): JsonResponse
  {
    try {
      $this->authorize('view', $listing);
      return ApiResponse::success(
        'Listing retrieved successfully',
        ListingResource::make($listing->load('mainImage', 'galleryImages'))
      );
    } catch (AuthorizationException $e) {
      return ApiResponse::error(
        'You are not authorized to view this listing',
        [],
        403
      );
    } catch (\Exception $e) {
      Log::error('Listing view error: ' . $e->getMessage());
      return ApiResponse::error(
        'Failed to retrieve listing',
        ['error' => $e->getMessage()],
        500
      );
    }
  }

  public function destroy(Request $request, $id): JsonResponse
  {
    $listing = Listing::find($id);

    try {
      if (!$listing) {
        return ApiResponse::notFound('Listing not found');
      }
      $this->authorize('delete', $listing);
      $this->deleteListingMedia($listing);
      $listing->delete();

      return ApiResponse::success('Listing deleted successfully');
    } catch (AuthorizationException $e) {
      return ApiResponse::error(
        'You are not authorized to delete this listing',
        [],
        403
      );
    } catch (\Exception $e) {
      Log::error('Listing deletion error: ' . $e->getMessage());
      return ApiResponse::error(
        'Failed to delete listing',
        ['error' => $e->getMessage()],
        500
      );
    }
  }


  private function prepareListingData(AddListingRequest $request, $user, int $status, int $isPublished): array
  {
    $data = [
      'user_id' => $user->id,
      ...$request->validated(),
      'slug' => Str::slug($request->title),
      'negotiable' => $request->boolean('negotiable'),
      'phone_hidden' => $request->boolean('phone_hidden'),
      'status' => $status,
      'is_published' => $isPublished,
      'image' => null,
      'gallery_images' => null
    ];

    if ($isPublished === 1) {
      $data['published_at'] = now();
    }

    return $data;
  }
  private function prepareUpdateData(UpdateListingRequest $request): array
  {
    return [
      ...$request->validated(),
      'slug' => Str::slug($request->title),
      'negotiable' => $request->boolean('negotiable'),
      'phone_hidden' => $request->boolean('phone_hidden'),
    ];
  }

  private function handleMediaUploads($request, $user, Listing $listing, &$mainImageId, &$galleryImageIds): void
  {
    if ($request->hasFile('image')) {
      $mainImage = $this->mediaHelper->uploadMedia(
        $request->file('image'),
        'web',
        $user->id
      );
      $listing->update(['image' => $mainImage->id]);
      $mainImageId = $mainImage->id;
    }

    foreach ($request->file('gallery_images', []) as $image) {
      $galleryImage = $this->mediaHelper->uploadMedia($image, 'web', $user->id);
      $galleryImageIds[] = $galleryImage->id;
    }

    if (!empty($galleryImageIds)) {
      $listing->update(['gallery_images' => implode('|', $galleryImageIds)]);
    }
  }

  private function rollbackOperations(
    ?Listing $listing,
    ?int $mainImageId,
    array $galleryImageIds
  ): void {
    try {
      if ($listing)
        $listing->delete();
      if ($mainImageId)
        $this->mediaHelper->deleteMediaImage($mainImageId, 'web');
      foreach ($galleryImageIds as $id) {
        $this->mediaHelper->deleteMediaImage($id, 'web');
      }
    } catch (\Exception $e) {
      Log::critical('Rollback failed: ' . $e->getMessage());
    }
  }

  private function getCurrentGallery(Listing $listing): array
  {
    $gallery = $listing->getRawOriginal('gallery_images');
    return $gallery ? array_map('intval', explode('|', $gallery)) : [];
  }

  private function processGalleryUpdates($request, array $currentGallery, $user): array
  {
    $deletedImageIds = array_map('intval', (array) $request->input('deleted_images', []));
    $validDeletedIds = array_intersect($deletedImageIds, $currentGallery);
    $newGallery = array_diff($currentGallery, $validDeletedIds);

    if ($request->hasFile('gallery_images')) {
      foreach ($request->file('gallery_images') as $image) {
        $galleryImage = $this->mediaHelper->uploadMedia($image, 'web', $user->id);
        $newGallery[] = $galleryImage->id;
      }
    }

    return $newGallery;
  }

  private function cleanupMedia($oldMainImageId, $deletedImages, $currentGallery): void
  {
    try {
      if ($oldMainImageId) {
        $this->mediaHelper->deleteMediaImage($oldMainImageId, 'web');
      }

      $validDeletedIds = array_intersect(
        array_map('intval', (array) $deletedImages),
        $currentGallery
      );

      foreach ($validDeletedIds as $id) {
        $this->mediaHelper->deleteMediaImage($id, 'web');
      }
    } catch (\Exception $e) {
      Log::error('Media cleanup error: ' . $e->getMessage());
    }
  }

  private function deleteListingMedia(Listing $listing): void
  {
    try {
      if ($listing->image) {
        $this->mediaHelper->deleteMediaImage($listing->image, 'web');
      }

      if ($listing->gallery_images) {
        $ids = explode('|', $listing->gallery_images);
        foreach ($ids as $id) {
          $this->mediaHelper->deleteMediaImage($id, 'web');
        }
      }
    } catch (\Exception $e) {
      Log::error('Media deletion error: ' . $e->getMessage());
    }
  }
}
