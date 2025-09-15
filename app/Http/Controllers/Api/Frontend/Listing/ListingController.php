<?php

namespace App\Http\Controllers\Api\Frontend\Listing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Listing\ListingRequest;
use App\Http\Resources\Listing\ListingDetailsResource;
use App\Http\Resources\Listing\ListingResource;
use App\Http\Responses\ApiResponse;
use App\Models\Backend\Listing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class ListingController extends Controller
{
    public function index(ListingRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $query = Listing::query()
                ->with(['category', 'subcategory', 'childcategory', 'brand', 'tags'])
                ->filter($validated);

            if (($validated['sort'] ?? '') === 'nearest' && !isset($validated['at'], $validated['lon'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'User location is required for nearest sorting.',
                ], 422);
            }

            $perPage = $validated['per_page'] ?? 10;
            $listings = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Listings retrieved successfully.',
                'data' => ListingResource::collection($listings),
                'meta' => [
                    'current_page' => $listings->currentPage(),
                    'last_page' => $listings->lastPage(),
                    'total_items' => $listings->total(),
                    'per_page' => (int) $listings->perPage(),
                    'from' => $listings->firstItem(),
                    'to' => $listings->lastItem(),
                ]
            ]);

        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Server Error',
                'errors' => [$e->getMessage()],
                'data' => null
            ], 500);
        }
    }
    // public function show($id)
    // {
    //     try {
    //         $listing = Listing::with([
    //             'category',
    //             'subcategory',
    //             'childcategory',
    //             'brand',
    //             'tags',
    //             'user'
    //         ])->findOrFail($id);

    //         $listing->increment('view');

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Listing retrieved successfully',
    //             'data' => new ListingResource($listing)
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Listing not found',
    //             'errors' => [$e->getMessage()],
    //             'data' => null
    //         ], 404);
    //     }
    // }

    // public function index(ListingRequest $request)
    // {
    //     try {
    //         $validated = $request->validated();
    //         $query = Listing::withCount(['comments'])
    //             ->with([
    //                 'category',
    //                 'subcategory',
    //                 'childcategory',
    //                 'brand',
    //                 'tags',
    //                 'user' => function($q) {
    //                     $q->withCount('listings');
    //                 }
    //             ]);
    //         $query = $this->applyFilters($query, $validated);
    //         $query = $this->applySorting($query, $validated);
    //         $listings = $query->paginate($validated['per_page'] ?? 10);
    //         $listings->getCollection()->transform(function ($listing) use ($validated) {
    //             if (isset($validated['lat']) && isset($validated['lon'])) {
    //                 $listing->distance = $listing->distanceFrom(
    //                     $validated['lat'],
    //                     $validated['lon']
    //                 );
    //             }

    //             return $listing;
    //         });

    //         return ApiResponse::success(
    //             'Listings retrieved successfully',
    //             ListingResource::collection($listings)
    //         );
    //     } catch (\Exception $e) {
    //         return ApiResponse::error(
    //             'Error retrieving listings',
    //             [$e->getMessage()],
    //             500
    //         );
    //     }
    // }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        return $query ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('tags', function ($tagQuery) use ($search) {
                            $tagQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($filters['category_id'] ?? null, fn($q, $id) => $q->whereCategoryId($id))
            ->when($filters['sub_category_id'] ?? null, fn($q, $id) => $q->whereSubCategoryId($id))
            ->when($filters['child_category_id'] ?? null, fn($q, $id) => $q->whereChildCategoryId($id))

            ->when($filters['state_id'] ?? null, fn($q, $id) => $q->whereStateId($id))
            ->when($filters['city_id'] ?? null, fn($q, $id) => $q->whereCityId($id))
            ->when($filters['district_id'] ?? null, fn($q, $id) => $q->whereDistrictId($id))

            ->when(isset($filters['lat']) && isset($filters['lon']), function ($q) use ($filters) {
                return $q->filterByLocation(
                    $filters['lon'],
                    $filters['lat'],
                    $filters['radius'] ?? 10
                );
            })
            ->filterByPrice($filters['min_price'] ?? null, $filters['max_price'] ?? null)
            ->when($filters['condition'] ?? null, fn($q, $cond) => $q->whereCondition($cond))
            ->when($filters['product_condition'] ?? null, fn($q, $cond) => $q->whereProductCondition($cond))
            ->when($filters['listing_type'] ?? null, fn($q, $type) => $q->whereListingType($type))
            ->when($filters['featured'] ?? false, fn($q) => $q->whereFeatured(true))

            ->when($filters['verified_user'] ?? false, function ($q) {
                $q->whereHas('user', function ($userQuery) {
                    $userQuery->whereVerified(true);
                });
            })
            ->when($filters['with_images'] ?? false, fn($q) => $q->whereNotNull('image'))
            ->when($filters['brand_id'] ?? null, fn($q, $id) => $q->whereBrandId($id))
            ->when(!empty($filters['tags']), function ($q) use ($filters) {
                return $q->filterByTags($filters['tags']);
            });
    }

    private function applySorting(Builder $query, array $filters): Builder
    {
        if (!$filters['sort'] ?? null) {
            return $query->latest();
        }

        switch ($filters['sort']) {
            case 'newest':
                return $query->latest();
            case 'oldest':
                return $query->oldest();
            case 'price_asc':
                return $query->orderBy('price');
            case 'price_desc':
                return $query->orderByDesc('price');
            case 'popular':
                return $query->orderByDesc('view')
                        ->orderByDesc('created_at');
            case 'created_at':
                return $query->orderBy('created_at');
            case 'updated_at':
                return $query->orderByDesc('updated_at');
            default:
                return $query->latest();
        }
    }

    public function show(Listing $listing)
    {
        $listing->increment('view');
        return ApiResponse::success('Listing details', new ListingDetailsResource($listing->loadCount('comments')));
    }

    public function incrementViews(Listing $listing)
    {
        $listing->increment('view');

        return response()->noContent();
    }

    // public function getRelatedListings(Listing $listing)
    // {
    //     $relatedListings = $listing->getAlwifi()->get();
    //     return ApiResponse::success('Related listings retrieved', ListingResource::collection($relatedListings));
    // }

    public function getRelatedListings(Listing $listing)
    {
        $relatedListings = $listing->getRelatedListingsFromCache();
        return ApiResponse::success('Related listings retrieved', ListingResource::collection($relatedListings));
    }



    public function getFeaturedListings()
    {
        $featuredListings = Listing::where('is_featured', 1)->withCount('comments')->get();
        return ApiResponse::success('Featured listings retrieved', ListingResource::collection($featuredListings));
    }

    public function getTopRatedListings()
    {
        $topRatedListings = Listing::where('is_top_rated', 1)->withCount('comments')->get();
        return ApiResponse::success('Top rated listings retrieved', ListingResource::collection($topRatedListings));
    }

    public function getListingsByCategory($categoryId)
    {
        $listings = Listing::where('category_id', $categoryId)->withCount('comments')->get();
        return ApiResponse::success('Listings by category retrieved', ListingResource::collection($listings));
    }

    public function getLatestListings()
    {
        $latestListings = Listing::latest()->withCount('comments')->get();
        return ApiResponse::success('Latest listings retrieved', ListingResource::collection($latestListings));
    }

    public function getMostViewedListings()
    {
        $mostViewedListings = Listing::orderBy('view', 'desc')->withCount('comments')->get();
        return ApiResponse::success('Most viewed listings retrieved', ListingResource::collection($mostViewedListings));
    }

    public function searchListings(ListingRequest $request)
    {
        try {
            $validated = $request->validated();
            $query = Listing::withCount('comments')
                ->with(['category', 'subcategory', 'childcategory', 'brand', 'tags'])
                ->latest()
                ->filter($validated);

            $listings = $query->paginate($validated['per_page'] ?? 10);

            return ApiResponse::success('Search results retrieved', ListingResource::collection($listings));

        } catch (\Exception $e) {
            return ApiResponse::error('Unauthorized', [$e->getMessage()], 403);
        }
    }

    public function getRecommendedListings(Listing $listing)
    {
        $recommendedListings = $listing->getRecommendedListingsFromCache();
        return ApiResponse::success('Recommended listings retrieved', ListingResource::collection($recommendedListings));
    }
}
