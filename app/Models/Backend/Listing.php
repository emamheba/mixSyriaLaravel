<?php

namespace App\Models\Backend;

use App\Models\Common\ListingReport;
use App\Models\Frontend\GuestListing;
use App\Models\Frontend\ListingAttribute;
use App\Models\Frontend\ListingFavorite;
use App\Models\ListingPromotion;
use App\Models\Location\District;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Blog\app\Models\Tag;
use Modules\Brand\app\Models\Brand;
use Modules\CountryManage\app\Models\City;
use Modules\CountryManage\app\Models\Country;
use Modules\CountryManage\app\Models\State;

class Listing extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'listings';
    protected $fillable = [
        'user_id',
        'admin_id',
        'category_id',
        'sub_category_id',
        'child_category_id',
        'brand_id',
        'country_id',
        'state_id',
        'city_id',
        'district_id',
        'title',
        'slug',
        'description',
        'image',
        'gallery_images',
        'video_url',
        'price',
        'negotiable',
        'condition',
        'contact_name',
        'email',
        'phone',
        'phone_hidden',
        'address',
        'lon',
        'lat',
        'is_featured',
        'promoted_until',
        'view',
        'status',
        'is_published',
        'published_at',
        'listing_type',
        'expire_at'
    ];

    protected $casts = [
        'status' => 'integer',
    ];
    public function listing_attributes()
    {
        return $this->hasMany(ListingAttribute::class);
    }
    public function listing_creator()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Backend\Category');
    }

    public function subcategory()
    {
        return $this->belongsTo(\App\Models\Backend\SubCategory::class, 'sub_category_id', 'id');
    }

    public function childcategory()
    {
        return $this->belongsTo(ChildCategory::class, 'child_category_id', 'id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function metaData()
    {
        return $this->morphOne(MetaData::class, 'meta_taggable');
    }

    // protected function image(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($value) => get_attachment_image_by_id($value, 'thumb')['img_url'] ?? '',
    //     );
    // }
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn($value) => [
                'image_id' => $value,
                'image_url' => get_attachment_image_by_id($value)['img_url'] ?? '',
            ]
        );
    }
    protected function mainImage(): Attribute
    {
        return Attribute::make(
            get: fn($value) => [
                'image_id' => $value,
                'image_url' => get_attachment_image_by_id($value)['img_url'] ?? '',
            ]
        );
    }

    public function listingCity()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'listing_tags', 'listing_id', 'tag_id');
    }

    public function scopeAdminListings($query)
    {
        return $query->whereNotNull('admin_id');
    }

    public function scopeUserListings($query)
    {
        return $query->whereNotNull('user_id')->where('user_id', '!=', 0)->where('admin_id', null);
    }

    public function scopeGuestListings($query)
    {
        return $query->where('user_id', 0);
    }

    public function listingReports()
    {
        return $this->hasMany(ListingReport::class, 'listing_id');
    }

    public function listingTags()
    {
        return $this->hasMany(ListingTag::class, 'listing_id');
    }

    public function listingFavorites()
    {
        return $this->hasMany(ListingFavorite::class, 'listing_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function user_membership()
    {
        return $this->belongsTo('\Modules\Membership\app\Models\UserMembership', 'user_id', 'user_id')
            ->where('expire_date', '>=', now())
            ->where('user_id', '!=', 0);
    }
    public function guestListing()
    {
        return $this->hasOne(GuestListing::class, 'listing_id', 'id');
    }
    // public function scopeFilter($query, array $filters)
    // {
    //     return $query
    //         ->when($filters['search'] ?? null, fn($q, $search) => $this->applySearchFilter($q, $search))
    //         ->when($filters['category_id'] ?? null, fn($q, $id) => $q->whereCategoryId($id))
    //         ->when($filters['sub_category_id'] ?? null, fn($q, $id) => $q->whereSubCategoryId($id))
    //         ->when($filters['child_category_id'] ?? null, fn($q, $id) => $q->whereChildCategoryId($id))
    //         ->when($filters['brand_id'] ?? null, fn($q, $id) => $q->whereBrandId($id))
    //         ->when($filters['state_id'] ?? null, fn($q, $id) => $q->whereStateId($id))
    //         ->when($filters['city_id'] ?? null, fn($q, $id) => $q->whereCityId($id))
    //         ->when($filters['district_id'] ?? null, fn($q, $id) => $q->whereDistrictId($id))
    //         ->when($filters['tags'] ?? null, fn($q, $tags) => $q->filterByTags($tags))
    //         ->when(
    //             isset($filters['lat'], $filters['lon']),
    //             fn($q) => $this->applyLocationFilters($q, $filters)
    //         )
    //         ->when($filters['min_price'] ?? null, fn($q) => $q->where('price', '>=', $filters['min_price']))
    //         ->when($filters['max_price'] ?? null, fn($q) => $q->where('price', '<=', $filters['max_price']))
    //         ->when($filters['condition'] ?? null, fn($q, $cond) => $q->where('condition', $cond))
    //         ->when($filters['listing_type'] ?? null, fn($q, $type) => $q->whereListingType($type))
    //         ->when(isset($filters['featured']), fn($q) => $q->where('is_featured', (bool) $filters['featured']))
    //         ->when(isset($filters['verified_user']), fn($q) => $q->whereHas('user', fn($u) => $u->whereNotNull('verified_at')))
    //         ->when(isset($filters['with_images']), fn($q) => $this->applyImagesFilter($q))
    //         ->when(
    //             $filters['sort'] ?? 'newest',
    //             fn($q, $sort) => $this->applySorting($q, $sort, $filters)
    //         )
    //         ->when($filters['user_id'] ?? null, fn($q, $id) => $q->whereUserId($id));
    // }

    // private function applySearchFilter($query, $search)
    // {
    //     return $query->where(function ($q) use ($search) {
    //         $q->where('title', 'like', "%{$search}%")
    //             ->orWhere('description', 'like', "%{$search}%")
    //             ->orWhereHas('tags', fn($t) => $t->where('name', 'like', "%{$search}%"));
    //     });
    // }

    // private function applyImagesFilter($query)
    // {
    //     return $query->where(function ($q) {
    //         $q->whereNotNull('image')
    //             ->orWhereNotNull('gallery_images')
    //             ->where('gallery_images', '<>', '');
    //     });
    // }

    // private function applyLocationFilters($query, $filters)
    // {
    //     $lat = $filters['lat'];
    //     $lon = $filters['lon'];
    //     $radius = $filters['radius'] ?? 10;

    //     if ($lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
    //         abort(422, 'Invalid coordinates');
    //     }

    //     $minLat = $lat - ($radius / 111.12);
    //     $maxLat = $lat + ($radius / 111.12);
    //     $minLon = $lon - ($radius / (111.12 * cos(deg2rad($lat))));
    //     $maxLon = $lon + ($radius / (111.12 * cos(deg2rad($lat))));

    //     $query->whereBetween('lat', [$minLat, $maxLat])
    //         ->whereBetween('lon', [$minLon, $maxLon])
    //         ->selectRaw("
    //                 listings.*,
    //                 ST_Distance_Sphere(
    //                     POINT(?, ?),
    //                     POINT(listings.lon, listings.lat)
    //                 ) / 1000 AS distance_km
    //             ", [$lon, $lat])
    //             ->whereRaw("
    //                 ST_Distance_Sphere(
    //                     POINT(?, ?),
    //                     POINT(listings.lon, listings.lat)
    //                 ) <= ?
    //             ", [$lon, $lat, $radius * 1000]);

    //     return $query;
    // }

    // private function applySorting($query, $sortType, $filters)
    // {
    //     if ($sortType === 'nearest' && isset($filters['lat'], $filters['lon'])) {
    //         return $query->orderBy('distance_km');
    //     }

    //     return match ($sortType) {
    //         'newest' => $query->latest(),
    //         'oldest' => $query->oldest(),
    //         'price_asc' => $query->orderBy('price'),
    //         'price_desc' => $query->orderByDesc('price'),
    //         'popular' => $query->orderByDesc('view'),
    //         'created_at' => $query->orderByDesc('created_at'),
    //         'updated_at' => $query->orderBy('created_at'),
    //         default => $query->latest(),
    //     };
    // }


    // public function distanceFrom(float $userLat, float $userLon): ?float
    // {
    //     if (!$this->lat || !$this->lon) {
    //         return null;
    //     }

    //     $result = DB::selectOne("
    //         SELECT ST_Distance_Sphere(
    //             POINT(?, ?),
    //             POINT(?, ?)
    //         ) AS distance
    //     ", [$userLon, $userLat, $this->lon, $this->lat]);

    //     return round($result->distance / 1000, 2);
    // }




    // public function user() { return $this->belongsTo(User::class); }
    // public function category() { return $this->belongsTo(Category::class); }
    // public function subcategory() { return $this->belongsTo(Category::class, 'sub_category_id'); }
    // public function childcategory() { return $this->belongsTo(Category::class, 'child_category_id'); }
    // public function brand() { return $this->belongsTo(Brand::class); }
    // public function tags() { return $this->belongsToMany(Tag::class); }
    // public function state() { return $this->belongsTo(State::class); }
    // public function city() { return $this->belongsTo(City::class); }
    // public function district() { return $this->belongsTo(District::class); }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['search'] ?? null, fn($q, $search) => $q->applySearchFilter($search))
            ->when($filters['category_id'] ?? null, fn($q, $id) => $q->where('category_id', $id))
            ->when($filters['sub_category_id'] ?? null, fn($q, $id) => $q->where('sub_category_id', $id))
            ->when($filters['child_category_id'] ?? null, fn($q, $id) => $q->where('child_category_id', $id))
            ->when($filters['brand_id'] ?? null, fn($q, $id) => $q->where('brand_id', $id))
            ->when($filters['state_id'] ?? null, fn($q, $id) => $q->where('state_id', $id))
            ->when($filters['city_id'] ?? null, fn($q, $id) => $q->where('city_id', $id))
            ->when($filters['district_id'] ?? null, fn($q, $id) => $q->where('district_id', $id))
            ->when($filters['tags'] ?? null, fn($q, $tags) => $q->filterByTags($tags))
            ->when(isset($filters['lat'], $filters['lon']), fn($q) => $q->applyGeoFeatures($filters))
            ->when($filters['min_price'] ?? null, fn($q, $price) => $q->where('price', '>=', $price))
            ->when($filters['max_price'] ?? null, fn($q, $price) => $q->where('price', '<=', $price))
            ->when($filters['condition'] ?? null, fn($q, $cond) => $q->where('condition', $cond))
            ->when($filters['listing_type'] ?? null, fn($q, $type) => $q->where('listing_type', $type))
            ->when(isset($filters['featured']), fn($q, $val) => $q->where('is_featured', (bool) $val))
            ->when(isset($filters['verified_user']), fn($q) => $q->whereHas('user', fn($u) => $u->whereNotNull('verified_at')))
            ->when(isset($filters['with_images']), fn($q) => $q->applyImagesFilter())
            ->when($filters['user_id'] ?? null, fn($q, $id) => $q->where('user_id', $id))
            ->when($filters['sort'] ?? 'newest', fn($q, $sort) => $q->applySorting($sort, $filters));
    }

    public function scopeApplySearchFilter($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhereHas('tags', fn($t) => $t->where('name', 'like', "%{$search}%"));
        });
    }

    public function scopeFilterByTags($query, array $tags)
    {
        return $query->whereHas('tags', fn($q) => $q->whereIn('tags.id', $tags));
    }

    public function scopeApplyImagesFilter($query)
    {
        return $query->where(function ($q) {
            $q->whereNotNull('image')->orWhere(function ($sub) {
                $sub->whereNotNull('gallery_images')->where('gallery_images', '<>', '[]');
            });
        });
    }

    public function scopeApplyGeoFeatures($query, array $filters)
    {
        $userLat = (float) $filters['lat'];
        $userLon = (float) $filters['lon'];

        $safeDistanceExpression = "
            CASE
                WHEN listings.lat BETWEEN -90 AND 90 AND listings.lon BETWEEN -180 AND 180
                THEN ST_Distance_Sphere(POINT(?, ?), POINT(listings.lon, listings.lat))
                ELSE NULL
            END
        ";

        $query->addSelect(
            DB::raw("listings.*, ({$safeDistanceExpression}) / 1000 AS distance_km")
        )->addBinding([$userLon, $userLat], 'select');

        if (isset($filters['radius']) && is_numeric($filters['radius'])) {
            $radiusInMeters = (float) $filters['radius'] * 1000;
            $query->whereRaw("({$safeDistanceExpression}) <= ?", [$userLon, $userLat, $radiusInMeters]);
        }

        return $query;
    }

    public function scopeApplySorting($query, $sortType, array $filters)
    {
        if ($sortType === 'nearest' && isset($filters['lat'], $filters['lon'])) {
            return $query->orderBy('distance_km');
        }

        return match ($sortType) {
            'oldest' => $query->oldest(),
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'popular' => $query->orderByDesc('view'),
            'updated_at' => $query->orderBy('created_at'),
            'newest' => $query->latest(),
            default => $query->latest(),
        };
    }

    // private function applySearchFilter($query, $search)
    // {
    //     return $query->where(function ($q) use ($search) {
    //         $q->where('title', 'like', "%{$search}%")
    //             ->orWhere('description', 'like', "%{$search}%")
    //             ->orWhereHas('tags', fn($t) => $t->where('name', 'like', "%{$search}%"));
    //     });
    // }

    // private function applyImagesFilter($query)
    // {
    //     return $query->where(function ($q) {
    //         $q->whereNotNull('image')
    //             ->orWhere(function ($subQuery) {
    //                 $subQuery->whereNotNull('gallery_images')
    //                         ->where('gallery_images', '<>', '[]');
    //             });
    //     });
    // }


    // public function scopeFilterByTags($query, array $tags)
    // {
    //     return $query->whereHas('tags', fn($q) => $q->whereIn('id', $tags));
    // }

    public function getIsCurrentlyPromotedAttribute(): bool
    {
        return $this->is_featured && $this->promoted_until && $this->promoted_until > Carbon::now();
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getGalleryUrlsAttribute()
    {
        return collect($this->gallery_images)->map(function ($image) {
            return asset('storage/' . $image);
        })->toArray();
    }

    public function getRelatedListings($limit = 15)
    {
        return Cache::remember("related_listings_{$this->id}", 3600, function () use ($limit) {
            return self::query()
                ->where('category_id', $this->category_id)
                ->where('id', '!=', $this->id)
                ->where('is_published', 1)
                ->latest()
                ->limit($limit)
                ->get();
        });
    }



    public function relatedListingsQuery()
    {
        return $this->hasMany(self::class, 'category_id', 'category_id')
            ->where('id', '!=', $this->id)
            ->where('is_published', 1);
    }


    public function relatedListings()
    {
        return Cache::remember("related_listings_{$this->id}", 3600, function () {
            return $this->relatedListingsQuery()
                ->latest()
                ->take(15)
                ->get();
        });
    }

    public function getRelatedListingsFromCache()
    {
        return Cache::remember("related_listings_{$this->id}", 3600, function () {
            return self::query()
                ->where('category_id', $this->category_id)
                ->where('id', '!=', $this->id)
                ->where('is_published', 1)
                ->latest()
                ->take(15)
                ->get();
        });
    }


    // public function distanceFrom(float $userLat, float $userLon): ?float
    // {
    //     if (is_null($this->lat) || is_null($this->lon)) {
    //         return null;
    //     }

    //     $result = \DB::selectOne("
    //         SELECT ST_Distance_Sphere(
    //             POINT(?, ?),
    //             POINT(?, ?)
    //         ) AS distance
    //     ", [$userLon, $userLat, $this->lon, $this->lat]);

    //     return round($result->distance / 1000, 2);
    // }



    public function listingPromotions()
    {
        return $this->hasMany(ListingPromotion::class);
    }

    public function scopeCurrentlyPromoted($query)
    {
        return $query->where('is_featured', true)
            ->where('promoted_until', '>', Carbon::now());
    }

    // public function getIsCurrentlyPromotedAttribute(): bool
    // {
    //     return $this->is_featured && $this->promoted_until && $this->promoted_until > Carbon::now();
    // }

    public function getRecommendedListingsFromCache()
    {
        return Cache::remember("recommended_listings_{$this->id}", 3600, function () {
            return self::query()
                ->where('category_id', $this->category_id)
                ->where('id', '!=', $this->id)
                ->where('is_published', 1)
                ->latest()
                ->take(15)
                ->get();
        });
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }
}
