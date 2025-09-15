<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr; // لاستخدام Arr::where, Arr::pluck, etc.
use Illuminate\Support\Collection;
use Illuminate\Support\Str;// لتسهيل التعامل مع المصفوفات

class ApiController extends Controller
{
    // --- مخزن البيانات الوهمية (يحاكي قاعدة البيانات) ---
    private array $users = [];
    private array $categories = [];
    private array $ads = [];
    private array $comments = [];
    private array $conversations = [];
    private array $messages = [];
    private array $favorites = []; // ['userId' => [adId1, adId2]]
    private array $notifications = [];

    // عدادات لتوليد IDs رقمية
    private int $nextUserId = 1;
    private int $nextCategoryId = 1;
    private int $nextAdId = 1;
    private int $nextCommentId = 1;
    private int $nextConversationId = 1;
    private int $nextMessageId = 1;
    private int $nextNotificationId = 1;

    // المستخدم الحالي المحاكى (لأغراض الاختبار)
    private const CURRENT_USER_ID = 1;

    public function __construct()
    {
        // تهيئة البيانات الوهمية الأولية عند إنشاء الكنترولر
        $this->seedMockData();
    }

    // --- تهيئة البيانات الوهمية ---
    private function seedMockData(): void
    {
        // Users
        $this->users = [
            1 => $this->_createMockUser(1, ['email' => 'current@example.com', 'name' => 'المستخدم الحالي']),
            2 => $this->_createMockUser(2, ['email' => 'seller1@example.com', 'name' => 'بائع 1']),
            3 => $this->_createMockUser(3, ['email' => 'seller2@example.com', 'name' => 'بائع 2']),
        ];
        $this->nextUserId = 4;

        // Categories
        $cat1 = $this->_createMockCategory(1, ['name' => 'سيارات', 'slug' => 'cars']);
        $cat2 = $this->_createMockCategory(2, ['name' => 'عقارات', 'slug' => 'real-estate']);
        $cat3 = $this->_createMockCategory(3, ['name' => 'إلكترونيات', 'slug' => 'electronics']);
        $subCat1 = $this->_createMockCategory(4, ['name' => 'سيارات تويوتا', 'slug' => 'toyota-cars', 'parentId' => 1]);
        $subCat2 = $this->_createMockCategory(5, ['name' => 'شقق للإيجار', 'slug' => 'apartments-rent', 'parentId' => 2]);
        $this->categories = [
            1 => $cat1, 2 => $cat2, 3 => $cat3, 4 => $subCat1, 5 => $subCat2,
        ];
        $this->nextCategoryId = 6;

        // Ads
        $ad1 = $this->_createMockAd(1, ['title' => 'سيارة تويوتا كامري 2020', 'price' => 75000, 'categoryId' => 4, 'sellerId' => 2, 'location' => ['city' => 'الرياض', 'district' => 'العليا', 'lat' => 24.7136, 'lng' => 46.6753], 'status' => 'active']);
        $ad2 = $this->_createMockAd(2, ['title' => 'شقة للإيجار بحي الروضة', 'price' => 100, 'categoryId' => 5, 'sellerId' => 3, 'location' => ['city' => 'جدة', 'district' => 'الروضة', 'lat' => 21.5810, 'lng' => 39.1850], 'status' => 'active', 'featured' => true]);
        $ad3 = $this->_createMockAd(3, ['title' => 'لابتوب مستعمل للبيع', 'price' => 1500, 'categoryId' => 3, 'sellerId' => 2, 'location' => ['city' => 'الدمام', 'district' => 'الشاطئ', 'lat' => 26.4207, 'lng' => 50.0888], 'status' => 'sold']);
        $ad4 = $this->_createMockAd(4, ['title' => 'أرض للبيع شمال الرياض', 'price' => 1200000, 'categoryId' => 2, 'sellerId' => 3, 'location' => ['city' => 'الرياض', 'district' => 'النرجس', 'lat' => 24.8168, 'lng' => 46.6300], 'status' => 'active']);
        $this->ads = [
            1 => $ad1, 2 => $ad2, 3 => $ad3, 4 => $ad4,
        ];
        $this->nextAdId = 5;

        // Comments
        $com1 = $this->_createMockComment(1, ['adId' => 1, 'userId' => 3, 'text' => 'كم آخر سعر؟']);
        $com2 = $this->_createMockComment(2, ['adId' => 1, 'userId' => self::CURRENT_USER_ID, 'text' => '70 ألف وصامل.', 'parentId' => 1]);
        $com3 = $this->_createMockComment(3, ['adId' => 2, 'userId' => self::CURRENT_USER_ID, 'text' => 'هل الإيجار سنوي؟']);
        $this->comments = [
             1 => $com1, 2 => $com2, 3 => $com3,
        ];
        $this->nextCommentId = 4;

        // Favorites for current user
        $this->favorites[self::CURRENT_USER_ID] = [1, 2];

        // Conversations & Messages
        $conv1 = $this->_createMockConversation(1, ['adId' => 1, 'participantIds' => [self::CURRENT_USER_ID, 2]]);
        $msg1 = $this->_createMockMessage(1, ['conversationId' => 1, 'senderId' => self::CURRENT_USER_ID, 'receiverId' => 2, 'text' => 'السلام عليكم، بخصوص السيارة']);
        $msg2 = $this->_createMockMessage(2, ['conversationId' => 1, 'senderId' => 2, 'receiverId' => self::CURRENT_USER_ID, 'text' => 'وعليكم السلام، حياك الله']);
        $conv1['lastMessageId'] = 2; // Update last message ID
        $conv1['updatedAt'] = $msg2['createdAt']; // Update timestamp

        $this->conversations = [1 => $conv1];
        $this->messages = [1 => $msg1, 2 => $msg2];
        $this->nextConversationId = 2;
        $this->nextMessageId = 3;

        // Notifications for current user
        $notif1 = $this->_createMockNotification(1, ['userId' => self::CURRENT_USER_ID, 'type' => 'comment', 'title' => 'تعليق جديد', 'content' => 'قام مستخدم بالتعليق على إعلانك', 'relatedId' => 1]); // Assuming relatedId is Ad ID for comment notification
        $notif2 = $this->_createMockNotification(2, ['userId' => self::CURRENT_USER_ID, 'type' => 'message', 'title' => 'رسالة جديدة', 'content' => 'لديك رسالة جديدة من بائع 1', 'relatedId' => 1, 'read' => true]); // Assuming relatedId is Conversation ID
        $this->notifications = [1 => $notif1, 2 => $notif2];
        $this->nextNotificationId = 3;
    }

    // --- دوال مساعدة لإنشاء بيانات وهمية (مع IDs رقمية) ---

    private function _createMockUser(int $id, array $overrides = []): array
    {
        $defaults = [
            'id' => $id,
            'name' => 'مستخدم ' . $id,
            'email' => 'user' . $id . '@example.com',
            'phone' => '05' . rand(10000000, 99999999),
            'city' => ['الرياض', 'جدة', 'الدمام'][array_rand(['الرياض', 'جدة', 'الدمام'])],
            'avatarUrl' => 'https://via.placeholder.com/150/' . dechex(rand(0,16777215)) . '/808080?text=U'.$id,
            'createdAt' => now()->subDays(rand(1, 30))->toIso8601String(),
            'emailVerifiedAt' => rand(0, 1) ? now()->subDays(rand(1, 5))->toIso8601String() : null,
            'rating' => round(rand(30, 50) / 10, 1),
            'reviewCount' => rand(5, 50),
        ];
        return array_merge($defaults, $overrides);
    }

    private function _createMockCategory(int $id, array $overrides = []): array
    {
        $parentId = $overrides['parentId'] ?? null;
        $defaults = [
            'id' => $id,
            'name' => ($parentId ? 'فرعي ' : '') . 'قسم ' . $id,
            'slug' => 'category-' . $id . Str::random(3),
            'iconUrl' => 'https://via.placeholder.com/50/' . dechex(rand(0,16777215)) . '/FFFFFF?text=C'.$id,
            'parentId' => $parentId,
            'adCount' => rand(10, 1000), // Should be calculated dynamically in real app
        ];
         // Remove parentId if null for cleaner output
        $category = array_merge($defaults, $overrides);
        if ($category['parentId'] === null) {
            unset($category['parentId']);
        }
        return $category;
    }

     private function _createMockAd(int $id, array $overrides = []): array
    {
        $defaults = [
            'id' => $id,
            'title' => 'إعلان وهمي رقم ' . $id,
            'description' => 'وصف تفصيلي للإعلان رقم ' . $id . ' لغرض الاختبار.',
            'price' => rand(100, 5000),
            'currency' => 'SAR',
            'categoryId' => 1, // Default category ID
            'sellerId' => 1, // Default seller ID
            'images' => [
                'https://via.placeholder.com/600x400/CCCCCC/FFFFFF?text=Ad'.$id.'-Img1',
                'https://via.placeholder.com/600x400/DDDDDD/FFFFFF?text=Ad'.$id.'-Img2',
            ],
            'location' => [
                'city' => 'الرياض',
                'district' => 'حي وهمي ' . $id,
                'lat' => round(24.7136 + (rand(-500, 500) / 10000), 4),
                'lng' => round(46.6753 + (rand(-500, 500) / 10000), 4),
            ],
            'status' => ['active', 'pending'][rand(0, 1)], // Default status
            'featured' => false,
            'viewCount' => rand(50, 5000),
            'commentCount' => 0, // Calculated later based on actual comments
            'favoriteCount' => 0, // Calculated later based on favorites
            'createdAt' => now()->subDays(rand(1, 60))->toIso8601String(),
            'updatedAt' => now()->subHours(rand(1, 48))->toIso8601String(),
            'attributes' => [
                ['name' => 'اللون', 'value' => ['أحمر', 'أزرق', 'أسود'][array_rand(['أحمر', 'أزرق', 'أسود'])]],
            ],
            'isSold' => false,
        ];

        $adData = array_merge($defaults, $overrides);
        $adData['isSold'] = ($adData['status'] === 'sold');

        // Ensure seller and category objects are included based on IDs
        $adData['seller'] = $this->users[$adData['sellerId']] ?? $this->_createMockUser($adData['sellerId']); // Get actual or mock if missing
        $adData['category'] = $this->categories[$adData['categoryId']] ?? $this->_createMockCategory($adData['categoryId']); // Get actual or mock if missing

        // Calculate commentCount (simple count)
        $adData['commentCount'] = count(Arr::where($this->comments, fn($c) => $c['adId'] === $id));
        // Calculate favoriteCount (simple count across all users)
        $favCount = 0;
        foreach($this->favorites as $userFavs) {
            if (in_array($id, $userFavs)) {
                $favCount++;
            }
        }
        $adData['favoriteCount'] = $favCount;


        return $adData;
    }

    private function _createMockComment(int $id, array $overrides = []): array
    {
        $defaults = [
            'id' => $id,
            'adId' => 1, // Default Ad ID
            'userId' => 1, // Default User ID
            'text' => 'تعليق تلقائي ' . $id,
            'parentId' => null,
            'replies' => [], // Populated when retrieving
            'createdAt' => now()->subHours(rand(1, 72))->toIso8601String(),
        ];
        $commentData = array_merge($defaults, $overrides);

        // Ensure user object is included
        $commentData['user'] = $this->users[$commentData['userId']] ?? $this->_createMockUser($commentData['userId']);

        // Remove parentId if null
        if ($commentData['parentId'] === null) {
            unset($commentData['parentId']);
        }

        return $commentData;
    }

     private function _createMockMessage(int $id, array $overrides = []): array
    {
        $defaults = [
            'id' => $id,
            'senderId' => 1,
            'receiverId' => 2,
            'conversationId' => 1,
            'adId' => null, // Can be added based on conversation
            'text' => 'رسالة تلقائية ' . $id,
            'read' => false,
            'createdAt' => now()->subMinutes(rand(1, 120))->toIso8601String(),
        ];
         $messageData = array_merge($defaults, $overrides);

         // Add adId if conversation exists and has adId
         if(isset($this->conversations[$messageData['conversationId']])) {
            $messageData['adId'] = $this->conversations[$messageData['conversationId']]['adId'] ?? null;
         }
          // Remove adId if null
        if ($messageData['adId'] === null) {
            unset($messageData['adId']);
        }


         return $messageData;
    }

    private function _createMockConversation(int $id, array $overrides = []): array
    {
         $defaults = [
            'id' => $id,
            'adId' => 1, // Default Ad ID
            'participantIds' => [1, 2], // Default participants
            'lastMessageId' => null, // ID of the last message
            'unreadCount' => rand(0, 5), // Unread count for the *current user*
            'createdAt' => now()->subDays(rand(1, 10))->toIso8601String(),
            'updatedAt' => now()->subHours(rand(1, 24))->toIso8601String(),
        ];
        $convData = array_merge($defaults, $overrides);

        // Ensure Ad object is included
        $convData['ad'] = isset($this->ads[$convData['adId']]) ? $this->_enrichAd($this->ads[$convData['adId']]) : null; // Get full ad or null
        if ($convData['ad'] === null) {
             unset($convData['ad']); // Remove if ad not found
             unset($convData['adId']);
        }


        // Ensure participant User objects are included
        $participants = [];
        foreach ($convData['participantIds'] as $pId) {
            if (isset($this->users[$pId])) {
                $participants[] = $this->users[$pId];
            }
        }
        $convData['participants'] = $participants;

        // Ensure lastMessage object is included (if ID exists)
        if ($convData['lastMessageId'] !== null && isset($this->messages[$convData['lastMessageId']])) {
            $convData['lastMessage'] = $this->messages[$convData['lastMessageId']];
        } else {
            $convData['lastMessage'] = null; // Or a default message structure
            unset($convData['lastMessageId']); // Remove ID if message not found
        }
        // Remove internal participantIds
        unset($convData['participantIds']);

        return $convData;
    }

    private function _createMockNotification(int $id, array $overrides = []): array
    {
        $types = ['message', 'comment', 'favorite', 'system', 'ad_status'];
        $type = $types[array_rand($types)];

        $defaults = [
            'id' => $id,
            'userId' => self::CURRENT_USER_ID,
            'type' => $type,
            'title' => 'إشعار ' . $id,
            'content' => 'محتوى إشعار تلقائي (' . $type . ')',
            'relatedId' => null, // ID of related entity (ad, conversation, etc.)
            'read' => false,
            'createdAt' => now()->subHours(rand(1, 24))->toIso8601String(),
        ];
        $notification = array_merge($defaults, $overrides);

        // Make relatedId numeric if present
        if($notification['relatedId'] !== null) {
            $notification['relatedId'] = (int) $notification['relatedId'];
        } else {
             unset($notification['relatedId']);
        }

        return $notification;
    }

    // Helper to enrich Ad data with Seller and Category objects (used when retrieving single ads)
    private function _enrichAd(array $adData): array
    {
        $enriched = $adData;
        if (isset($adData['sellerId']) && isset($this->users[$adData['sellerId']])) {
            $enriched['seller'] = $this->users[$adData['sellerId']];
        } else {
             // Handle missing seller? Maybe add a placeholder or remove the key
            unset($enriched['seller']);
        }
         if (isset($adData['categoryId']) && isset($this->categories[$adData['categoryId']])) {
            $enriched['category'] = $this->categories[$adData['categoryId']];
        } else {
             unset($enriched['category']);
        }

         // Recalculate counts just in case
        $enriched['commentCount'] = count(Arr::where($this->comments, fn($c) => $c['adId'] === $adData['id']));
        $favCount = 0;
        foreach($this->favorites as $userFavs) {
            if (in_array($adData['id'], $userFavs)) {
                $favCount++;
            }
        }
        $enriched['favoriteCount'] = $favCount;

        return $enriched;
    }

     // Helper to enrich Comment data with User and Replies
    private function _enrichComment(array $commentData): array
    {
        $enriched = $commentData;
         if (isset($commentData['userId']) && isset($this->users[$commentData['userId']])) {
            $enriched['user'] = $this->users[$commentData['userId']];
        } else {
            unset($enriched['user']);
        }

        // Find replies for this comment
        $replies = Arr::where($this->comments, fn($c) => $c['parentId'] === $commentData['id']);
        $enriched['replies'] = array_map([$this, '_enrichComment'], array_values($replies)); // Enrich replies recursively

        return $enriched;
    }


        // --- Ads API ---

        public function getAds(Request $request): JsonResponse
        {
            $limit = (int) $request->input('limit', 10);
            $page = (int) $request->input('page', 1);

            // Start with all ads collection
            $query = new Collection($this->ads);

            // --- Filtering ---
            if ($request->has('category')) {
                $categoryId = (int) $request->input('category');
                 // Include ads from subcategories too (example logic)
                 $targetCategoryIds = [$categoryId];

                 // *** FIX HERE: Check if parentId exists before comparing ***
                 $subcategories = Arr::where($this->categories, fn($cat) => isset($cat['parentId']) && $cat['parentId'] === $categoryId);

                 $targetCategoryIds = array_merge($targetCategoryIds, Arr::pluck($subcategories, 'id'));
                 $query = $query->whereIn('categoryId', $targetCategoryIds);
            }
            // ... (rest of the filtering and sorting logic remains the same) ...
             if ($request->has('city')) {
                 $query = $query->where('location.city', $request->input('city'));
             }
             if ($request->has('minPrice')) {
                  $query = $query->where('price', '>=', (float) $request->input('minPrice'));
             }
             if ($request->has('maxPrice')) {
                 $query = $query->where('price', '<=', (float) $request->input('maxPrice'));
             }
             if ($request->has('featured')) {
                  $query = $query->where('featured', filter_var($request->input('featured'), FILTER_VALIDATE_BOOLEAN));
             }
             if ($request->has('search')) {
                  $searchTerm = $request->input('search');
                  $query = $query->filter(function ($ad) use ($searchTerm) {
                      return stripos($ad['title'], $searchTerm) !== false || stripos($ad['description'], $searchTerm) !== false;
                  });
             }
              if ($request->has('status')) { // Added status filter for getUserAds consistency
                  $query = $query->where('status', $request->input('status'));
              }

            // --- Sorting ---
            $sortBy = $request->input('sortBy');
            if ($sortBy === 'newest') {
                $query = $query->sortByDesc('createdAt');
            } elseif ($sortBy === 'price_asc') {
                $query = $query->sortBy('price');
            } elseif ($sortBy === 'price_desc') {
                 $query = $query->sortByDesc('price');
            } elseif ($sortBy === 'distance' && $request->has(['lat', 'lng'])) {
                $query = $query->sortByDesc('id'); // Simplified mock distance sort
            } else {
                 $query = $query->sortByDesc('createdAt'); // Default sort
            }

            // --- Pagination ---
            $total = $query->count();
            $results = $query->forPage($page, $limit)->values(); // Get items for the current page

            // Enrich results with Seller and Category details
            $enrichedResults = $results->map(fn($ad) => $this->_enrichAd($ad))->all();

            return response()->json([
                'data' => $enrichedResults,
                'meta' => [
                    'total' => $total,
                    'page' => $page,
                    'limit' => $limit,
                    'totalPages' => ceil($total / $limit),
                ]
            ]);
        }

    public function getAd(int $id): JsonResponse
    {
        if (!isset($this->ads[$id])) {
            return response()->json(['message' => 'Ad not found'], 404);
        }
        // Enrich with seller/category details before returning
        return response()->json($this->_enrichAd($this->ads[$id]));
    }

     public function getRelatedAds(int $adId, Request $request): JsonResponse
    {
         if (!isset($this->ads[$adId])) {
             return response()->json(['message' => 'Ad not found'], 404);
         }
         $limit = (int) $request->input('limit', 4);
         $originalAd = $this->ads[$adId];

         // Simple related logic: same category, different ad, limit result
        $related = (new Collection($this->ads))
            ->where('categoryId', $originalAd['categoryId'])
            ->where('id', '!=', $adId)
             ->where('status','active') // Only show active related ads
            ->take($limit);

         $enrichedResults = $related->map(fn($ad) => $this->_enrichAd($ad))->values()->all();

        return response()->json($enrichedResults);
    }

    public function createAd(Request $request): JsonResponse
    {
        // Simulate authentication - assuming user is CURRENT_USER_ID
        $sellerId = self::CURRENT_USER_ID;

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'categoryId' => 'required|integer|exists:categories,id', // Use exists rule with mock data keys
            'location' => 'required|array',
            'location.city' => 'required|string',
            'location.district' => 'required|string',
            'location.lat' => 'sometimes|numeric',
            'location.lng' => 'sometimes|numeric',
            'images' => 'sometimes|array', // URLs passed directly
            'attributes' => 'sometimes|array',
        ], [], [ // Custom attribute names for validation messages (Arabic)
             'categoryId' => 'القسم',
             'location.city' => 'المدينة',
             'location.district' => 'الحي',
         ]);


         // Custom exists validation for mock data
         $validator->after(function ($validator) use ($request) {
             if ($request->has('categoryId') && !isset($this->categories[$request->integer('categoryId')])) {
                 $validator->errors()->add('categoryId', 'القسم المحدد غير موجود.');
             }
         });


        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $newAdId = $this->nextAdId++;
        $adData = $request->only(['title', 'description', 'price', 'currency', 'categoryId', 'location', 'attributes', 'images']);
        $adData['id'] = $newAdId;
        $adData['sellerId'] = $sellerId;
        $adData['status'] = 'pending'; // Default status for new ads
        $adData['featured'] = false;
        $adData['viewCount'] = 0;
        $adData['createdAt'] = now()->toIso8601String();
        $adData['updatedAt'] = now()->toIso8601String();
        $adData['isSold'] = false;
        // Ensure numeric types
        $adData['price'] = (float)$adData['price'];
        $adData['categoryId'] = (int)$adData['categoryId'];
        $adData['images'] = $adData['images'] ?? []; // Default to empty array if not provided
        $adData['attributes'] = $adData['attributes'] ?? [];

        // Add to our mock database
        $this->ads[$newAdId] = $adData;


        // Return the newly created ad, enriched with seller/category
        return response()->json($this->_enrichAd($this->ads[$newAdId]), 201);
    }

    public function updateAd(Request $request, int $id): JsonResponse
    {
        // Simulate authorization - check if current user owns the ad
        if (!isset($this->ads[$id])) {
            return response()->json(['message' => 'Ad not found'], 404);
        }
        if ($this->ads[$id]['sellerId'] !== self::CURRENT_USER_ID) {
             return response()->json(['message' => 'Unauthorized'], 403);
        }

         // Allow partial updates, validate only provided fields
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric|min:0',
            'currency' => 'sometimes|required|string|size:3',
            'categoryId' => 'sometimes|required|integer|exists:categories,id',
            'location' => 'sometimes|required|array',
            'location.city' => 'sometimes|required|string',
            'location.district' => 'sometimes|required|string',
             'status' => 'sometimes|required|in:active,pending,expired', // Cannot set to 'sold' via update
            'attributes' => 'sometimes|array',
            'images' => 'sometimes|array',
        ], [], [
             'categoryId' => 'القسم',
             'location.city' => 'المدينة',
             'location.district' => 'الحي',
             'status' => 'الحالة',
        ]);

         // Custom exists validation for mock data
         $validator->after(function ($validator) use ($request) {
             if ($request->has('categoryId') && !isset($this->categories[$request->integer('categoryId')])) {
                 $validator->errors()->add('categoryId', 'القسم المحدد غير موجود.');
             }
         });

         if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        // Get validated data
        $validatedData = $validator->validated();

        // Update the ad in the mock array
        $this->ads[$id] = array_merge($this->ads[$id], $validatedData);
        $this->ads[$id]['updatedAt'] = now()->toIso8601String();

         // Ensure numeric types after merge
        if(isset($validatedData['price'])) $this->ads[$id]['price'] = (float)$validatedData['price'];
        if(isset($validatedData['categoryId'])) $this->ads[$id]['categoryId'] = (int)$validatedData['categoryId'];

        // Return the updated ad, enriched
        return response()->json($this->_enrichAd($this->ads[$id]));
    }

    public function deleteAd(int $id): JsonResponse
    {
        // Simulate authorization
        if (!isset($this->ads[$id])) {
            return response()->json(['message' => 'Ad not found'], 404);
        }
        if ($this->ads[$id]['sellerId'] !== self::CURRENT_USER_ID) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Remove the ad from the array
        unset($this->ads[$id]);

         // Optional: Remove related comments, favorites etc.
         $this->comments = Arr::where($this->comments, fn($c) => $c['adId'] !== $id);
         foreach ($this->favorites as $userId => $favs) {
             $this->favorites[$userId] = array_values(array_diff($favs, [$id]));
         }


        return response()->json(null, 204); // No Content
    }

    public function markAsSold(int $id): JsonResponse
    {
        // Simulate authorization
        if (!isset($this->ads[$id])) {
            return response()->json(['message' => 'Ad not found'], 404);
        }
        if ($this->ads[$id]['sellerId'] !== self::CURRENT_USER_ID) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $this->ads[$id]['status'] = 'sold';
        $this->ads[$id]['isSold'] = true;
        $this->ads[$id]['updatedAt'] = now()->toIso8601String();

        return response()->json($this->_enrichAd($this->ads[$id]));
    }

     public function promoteAd(Request $request, int $id): JsonResponse
    {
        // Simulate authorization
        if (!isset($this->ads[$id])) {
             return response()->json(['message' => 'Ad not found'], 404);
         }
         if ($this->ads[$id]['sellerId'] !== self::CURRENT_USER_ID) {
             return response()->json(['message' => 'Unauthorized'], 403);
         }

        $validator = Validator::make($request->all(), [
            'package' => 'required|string', // e.g., 'featured_7_days'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Promotion package is required', 'errors' => $validator->errors()], 422);
        }

        // Simulate applying promotion
        $this->ads[$id]['featured'] = true;
        $this->ads[$id]['updatedAt'] = now()->toIso8601String();
        // In real app, store promotion details and expiry

        return response()->json($this->_enrichAd($this->ads[$id]));
    }

    public function uploadAdImages(Request $request, int $id): JsonResponse
    {
        // Simulate authorization
        if (!isset($this->ads[$id])) {
             return response()->json(['message' => 'Ad not found'], 404);
         }
         if ($this->ads[$id]['sellerId'] !== self::CURRENT_USER_ID) {
             return response()->json(['message' => 'Unauthorized'], 403);
         }

        if (!$request->hasFile('images')) {
            return response()->json(['message' => 'No images uploaded'], 400);
        }

        $files = $request->file('images');
        $urls = $this->ads[$id]['images'] ?? []; // Start with existing images

        foreach ($files as $file) {
            if ($file->isValid()) {
                // Simulate storing and generating URL
                 $urls[] = 'https://via.placeholder.com/600x400/AFAFAF/FFFFFF?text=Ad'.$id.'-New'.rand(100,999); // Mock URL
            }
        }

        if (empty($urls) && empty($this->ads[$id]['images'])) {
             return response()->json(['message' => 'Invalid files provided or no files uploaded'], 400);
        }

        // Update ad with new image list
        $this->ads[$id]['images'] = $urls;
        $this->ads[$id]['updatedAt'] = now()->toIso8601String();

        return response()->json($urls, 200); // Return the list of URLs (or maybe the updated ad?) - Front expects URL[]
    }

    public function getUserAds(int $userId, Request $request): JsonResponse
    {
        // In real app, add authorization check if needed (e.g., only owner or admin can see non-active ads)
        if (!isset($this->users[$userId])) {
             return response()->json(['message' => 'User not found'], 404);
        }

        $query = (new Collection($this->ads))->where('sellerId', $userId);

        // Filter by status if provided
        if ($request->has('status')) {
             $query = $query->where('status', $request->input('status'));
        }

        // Enrich and return
        $userAds = $query->map(fn($ad) => $this->_enrichAd($ad))->values()->all();
        return response()->json($userAds);
    }

    // --- Categories API ---

    public function getCategories(): JsonResponse
    {
        // Return only top-level categories
        $topLevel = Arr::where($this->categories, fn($cat) => !isset($cat['parentId']) || $cat['parentId'] === null);
        return response()->json(array_values($topLevel));
    }

     public function getCategory(int $id): JsonResponse
    {
         if (!isset($this->categories[$id])) {
             return response()->json(['message' => 'Category not found'], 404);
         }
        return response()->json($this->categories[$id]);
    }

    public function getSubcategories(int $categoryId): JsonResponse
    {
        if (!isset($this->categories[$categoryId])) {
             return response()->json(['message' => 'Category not found'], 404);
        }
        $subcategories = Arr::where($this->categories, fn($cat) => isset($cat['parentId']) && $cat['parentId'] === $categoryId);
        return response()->json(array_values($subcategories));
    }

    // --- Comments API ---

    public function getComments(int $adId): JsonResponse
    {
        if (!isset($this->ads[$adId])) {
             return response()->json(['message' => 'Ad not found'], 404);
        }

        // Get top-level comments for the ad
        $topLevelComments = Arr::where($this->comments, fn($c) => $c['adId'] === $adId && (!isset($c['parentId']) || $c['parentId'] === null));

        // Enrich comments with user data and replies
        $enrichedComments = array_map([$this, '_enrichComment'], array_values($topLevelComments));

        return response()->json($enrichedComments);
    }

    public function addComment(Request $request, int $adId): JsonResponse
    {
        // Requires authentication
        $userId = self::CURRENT_USER_ID;
        if (!isset($this->ads[$adId])) {
             return response()->json(['message' => 'Ad not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'text' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422);
        }

        $newCommentId = $this->nextCommentId++;
        $commentData = [
            'id' => $newCommentId,
            'adId' => $adId,
            'userId' => $userId,
            'text' => $request->input('text'),
            'parentId' => null, // Top-level comment
            'createdAt' => now()->toIso8601String(),
        ];

        $this->comments[$newCommentId] = $commentData;

        // Update ad comment count
        //$this->ads[$adId]['commentCount']++; // Let _enrichAd handle recalculation

        return response()->json($this->_enrichComment($this->comments[$newCommentId]), 201);
    }

    public function replyToComment(Request $request, int $adId, int $commentId): JsonResponse
    {
        // Requires authentication
        $userId = self::CURRENT_USER_ID;
         if (!isset($this->ads[$adId])) {
             return response()->json(['message' => 'Ad not found'], 404);
         }
         if (!isset($this->comments[$commentId])) {
             return response()->json(['message' => 'Comment not found'], 404);
         }
         // Ensure the parent comment belongs to the same ad
         if($this->comments[$commentId]['adId'] !== $adId) {
             return response()->json(['message' => 'Comment does not belong to this Ad'], 400);
         }

        $validator = Validator::make($request->all(), [
            'text' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422);
        }

        $newReplyId = $this->nextCommentId++;
        $replyData = [
            'id' => $newReplyId,
            'adId' => $adId,
            'userId' => $userId,
            'text' => $request->input('text'),
            'parentId' => $commentId, // Link to the parent comment
            'createdAt' => now()->toIso8601String(),
        ];

        $this->comments[$newReplyId] = $replyData;

         // Update ad comment count
        //$this->ads[$adId]['commentCount']++;

        return response()->json($this->_enrichComment($this->comments[$newReplyId]), 201);
    }

    public function deleteComment(int $adId, int $commentId): JsonResponse
    {
        // Requires authentication and authorization
        $userId = self::CURRENT_USER_ID;
         if (!isset($this->ads[$adId])) {
             return response()->json(['message' => 'Ad not found'], 404);
         }
        if (!isset($this->comments[$commentId])) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

         $comment = $this->comments[$commentId];
         $ad = $this->ads[$adId];

        // Authorization: User owns comment OR user owns ad
        if ($comment['userId'] !== $userId && $ad['sellerId'] !== $userId) {
             return response()->json(['message' => 'Unauthorized to delete this comment'], 403);
        }
         // Ensure comment belongs to the ad
        if ($comment['adId'] !== $adId) {
             return response()->json(['message' => 'Comment does not belong to this ad'], 400);
        }


        // Delete the comment and any replies it might have (cascading delete simulation)
        $idsToDelete = [$commentId];
        $replies = Arr::where($this->comments, fn($c) => $c['parentId'] === $commentId);
        $idsToDelete = array_merge($idsToDelete, Arr::pluck($replies, 'id'));

        foreach ($idsToDelete as $idDel) {
            unset($this->comments[$idDel]);
        }

        // Update ad comment count (will be recalculated on next fetch)
        //$this->ads[$adId]['commentCount'] -= count($idsToDelete);

        return response()->json(null, 204);
    }

    // --- User API ---

    public function getCurrentUser(): JsonResponse
    {
        // Requires authentication
        $userId = self::CURRENT_USER_ID;
        if (!isset($this->users[$userId])) {
            // Should not happen if middleware is set, but good failsafe
             return response()->json(['message' => 'User not authenticated or found'], 401);
        }
        return response()->json($this->users[$userId]);
    }

    public function getUser(int $id): JsonResponse
    {
        if (!isset($this->users[$id])) {
             return response()->json(['message' => 'User not found'], 404);
        }
        // Return public profile (might omit sensitive fields like email if needed)
        return response()->json($this->users[$id]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        // Requires authentication
        $userId = self::CURRENT_USER_ID;
        if (!isset($this->users[$userId])) {
             return response()->json(['message' => 'User not authenticated or found'], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20', // Add regex validation if needed
            'city' => 'sometimes|required|string|max:100',
        ]);

         if ($validator->fails()) {
            return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422);
        }

        // Get validated data and update user
        $validatedData = $validator->validated();
        $this->users[$userId] = array_merge($this->users[$userId], $validatedData);

        return response()->json($this->users[$userId]);
    }

     public function uploadAvatar(Request $request): JsonResponse
    {
        // Requires authentication
        $userId = self::CURRENT_USER_ID;
         if (!isset($this->users[$userId])) {
             return response()->json(['message' => 'User not authenticated or found'], 401);
        }

        if (!$request->hasFile('avatar')) {
            return response()->json(['message' => 'No avatar file uploaded'], 400);
        }

        $file = $request->file('avatar');

        if ($file->isValid()) {
             // Simulate storing and getting URL
             $newAvatarUrl = 'https://via.placeholder.com/150/' . dechex(rand(0,16777215)) . '/FFFFFF?text=NewU'.$userId; // Mock URL
             $this->users[$userId]['avatarUrl'] = $newAvatarUrl;

             return response()->json(['avatarUrl' => $newAvatarUrl], 200);
        } else {
            return response()->json(['message' => 'Invalid avatar file'], 400);
        }
    }

    // --- Favorites API ---

    public function getFavorites(): JsonResponse
    {
        // Requires authentication
        $userId = self::CURRENT_USER_ID;
        $favoriteAdIds = $this->favorites[$userId] ?? [];

        $favoriteAds = (new Collection($this->ads))
            ->whereIn('id', $favoriteAdIds)
            ->map(fn($ad) => $this->_enrichAd($ad)) // Enrich with seller/category
            ->values()
            ->all();

        return response()->json($favoriteAds);
    }

    public function addToFavorites(Request $request): JsonResponse
    {
        // Requires authentication
        $userId = self::CURRENT_USER_ID;
        $validator = Validator::make($request->all(), [
            'adId' => 'required|integer', // Check if ad exists
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Ad ID is required', 'errors' => $validator->errors()], 422);
        }

        $adId = $request->integer('adId');
        if (!isset($this->ads[$adId])) {
             return response()->json(['message' => 'Ad not found'], 404);
        }

        // Initialize favorites array for user if not exists
        if (!isset($this->favorites[$userId])) {
            $this->favorites[$userId] = [];
        }

        // Add ad ID if not already present
        if (!in_array($adId, $this->favorites[$userId])) {
            $this->favorites[$userId][] = $adId;
        }

        return response()->json(null, 204); // No Content
    }

    public function removeFromFavorites(int $adId): JsonResponse
    {
        // Requires authentication
        $userId = self::CURRENT_USER_ID;
        if (!isset($this->ads[$adId])) {
             return response()->json(['message' => 'Ad not found'], 404); // Or just proceed silently
        }

        if (isset($this->favorites[$userId])) {
            // Remove the ad ID from the user's favorites list
            $this->favorites[$userId] = array_values(array_diff($this->favorites[$userId], [$adId]));
        }

        return response()->json(null, 204); // No Content
    }

    public function isFavorite(int $adId): JsonResponse
    {
        // Requires authentication
        $userId = self::CURRENT_USER_ID;

        // Check if ad exists (optional, depends on desired behavior if ad is deleted)
        // if (!isset($this->ads[$adId])) {
        //     return response()->json(['message' => 'Ad not found'], 404);
        // }

        $isFav = isset($this->favorites[$userId]) && in_array($adId, $this->favorites[$userId]);

        // IMPORTANT: The frontend expects a 200 OK for true and an error (like 404) for false.
        if ($isFav) {
             // Return 200 OK, the body doesn't strictly matter for the frontend check, but we can include it.
             return response()->json(['isFavorite' => true]);
             // Alternative: return response(null, 200);
        } else {
            // Return 404 Not Found to indicate "not favorited" as per frontend error handling logic.
            return response()->json(['message' => 'Ad not in favorites'], 404);
        }
    }


    // --- Messages API ---

    public function getConversations(): JsonResponse
    {
        // Requires authentication
        $userId = self::CURRENT_USER_ID;

        // Find conversations where the current user is a participant
        $userConversations = Arr::where($this->conversations, function ($conv) use ($userId) {
            $participantIds = [];
             // Get participant IDs from the participants array
             if(isset($conv['participants'])) {
                 foreach($conv['participants'] as $p) {
                     $participantIds[] = $p['id'];
                 }
             }
             // Fallback if participants array isn't populated yet in the mock data structure
             elseif(isset($conv['participantIds'])) {
                 $participantIds = $conv['participantIds'];
             }
             return in_array($userId, $participantIds);

        });

        // Enrich each conversation (re-ensure ad, participants, lastMessage are objects)
         $enrichedConversations = [];
         foreach ($userConversations as $conv) {
             // Re-create participantIds temporarily for the helper
             $pIds = [];
             if(isset($conv['participants'])) {
                  foreach($conv['participants'] as $p) { $pIds[] = $p['id']; }
             } elseif(isset($conv['participantIds'])) {
                  $pIds = $conv['participantIds'];
             }
             $conv['participantIds'] = $pIds; // Add it back temporarily
             $enrichedConversations[] = $this->_createMockConversation($conv['id'], $conv); // Use helper to re-enrich
         }


        // Sort by last message time (descending)
        usort($enrichedConversations, function ($a, $b) {
            $timeA = isset($a['lastMessage']['createdAt']) ? strtotime($a['lastMessage']['createdAt']) : (isset($a['updatedAt']) ? strtotime($a['updatedAt']) : 0);
            $timeB = isset($b['lastMessage']['createdAt']) ? strtotime($b['lastMessage']['createdAt']) : (isset($b['updatedAt']) ? strtotime($b['updatedAt']) : 0);
            return $timeB <=> $timeA;
        });


        return response()->json(array_values($enrichedConversations));
    }

     public function getMessages(int $conversationId): JsonResponse
    {
        // Requires authentication & participation
        $userId = self::CURRENT_USER_ID;
        if (!isset($this->conversations[$conversationId])) {
             return response()->json(['message' => 'Conversation not found'], 404);
        }

        $conversation = $this->conversations[$conversationId];
        // Check participation (using participants array if available)
         $participantIds = [];
         if(isset($conversation['participants'])) {
              foreach($conversation['participants'] as $p) { $participantIds[] = $p['id']; }
         } elseif(isset($conversation['participantIds'])) { // Fallback
              $participantIds = $conversation['participantIds'];
         }
         if (!in_array($userId, $participantIds)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Get messages for this conversation
        $messages = Arr::where($this->messages, fn($msg) => $msg['conversationId'] === $conversationId);
        // Sort messages chronologically
        usort($messages, fn($a, $b) => strtotime($a['createdAt']) <=> strtotime($b['createdAt']));

        // Enrich the conversation object itself before returning
         // Re-add participantIds temporarily if needed for enrichment
        $conversation['participantIds'] = $participantIds;
        $enrichedConversation = $this->_createMockConversation($conversationId, $conversation);

        // Add sorted messages array to the response (as expected by frontend type `Conversation` which includes `messages`)
        $enrichedConversation['messages'] = array_values($messages);


        return response()->json($enrichedConversation);
    }

    public function sendMessage(Request $request, int $conversationId): JsonResponse
    {
        // Requires authentication & participation
        $userId = self::CURRENT_USER_ID;
        if (!isset($this->conversations[$conversationId])) {
            return response()->json(['message' => 'Conversation not found'], 404);
        }
        $conversation = $this->conversations[$conversationId];
         $participantIds = [];
         if(isset($conversation['participants'])) {
              foreach($conversation['participants'] as $p) { $participantIds[] = $p['id']; }
         } elseif(isset($conversation['participantIds'])) {
              $participantIds = $conversation['participantIds'];
         }
         if (!in_array($userId, $participantIds)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }


        $validator = Validator::make($request->all(), [
            'text' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422);
        }

        // Determine receiver ID
        $receiverId = null;
        foreach ($participantIds as $pId) {
            if ($pId !== $userId) {
                $receiverId = $pId;
                break;
            }
        }
        if ($receiverId === null) {
            // Should not happen in a 2-participant conversation
             return response()->json(['message' => 'Could not determine message receiver'], 500);
        }


        // Create and store new message
        $newMessageId = $this->nextMessageId++;
        $messageData = [
            'id' => $newMessageId,
            'senderId' => $userId,
            'receiverId' => $receiverId,
            'conversationId' => $conversationId,
            'adId' => $conversation['adId'] ?? null, // Get adId from conversation
            'text' => $request->input('text'),
            'read' => false, // Sent message starts as unread
            'createdAt' => now()->toIso8601String(),
        ];

        $this->messages[$newMessageId] = $this->_createMockMessage($newMessageId, $messageData); // Use helper to ensure structure


        // Update conversation's last message and timestamp
        $this->conversations[$conversationId]['lastMessageId'] = $newMessageId;
        $this->conversations[$conversationId]['updatedAt'] = $messageData['createdAt'];
        // Reset unread count for sender, potentially increment for receiver (more complex to track mock)

        return response()->json($this->messages[$newMessageId], 201);
    }

     public function startConversation(Request $request): JsonResponse
    {
        // Requires authentication
        $userId = self::CURRENT_USER_ID;
        $validator = Validator::make($request->all(), [
            'sellerId' => 'required|integer',
            'adId' => 'required|integer',
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422);
        }

        $sellerId = $request->integer('sellerId');
        $adId = $request->integer('adId');
        $initialMessageText = $request->input('message');

        if (!isset($this->users[$sellerId])) {
            return response()->json(['message' => 'Seller not found'], 404);
        }
        if (!isset($this->ads[$adId])) {
             return response()->json(['message' => 'Ad not found'], 404);
        }
         // Ensure the ad belongs to the seller
         if($this->ads[$adId]['sellerId'] !== $sellerId) {
             return response()->json(['message' => 'Ad does not belong to the specified seller'], 400);
         }
        if ($sellerId === $userId) {
             return response()->json(['message' => 'Cannot start conversation with yourself'], 400);
        }

        // --- Check if conversation already exists for this user, seller, and ad ---
        $existingConversation = (new Collection($this->conversations))->first(function ($conv) use ($userId, $sellerId, $adId) {
            $pIds = [];
             if(isset($conv['participants'])) {
                 foreach($conv['participants'] as $p) { $pIds[] = $p['id']; }
             } elseif(isset($conv['participantIds'])) { // Fallback
                 $pIds = $conv['participantIds'];
             }
             sort($pIds); // Sort IDs to ensure order doesn't matter
             $targetIds = [$userId, $sellerId];
             sort($targetIds);
             return ($conv['adId'] ?? null) === $adId && $pIds === $targetIds;

        });

        if ($existingConversation) {
             // Optional: Send the initial message to the existing conversation instead of erroring
             // For now, just return the existing one (enriched)
             $existingConversation['participantIds'] = [$userId, $sellerId]; // Temp re-add for enrichment
             return response()->json($this->_createMockConversation($existingConversation['id'], $existingConversation));
            // Alternatively, return an error:
            // return response()->json(['message' => 'Conversation already exists'], 409); // 409 Conflict
        }


        // --- Create New Conversation ---
        $newConversationId = $this->nextConversationId++;
        $conversationData = [
            'id' => $newConversationId,
            'adId' => $adId,
            'participantIds' => [$userId, $sellerId],
            'lastMessageId' => null, // Will be set after creating message
            'unreadCount' => 0, // For the current user initially
            'createdAt' => now()->toIso8601String(),
            'updatedAt' => now()->toIso8601String(),
        ];

        // --- Create First Message ---
        $newMessageId = $this->nextMessageId++;
        $messageData = [
            'id' => $newMessageId,
            'senderId' => $userId,
            'receiverId' => $sellerId,
            'conversationId' => $newConversationId,
            'adId' => $adId,
            'text' => $initialMessageText,
            'read' => false,
            'createdAt' => $conversationData['updatedAt'], // Use conversation time
        ];
        $this->messages[$newMessageId] = $this->_createMockMessage($newMessageId, $messageData);

        // Update conversation with last message ID
        $conversationData['lastMessageId'] = $newMessageId;

        // Store the conversation (use helper to ensure nested objects are correct from the start)
        $this->conversations[$newConversationId] = $this->_createMockConversation($newConversationId, $conversationData);


        return response()->json($this->conversations[$newConversationId], 201);
    }

     public function markConversationAsRead(int $conversationId): JsonResponse
    {
        // Requires authentication & participation
        $userId = self::CURRENT_USER_ID;
        if (!isset($this->conversations[$conversationId])) {
            return response()->json(['message' => 'Conversation not found'], 404);
        }
        $conversation = $this->conversations[$conversationId];
        // Check participation
         $participantIds = [];
         if(isset($conversation['participants'])) {
              foreach($conversation['participants'] as $p) { $participantIds[] = $p['id']; }
         } elseif(isset($conversation['participantIds'])) { // Fallback
              $participantIds = $conversation['participantIds'];
         }
         if (!in_array($userId, $participantIds)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Simulate marking messages as read *for the current user*
        foreach ($this->messages as $msgId => $message) {
            if ($message['conversationId'] === $conversationId && $message['receiverId'] === $userId && !$message['read']) {
                $this->messages[$msgId]['read'] = true;
            }
        }

        // Simulate resetting unread count for the current user in the conversation list (if tracked)
        $this->conversations[$conversationId]['unreadCount'] = 0; // Simplified simulation

        return response()->json(null, 204);
    }

    // --- Location API ---
    // getCurrentLocation is client-side

    public function getCities(): JsonResponse
    {
        $cities = ['الرياض', 'جدة', 'الدمام', 'مكة المكرمة', 'المدينة المنورة', 'أبها'];
        return response()->json($cities);
    }

    public function getDistricts(string $city): JsonResponse
    {
        $districts = [];
        // Use URL decoded city name for matching
        $decodedCity = urldecode($city);
        switch ($decodedCity) {
            case 'الرياض':
                $districts = ['العليا', 'السليمانية', 'الملز', 'النسيم', 'الشفاء', 'النرجس'];
                break;
            case 'جدة':
                $districts = ['البلد', 'الحمراء', 'الروضة', 'السلامة', 'أبحر'];
                break;
            case 'الدمام':
                 $districts = ['العزيزية', 'الشاطئ', 'الفيصلية', 'الريان'];
                 break;
             case 'مكة المكرمة':
                  $districts = ['العزيزية', 'الشوقية', 'الرصيفة', 'المعابدة'];
                  break;
            default:
                 $districts = ['حي رئيسي 1', 'حي رئيسي 2'];
        }
        return response()->json($districts);
    }

    // --- Auth API ---

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422);
        }

        // Simulate login check against mock users
        $user = (new Collection($this->users))->firstWhere('email', $request->input('email'));

        // Simulate password check (always true for mock if user found)
        if ($user && $request->input('password') === 'password') { // Use a fixed password for testing
            $token = 'mock_auth_token_' . Str::random(40);
            return response()->json(['user' => $user, 'token' => $token]);
        } else {
            return response()->json(['message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'], 401);
        }
    }

     public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email', // Simulate unique
            'password' => 'required|string|min:8',
            'phone' => 'required|string|max:20', // Add unique phone validation if needed
            'city' => 'required|string|max:100',
        ]);

        // Custom unique validation for mock data
         $validator->after(function ($validator) use ($request) {
             $existingEmail = (new Collection($this->users))->firstWhere('email', $request->input('email'));
             if ($existingEmail) {
                 $validator->errors()->add('email', 'البريد الإلكتروني مسجل مسبقاً.');
             }
             // Add phone uniqueness check if needed
         });


        if ($validator->fails()) {
            return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422);
        }

        // Create new user
        $newUserId = $this->nextUserId++;
        $userData = $request->only(['name', 'email', 'phone', 'city']);
        // Simulate password hashing (not stored in mock)
        // $hashedPassword = Hash::make($request->input('password'));
        $userData['id'] = $newUserId;
        $userData['emailVerifiedAt'] = null; // Requires verification
        $userData['createdAt'] = now()->toIso8601String();
        // Add default avatar, rating etc. if desired using _createMockUser helper structure
        $newUser = $this->_createMockUser($newUserId, $userData);


        $this->users[$newUserId] = $newUser;

        $token = 'mock_auth_token_' . Str::random(40); // Simulate login after registration

        return response()->json(['user' => $newUser, 'token' => $token], 201);
    }

    public function logout(): JsonResponse
    {
        // Simulate invalidating token (no action needed for mock stateless tokens)
        return response()->json(['message' => 'تم تسجيل الخروج بنجاح']);
    }

    public function requestPasswordReset(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), ['email' => 'required|email']);
        if ($validator->fails()) { return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422); }
        // Simulate: Check if email exists, generate token, send email (log only)
        \Log::info("Password reset requested for: " . $request->input('email') . " (Mock - No email sent)");
        return response()->json(['message' => 'إذا كان البريد الإلكتروني موجوداً، فسيتم إرسال رابط إعادة التعيين.'], 200); // Use 200 for user feedback
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'password' => 'required|string|min:8',
        ]);
        if ($validator->fails()) { return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422); }
        // Simulate token validation and password update
        if ($request->input('token') === 'valid_reset_token_123') { // Use a fixed token for testing
             \Log::info("Password reset successful for token: " . $request->input('token'));
            return response()->json(['message' => 'تم إعادة تعيين كلمة المرور بنجاح.'], 200);
        } else {
            return response()->json(['message' => 'رمز إعادة التعيين غير صالح أو انتهت صلاحيته.'], 400);
        }
    }

     public function verifyEmail(string $token): JsonResponse
    {
        // Simulate token validation and marking email as verified
        // Usually involves user ID + token/signature check
        $userIdToVerify = self::CURRENT_USER_ID; // Assume token belongs to current mock user for simplicity

        if ($token === 'valid_verification_token_abc' && isset($this->users[$userIdToVerify])) {
             $this->users[$userIdToVerify]['emailVerifiedAt'] = now()->toIso8601String();
             \Log::info("Email verified for user ID: " . $userIdToVerify);
             return response()->json(['message' => 'تم التحقق من البريد الإلكتروني بنجاح.'], 200);
        } else {
             return response()->json(['message' => 'رابط التحقق غير صالح أو انتهت صلاحيته.'], 400);
        }
    }

    // --- Notifications API ---

    public function getNotifications(): JsonResponse
    {
        // Requires authentication
        $userId = self::CURRENT_USER_ID;
        $userNotifications = Arr::where($this->notifications, fn($n) => $n['userId'] === $userId);

        // Sort by date descending
        usort($userNotifications, fn($a, $b) => strtotime($b['createdAt']) <=> strtotime($a['createdAt']));

        return response()->json(array_values($userNotifications));
    }

    public function markNotificationAsRead(int $notificationId): JsonResponse
    {
        // Requires authentication
        $userId = self::CURRENT_USER_ID;
        if (!isset($this->notifications[$notificationId])) {
             return response()->json(['message' => 'Notification not found'], 404);
        }
        if ($this->notifications[$notificationId]['userId'] !== $userId) {
             return response()->json(['message' => 'Unauthorized'], 403); // Belongs to another user
        }

        $this->notifications[$notificationId]['read'] = true;
        return response()->json(null, 204);
    }

    public function markAllNotificationsAsRead(): JsonResponse
    {
        // Requires authentication
        $userId = self::CURRENT_USER_ID;
        foreach ($this->notifications as $id => $notification) {
            if ($notification['userId'] === $userId && !$notification['read']) {
                $this->notifications[$id]['read'] = true;
            }
        }
        return response()->json(null, 204);
    }

    public function deleteNotification(int $notificationId): JsonResponse
    {
         // Requires authentication
        $userId = self::CURRENT_USER_ID;
        if (!isset($this->notifications[$notificationId])) {
             return response()->json(['message' => 'Notification not found'], 404);
        }
        if ($this->notifications[$notificationId]['userId'] !== $userId) {
             return response()->json(['message' => 'Unauthorized'], 403);
        }

        unset($this->notifications[$notificationId]);
        return response()->json(null, 204);
    }

    // --- Search API ---

    public function searchAds(Request $request): JsonResponse
    {
        // Uses the same logic as getAds, but primarily driven by 'q' parameter
        if (!$request->has('q') || empty($request->input('q'))) {
            // Maybe return featured ads or latest ads instead of error?
            // Or enforce query:
             return response()->json(['message' => 'Search query (q) is required'], 400);
        }
        // Add the 'search' parameter based on 'q' for the getAds logic
        $request->merge(['search' => $request->input('q')]);
        return $this->getAds($request); // Reuse getAds logic
    }


    // --- Stats API ---

    public function getUserStats(): JsonResponse
    {
        // Requires authentication
        $userId = self::CURRENT_USER_ID;

        $userAds = (new Collection($this->ads))->where('sellerId', $userId);
        $totalAds = $userAds->count();
        $activeAds = $userAds->where('status', 'active')->count();
        $viewsTotal = $userAds->sum('viewCount');
        $favoriteCount = 0; // How many times user's ads were favorited
        foreach ($userAds->pluck('id') as $adId) {
             foreach($this->favorites as $favs) {
                 if (in_array($adId, $favs)) {
                     $favoriteCount++;
                 }
             }
        }

        // Count user's conversations
        $messagesCount = (new Collection($this->conversations))->filter(function ($conv) use ($userId) {
             $pIds = [];
             if(isset($conv['participants'])) { foreach($conv['participants'] as $p) { $pIds[] = $p['id']; } }
             elseif(isset($conv['participantIds'])) { $pIds = $conv['participantIds']; }
             return in_array($userId, $pIds);
         })->count(); // Or count unread messages instead

        $stats = [
            'totalAds' => $totalAds,
            'activeAds' => $activeAds,
            'viewsTotal' => $viewsTotal,
            'favoriteCount' => $favoriteCount,
            'messagesCount' => $messagesCount,
        ];
        return response()->json($stats);
    }

     public function getAdViews(int $adId): JsonResponse
    {
        // Requires authentication and ownership
        $userId = self::CURRENT_USER_ID;
         if (!isset($this->ads[$adId])) {
             return response()->json(['message' => 'Ad not found'], 404);
         }
         if ($this->ads[$adId]['sellerId'] !== $userId) {
             return response()->json(['message' => 'Unauthorized'], 403);
         }


        $chartData = [];
        $totalViews = 0;
        // Simulate views over the last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            // Generate mock count - slightly influenced by total views
            $count = rand(0, max(10, floor($this->ads[$adId]['viewCount'] / 10)));
            $chartData[] = ['date' => $date, 'count' => $count];
            $totalViews += $count; // Note: This simulated total might not match ad's viewCount
        }

        return response()->json([
            'total' => $this->ads[$adId]['viewCount'], // Use the actual stored viewCount for total
            'chart' => $chartData,
        ]);
    }
}
