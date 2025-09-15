<?php

use App\Http\Controllers\Api\Frontend\Categories\CategoryController;
use App\Http\Controllers\Api\Frontend\Chat\ChatController;
use App\Http\Controllers\Api\Frontend\GeneralSettings\GeneralSettingsController;
use App\Http\Controllers\Api\Frontend\Listing\CommentController;
use App\Http\Controllers\Api\Frontend\Listing\ListingController;
use App\Http\Controllers\Api\Frontend\Location\LocationController;
use App\Http\Controllers\Api\Frontend\Promotion\ListingPromotionController;
use App\Http\Controllers\Api\Frontend\Promotion\PromotionPackageController;
use App\Http\Controllers\Api\Frontend\Promotion\StripeWebhookController;
use App\Http\Controllers\Api\Frontend\User\AccountSettingController;
use App\Http\Controllers\Api\Frontend\User\Auth\AuthController;
use App\Http\Controllers\Api\Frontend\User\DashboardStatisticsController;
use App\Http\Controllers\Api\Frontend\User\Message\MessageController;
use App\Http\Controllers\Api\Frontend\User\ProfileController;
use App\Http\Controllers\Api\Frontend\User\UserListingController;
use Modules\Notification\App\Http\Controllers\Api\NotificationApiController;
use Modules\Notification\App\Http\Controllers\Backend\NotificationSettingsController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::prefix('v1')->middleware('api')->group(function () {

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'getCategory']);
    Route::get('/subcategories', [CategoryController::class, 'getBrands']);
    Route::get('/childcategories', [CategoryController::class, 'getSubcategories']);
    // Route::get('/subcategories/{catesubcategoriesgory}', [CategoryController::class, 'getCategory']);
    Route::get('/brands', [CategoryController::class, 'getBrands']);
    Route::get('/brands/{category}', [CategoryController::class, 'getBrandsByCategory']);



    Route::get('/listings', [ListingController::class, 'index']);
    Route::get('/listing/{listing}', [ListingController::class, 'show']);
    // Route::get('/categories/{category}', [ListingController::class, 'getListingsByCategory']);


    Route::get('/listings/{listing}/related', [ListingController::class, 'getRelatedListings']);
    Route::get('/listings/{listing}/recommended', [ListingController::class, 'getRecommendedListings']);
    Route::get('/listings/search', [ListingController::class, 'searchListings']);

    Route::get('/listing/{listing}/increment-views', [ListingController::class, 'incrementViews']);
    Route::get('/featured-listings', [ListingController::class, 'getFeaturedListings']);
    Route::get('/top-rated-listings', [ListingController::class, 'getTopRatedListings']);
    Route::get('/latest-listings', [ListingController::class, 'getLatestListings']);
    Route::get('/most-viewed-listings', [ListingController::class, 'getMostViewedListings']);
    Route::get('/listings/{listing}/comments', [CommentController::class, 'getComments']);


    Route::prefix('states')->group(function () {
        Route::get('/', [LocationController::class, 'getStates']);
        Route::get('/{state}/cities', [LocationController::class, 'getCitiesByState']);
    });

    Route::prefix('cities')->group(function () {
        Route::get('/', [LocationController::class, 'getCities']);
        Route::get('/{city}/districts', [LocationController::class, 'getDistrictsByCity']);
        Route::get('/districts', [LocationController::class, 'getDistricts']);
    });
Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetCode']); // <--- هنا التعديل
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        
        // المسارات المتعلقة بالتحقق
        Route::post('/send-verification-code', [AuthController::class, 'sendCode']);
        Route::post('/verify-code', [AuthController::class, 'verifyCode']);
        Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
        Route::post('/resend-verification-code', [AuthController::class, 'resendVerificationCode']);
          // --- NEW OTP (Phone) Verification Routes ---
        Route::post('/verify-phone', [AuthController::class, 'verifyPhone']);
        Route::post('/resend-verification-otp', [AuthController::class, 'resendVerificationOtp']);

        // --- NEW OTP (Phone) Password Reset Routes ---
        Route::post('/send-password-reset-otp', [AuthController::class, 'sendPasswordResetOtp']);
        Route::post('/reset-password-with-otp', [AuthController::class, 'resetPasswordWithOtp']);
        // مسار تسجيل الخروج يجب أن يكون محميًا
        Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
    });

    Route::middleware('auth:sanctum')->group(function () { // Use your auth guard e.g. 'auth:api' for Passport
        // Route::post('/admin/listing-promotions/{listingPromotion}/confirm-bank-transfer', [ListingPromotionController::class, 'confirmBankTransfer'])
        //     ->name('admin.promotions.confirm-bank');
    });
    Route::get('/promotion-packages', [PromotionPackageController::class, 'index']);
    // Stripe Webhook (should be outside auth middleware, but check CSRF if applicable)
    Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])->name('stripe.webhook');

    Route::prefix('user')->group(function () {

        // Route::post('/register', [AuthController::class, 'register']);
        // Route::post('/login', [AuthController::class, 'login']);
        // Route::post('/logout', [AuthController::class, 'logout']);
        // Route::post('/send-verification-code', [AuthController::class, 'sendCode']);
        // Route::post('/verify-code', [AuthController::class, 'verifyCode']);
        // Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
        // Route::post('/resend-verification-code', [AuthController::class, 'resendVerificationCode']);

        // Route::post('/send-password-reset-code', [AuthController::class, 'sendPasswordResetCode']);
        // Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        Route::post('/user/update-location', [AuthController::class, 'updateLocation']);


        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/profile', [ProfileController::class, 'fetch']);
            Route::post('/profile', [ProfileController::class, 'update']);
            Route::get('/favorites', [ProfileController::class, 'getFavorites']);
            Route::post('/listings/{listing}/favorite', [ProfileController::class, 'setfavorite']);
            Route::delete('/listings/{listing}/favorite', [ProfileController::class, 'unfavorite']);
            Route::get('/listings/{listing}/is-favorite', [ProfileController::class, 'checkIsFavorites']);
 Route::get('/dashboard/statistics', DashboardStatisticsController::class);


Route::prefix('chats')->name('chats.')->group(function () {
    Route::get('/', [ChatController::class, 'index'])->name('index');           // List chats
    Route::post('/', [ChatController::class, 'store'])->name('store');          // Create chat
    Route::get('/{chat}', [ChatController::class, 'show'])->name('show');       // Show specific chat
    Route::post('/{chat}/messages', [ChatController::class, 'storeMessage'])->name('messages.store'); // Send message
    Route::post('/{chat}/messages/{message}/seen', [ChatController::class, 'markAsSeen']); // Mark as read
});
            // Route::post('/reset-password', [AuthController::class, 'resetPassword']);
            // Route::post('/change-password', [AuthController::class, 'changePassword']);

            // ==============================================
            // NOTIFICATION ROUTES
            // ==============================================
            Route::prefix('notifications')->name('notifications.')->group(function () {
                /**
                 * Get user notifications with optional filters
                 * GET /api/v1/user/notifications
                 * Query params: ?unread=true&type=system&limit=20
                 */
                Route::get('/', [NotificationApiController::class, 'index'])->name('index');
                
                /**
                 * Get unread notifications count
                 * GET /api/v1/user/notifications/unread-count
                 */
                Route::get('/unread-count', [NotificationApiController::class, 'unreadCount'])->name('unreadCount');
                
                /**
                 * Get specific notification details
                 * GET /api/v1/user/notifications/{id}
                 */
                Route::get('/{notification}', [NotificationApiController::class, 'show'])->name('show');
                
                /**
                 * Mark a notification as read
                 * PUT /api/v1/user/notifications/{id}/read
                 */
                Route::put('/{notification}/read', [NotificationApiController::class, 'markAsRead'])->name('markAsRead');
                
                /**
                 * Mark all notifications as read
                 * PUT /api/v1/user/notifications/read-all
                 */
                Route::put('/read-all', [NotificationApiController::class, 'markAllAsRead'])->name('markAllAsRead');
                
                /**
                 * Delete a notification
                 * DELETE /api/v1/user/notifications/{id}
                 */
                Route::delete('/{notification}', [NotificationApiController::class, 'destroy'])->name('destroy');
            });

            // ==============================================
            // NOTIFICATION SETTINGS ROUTES
            // ==============================================
            Route::prefix('notification-settings')->name('notification-settings.')->group(function () {
                /**
                 * Get user notification settings
                 * GET /api/v1/user/notification-settings
                 */
                Route::get('/', [NotificationSettingsController::class, 'index'])->name('index');
                
                /**
                 * Update user notification settings
                 * PUT /api/v1/user/notification-settings
                 * Body: {settings: [{type_id: 1, channels: ['database', 'pusher'], is_enabled: true}, ...]}
                 */
                Route::put('/', [NotificationSettingsController::class, 'update'])->name('update');
            });

            // ==============================================
            // NOTIFICATION TYPES ROUTE
            // ==============================================
            /**
             * Get available notification types
             * GET /api/v1/user/notification-types
             */
            Route::get('/notification-types', [NotificationApiController::class, 'notificationTypes'])->name('notificationTypes');
            
            

            Route::get('/listings', [UserListingController::class, 'fetch']);
            Route::post('/listings', [UserListingController::class, 'store']);
            Route::put('/listings/{listing}', [UserListingController::class, 'update']);
            Route::patch('/listings/{listing}/refresh', [UserListingController::class, 'refresh']);

            // Route::get('/listings/{listing}', [UserListingController::class, 'getDetails']);

            // Route::get('/listings/{listing}/related', [UserListingController::class, 'fetch']);
            Route::delete('/listings/{listing}', [UserListingController::class, 'destroy']);



            // Route::get('/user', [UserController::class, 'show']);
            // Route::put('/user', [UserController::class, 'update']);

            // Route::apiResource('user/listings', UserListingController::class);

            Route::put('/listings/{listing}/comments/{comment}', [CommentController::class, 'editComment']);
            Route::post('/listings/{listing}/comments', [CommentController::class, 'store']);
            Route::delete('/listings/{listing}/comments/{comment}', [CommentController::class, 'destroyComment']);

            Route::post('/listings/{listing}/comments/{comment}/replies', [CommentController::class, 'storeReply']);
            Route::put('/listings/{listing}/comments/{comment}/replies/{reply}', [CommentController::class, 'editReply']);
            Route::delete('/listings/{listing}/comments/{comment}/replies/{reply}', [CommentController::class, 'destroyReply']);



            Route::post('/listings/{listing}/promote', [ListingPromotionController::class, 'initiatePromotion']);

            Route::get('/listing-promotions', [ListingPromotionController::class, 'userPromotions'])
                ->name('user.listing-promotions');


            Route::get('/messages', [MessageController::class, 'index']);
            Route::post('/messages', [MessageController::class, 'store']);
            Route::post('/messages/mark-as-read', [MessageController::class, 'markAsRead']);


            Route::prefix('account')->group(function () {
                Route::get('account-settings', [AccountSettingController::class, 'getAccountSettings']);
                Route::post('/change-password', [AccountSettingController::class, 'changePassword']);
                Route::get('settings', [AccountSettingController::class, 'getUserSettings']);
                Route::put('settings', [AccountSettingController::class, 'updateUserSettings']);
                Route::put('security-settings', [AccountSettingController::class, 'updateSecuritySettings']);
                Route::put('notification-settings', [AccountSettingController::class, 'updateNotificationSettings']);
                Route::put('general-settings', [AccountSettingController::class, 'updateGeneralSettings']);
                Route::post('reset-settings', [AccountSettingController::class, 'resetSettingsToDefault']);
                Route::post('deactivate-account', [AccountSettingController::class, 'deactivateAccount']);
                Route::delete('delete-account', [AccountSettingController::class, 'deleteAccount']);
                Route::post('cancel-deactivation', [AccountSettingController::class, 'cancelDeactivation']);
                Route::post('verify-profile', [AccountSettingController::class, 'verifyProfile']);
                Route::get('verification-status', [AccountSettingController::class, 'getVerificationStatus']);
            });
        });
    });

    Route::prefix('settings')->group(function () {
        Route::get('/site-identity', [GeneralSettingsController::class, 'siteIdentity']);
        Route::get('/basic', [GeneralSettingsController::class, 'basicSettings']);
        Route::get('/navbar-variant', [GeneralSettingsController::class, 'globalVariantNavbar']);
        Route::get('/footer-variant', [GeneralSettingsController::class, 'globalVariantFooter']);
        Route::get('/colors', [GeneralSettingsController::class, 'colorSettings']);
        Route::get('/seo', [GeneralSettingsController::class, 'seoSettings']);
        Route::get('/languages', [GeneralSettingsController::class, 'languages']);
        Route::get('/all', [GeneralSettingsController::class, 'allStaticOptions']);
        Route::get('/listing-settings', [GeneralSettingsController::class, 'getListingCreateSettings']);
    });
});
