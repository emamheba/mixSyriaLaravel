<?php

use Illuminate\Support\Facades\Route;

// Controllers Imports
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\language\LanguageController;

// Common Controllers
use App\Http\Controllers\Common\GetCategoryController;
use App\Http\Controllers\Common\NewTagAddController;
use App\Http\Controllers\Common\AdminUserController;

// Frontend Controllers
use App\Http\Controllers\Frontend\FrontendListingController;
use App\Http\Controllers\Frontend\CategoryWiseListingController;

// Backend (Admin) Controllers
use App\Http\Controllers\Backend\AdminDashboardController;
use App\Http\Controllers\Backend\AdminProfileController;
use App\Http\Controllers\Backend\Categories\CategoryController;
use App\Http\Controllers\Backend\Categories\SubCategoryController;
use App\Http\Controllers\Backend\Categories\ChildCategoryController;
use App\Http\Controllers\Backend\TagController;
use App\Http\Controllers\Backend\UserManageController;
use App\Http\Controllers\Backend\UserVerificationController;
use App\Http\Controllers\Backend\AdminListingController;
use App\Http\Controllers\Backend\UserListingManageController;
use App\Http\Controllers\Backend\ListingReportController;
use App\Http\Controllers\Backend\ReportReasonController;
use App\Http\Controllers\Backend\GeneralSettingsController;
use App\Http\Controllers\Backend\MapSettings;
use App\Http\Controllers\Backend\EmailSettingsController;
use App\Http\Controllers\Backend\EmailTemplateController;
use App\Http\Controllers\Backend\Promotion\PromotionController;
use Modules\Brand\app\Http\Controllers\BrandController;



// Language Switcher
Route::get('/lang/{locale}', [LanguageController::class, 'swap']);

// Common Helper Routes (used in both frontend and backend forms)
Route::group(['middleware' => ['setlang', 'globalVariable']], function () {
    Route::controller(AdminUserController::class)->group(function () {
        Route::post('get-state', 'get_country_state')->name('au.state.all');
        Route::post('get-city', 'get_state_city')->name('au.city.all');
        Route::post('get-subcategory', 'get_subcategory')->name('au.subcategory.all');
    });

    Route::post('/add-new-tag', [NewTagAddController::class, 'addNewTag'])->name('add.new.tag');
    Route::post('get-subcategory', [GetCategoryController::class, 'get_sub_category'])->name('get.subcategory');
    Route::post('get-child-category', [GetCategoryController::class, 'get_child_category'])->name('get.subcategory.with.child.category');
});


/*
|--------------------------------------------------------------------------
| 2. FRONTEND / USER ROUTES
|--------------------------------------------------------------------------
|
|
*/

Route::prefix('/user')->group(function () {
    // User Authentication
    Route::get('/login', [LoginController::class, 'showUserLoginForm'])->name('user.form.login');
    Route::post('/login', [LoginController::class, 'userLogin'])->name('user.login');

    // User Registration
    Route::get('/register', function () {
        return view('frontend.auth.register');
    });
    Route::post('/register', [RegisterController::class, 'userRegister'])->name('user.register');
});


/*
|--------------------------------------------------------------------------
| 3. ADMIN PANEL ROUTES
|--------------------------------------------------------------------------
|
|
*/

Route::prefix('admin')->group(function () {

    // --- Admin Authentication (Publicly Accessible) ---
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('admin.form.login');
    Route::post('/login', [LoginController::class, 'adminLogin'])->name('admin.login');

    // --- Authenticated Admin Routes ---
    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // --- Admin Profile & Logout ---
        Route::post('/logout', [AdminProfileController::class, 'adminLogout'])->name('admin.logout');
        Route::get('/profile-update', [AdminProfileController::class, 'adminProfile'])->name('admin.profile.edit');
        Route::post('/profile-update', [AdminProfileController::class, 'adminProfileUpdate'])->name('admin.profile.update');
        Route::get('/password-change', [AdminProfileController::class, 'adminPassword'])->name('admin.profile.password');
        Route::post('/password-change', [AdminProfileController::class, 'adminPasswordChange'])->name('admin.profile.password.update');

        // --- Categories & Tags ---
        Route::resource('categories', CategoryController::class);
        Route::put('categories/change-status/{id}', [CategoryController::class, 'changeStatus']);

        Route::resource('subcategories', SubCategoryController::class);
        Route::put('subcategories/change-status/{id}', [SubCategoryController::class, 'changeStatus']);// Note: The name is the same as category, which might be intentional or a typo in the original file. Kept as is.

        Route::resource('childcategories', ChildCategoryController::class);
        Route::put('childcategories/change-status/{id}', [ChildCategoryController::class, 'changeStatus']);// Note: The name is the same as category, which might be intentional or a typo in the original file. Kept as is.

        Route::resource('tags', TagController::class);
        Route::put('tags/change-status/{id}', [TagController::class, 'changeStatus']);

        // Helper routes for categories
        Route::get('/get-brands/{category_id}', [SubCategoryController::class, 'getBrands']);
        Route::get('get-brands-by-category/{category_id}', [ChildCategoryController::class, 'getBrandsByCategory'])->name('admin.get.brands.by.category');
        Route::get('get-subcategories-by-brand/{brand_id}', [ChildCategoryController::class, 'getSubCategoriesByBrand'])->name('admin.get.subcategories.by.brand');
        Route::get('get-subcategories-by-category/{category_id}', [ChildCategoryController::class, 'getSubCategoriesByCategory'])->name('admin.get.subcategories.by.category');

        // Brand status (assuming it's admin-only)
        Route::put('brands/change-status/{id}', [BrandController::class, 'changeStatus']);

        // --- User Management ---
        Route::prefix('users')->name('admin.users.')->group(function () {
            Route::get('/', [UserManageController::class, 'all_users'])->name('page');
            Route::match(['get', 'post'], '/add', [UserManageController::class, 'add_user'])->name('add');
            Route::get('/search', [UserManageController::class, 'search_user'])->name('search');
            Route::post('/edit-info', [UserManageController::class, 'edit_info'])->name('edit.info');
            Route::get('/status/{id}', [UserManageController::class, 'change_status'])->name('change.status');
            Route::get('/verify-email/{id}', [UserManageController::class, 'verify_user_email'])->name('verify.email');
            Route::delete('/delete/{id}', [UserManageController::class, 'delete_user'])->name('delete');
            Route::post('/change-password', [UserManageController::class, 'change_password'])->name('change.password');

            // Trash management routes
            Route::get('/trash', [UserManageController::class, 'trashed_users'])->name('trash');
            Route::get('/trash/search', [UserManageController::class, 'search_trashed_user'])->name('trash.search');
            Route::get('/trash/pagination', [UserManageController::class, 'pagination_trashed_users'])->name('trash.pagination');
            Route::post('/restore/{id}', [UserManageController::class, 'restore_user'])->name('restore');
            Route::delete('/permanent-delete/{id}', [UserManageController::class, 'permanent_delete_user'])->name('permanent.delete');

            // User Verification
            Route::prefix('verification')->group(function () {
                Route::get('/', [UserVerificationController::class, 'index'])->name('verification');
                Route::get('/requests', [UserVerificationController::class, 'verification_requests'])->name('verification.requests');
                Route::get('/identity-details', [UserVerificationController::class, 'identity_details'])->name('identity.details');
                Route::post('/verify-status', [UserVerificationController::class, 'identity_verify_status'])->name('verify.status');
                Route::post('/verify-decline', [UserVerificationController::class, 'identity_verify_decline'])->name('verify.decline');
                Route::get('/search', [UserVerificationController::class, 'search_verification_requests'])->name('verification.search');
                Route::get('/filter', [UserVerificationController::class, 'filter_verification_requests'])->name('verification.filter');
            });
        });

        // --- Listings Management ---
        Route::prefix('listings')->group(function () {
            // All admin listings
            Route::get('all', [AdminListingController::class, 'adminAllListings'])->name('admin.all.listings')->permission('admin-listing-list');
            Route::match(['get', 'post'], 'add', [AdminListingController::class, 'adminAddListing'])->name('admin.add.new.listing')->permission('admin-listing-add');
            Route::match(['get', 'post'], '/admin-edit-listing/{id?}', [AdminListingController::class, 'adminEditListing'])->name('admin.edit.listing')->permission('admin-listing-edit');
            Route::get('/admin-search', [AdminListingController::class, 'adminSearchListing'])->name('admin.search.listings');
            Route::get('/admin-paginate', [AdminListingController::class, 'adminPaginate'])->name('admin.paginate.listings');
            Route::post('/admin-delete/{id}', [AdminListingController::class, 'adminListingDelete'])->name('admin.delete.listings')->permission('admin-listing-delete');
            Route::post('/admin-bulk-action', [AdminListingController::class, 'bulkAction'])->name('admin.bulk.action.listing')->permission('admin-listing-bulk-delete');
            Route::post('/admin-published/{id}', [AdminListingController::class, 'adminListingPublishedStatus'])->name('admin.listings.published.status.change.by')->permission('admin-listing-published-status-change');
            Route::post('/admin-status/{id}', [AdminListingController::class, 'adminChangeStatus'])->name('admin.listings.status.change.by')->permission('admin-listing-status-change');

            // All user listings
            Route::get('/user-all-listings', [UserListingManageController::class, 'all_listings'])->name('admin.user.all.listings')->permission('user-listing-list');
            Route::get('/details/{id}', [UserListingManageController::class, 'listingDetails'])->name('admin.listings.details');
            Route::post('/user-all/approved', [UserListingManageController::class, 'userListingsAllApproved'])->name('admin.listings.user.all.approved')->permission('user-listing-approved');
            Route::post('/published/{id}', [UserListingManageController::class, 'listingPublishedStatus'])->name('admin.listings.published.status.change')->permission('user-listing-published-status-change');
            Route::post('/status/{id}', [UserListingManageController::class, 'changeStatus'])->name('admin.listings.status.change')->permission('user-listing-status-change');
            Route::get('/search', [UserListingManageController::class, 'searchListing'])->name('admin.listings.search');
            Route::get('/paginate', [UserListingManageController::class, 'paginate'])->name('admin.listings.paginate');
            Route::post('/delete/{id}', [UserListingManageController::class, 'listingDelete'])->name('admin.listings.delete')->permission('user-listing-delete');
            Route::post('/bulk-action', [UserListingManageController::class, 'bulkAction'])->name('admin.listing.bulk.action')->permission('user-listing-bulk-delete');

            // Listings report & reasons
            Route::prefix('report')->group(function () {
                Route::match(['get', 'post'], '/', [ListingReportController::class, 'all_report'])->name('admin.listing.report.all')->permission('listing-report-list');
                Route::post('/edit-report', [ListingReportController::class, 'edit_report'])->name('admin.listing.report.edit')->permission('listing-report-edit');
                Route::post('/delete/{id}', [ListingReportController::class, 'delete_report'])->name('admin.listing.report.delete')->permission('listing-report-delete');
                Route::post('/bulk-action', [ListingReportController::class, 'bulk_action_report'])->name('admin.listing.report.delete.bulk.action')->permission('listing-report-bulk-delete');
                Route::get('/paginate/data', [ListingReportController::class, 'pagination'])->name('admin.listing.report.paginate.data');
                Route::get('/search', [ListingReportController::class, 'search_report'])->name('admin.listing.report.search');
                Route::get('/detail/{id}', [ListingReportController::class, 'report_detail'])->name('admin.listing.report.detail');
                Route::post('/update-status/{id}', [ListingReportController::class, 'update_status'])->name('admin.listing.report.update.status');

                // Report reasons
                Route::prefix('reason')->group(function () {
                    Route::match(['get', 'post'], '/', [ReportReasonController::class, 'all_reason'])->name('admin.report.reason.all')->permission('report-reason-list');
                    Route::post('/edit-reason', [ReportReasonController::class, 'edit_reason'])->name('admin.report.reason.edit')->permission('report-reason-edit');
                    Route::post('/delete/{id}', [ReportReasonController::class, 'delete_reason'])->name('admin.report.reason.delete')->permission('report-reason-delete');
                    Route::post('/bulk-action', [ReportReasonController::class, 'bulk_action_reason'])->name('admin.report.reason.delete.bulk.action')->permission('report-reason-bulk-delete');
                    Route::get('/paginate/data', [ReportReasonController::class, 'pagination'])->name('admin.report.reason.paginate.data');
                    Route::get('/search', [ReportReasonController::class, 'search_reason'])->name('admin.report.reason.search');
                });
            });
        });

        // --- Settings ---
        Route::prefix('general-settings')->group(function () {
            Route::get('/site-identity', [GeneralSettingsController::class, 'siteIdentity'])->name('admin.general.site.identity')->permission('site-identity-settings');
            Route::post('/site-identity', [GeneralSettingsController::class, 'updateSiteIdentity']);

            Route::get('/color-settings', [GeneralSettingsController::class, 'colorSettings'])->name('admin.general.color.settings')->permission('color-settings');
            Route::post('/color-settings', [GeneralSettingsController::class, 'updateColorSettings']);

            Route::get('/basic-settings', [GeneralSettingsController::class, 'basicSettings'])->name('admin.general.basic.settings')->permission('basic-settings');
            Route::post('/basic-settings', [GeneralSettingsController::class, 'updateBasicSettings']);

            Route::match(['get', 'post'], '/register-page', [GeneralSettingsController::class, 'loginRegisterPageSettings'])->name('admin.login.register.page.settings')->permission('login-register-page-settings');
            Route::match(['get', 'post'], '/listing-create-page/settings', [GeneralSettingsController::class, 'listingCreateSettings'])->name('admin.listing.create.settings')->permission('listing-create-page-settings');
            Route::get('/scripts', [GeneralSettingsController::class, 'scriptsSettings'])->name('admin.general.scripts.settings')->permission('scripts-settings');
            Route::post('/scripts', [GeneralSettingsController::class, 'updateScriptsSettings']);
        });

        Route::prefix('map-settings')->group(function () {
            Route::get('/add-page', [MapSettings::class, 'addMapSettings'])->name('admin.map.settings.page')->permission('google-map-settings');
            Route::post('/add-page', [MapSettings::class, 'UpdateMapSettings']);
        });

        Route::prefix('email-settings')->group(function () {
            Route::post('/basic-settings', [EmailSettingsController::class, 'updateEmailSettings']);
            Route::get('/', [EmailSettingsController::class, 'smtpSettings'])->name('admin.email.smtp.settings')->permission('smtp-settings');
            Route::post('/update-smtp', [EmailSettingsController::class, 'updateSmtpSettings'])->name('admin.email.smtp.update.settings');
            Route::post('/test-smtp', [EmailSettingsController::class, 'testSmtpSettings'])->name('admin.email.smtp.settings.test');

            // Email Templates
            Route::get('/templates', [EmailTemplateController::class, 'allEmailTemplates'])->name('admin.email.template.all');
            Route::match(['get', 'post'], '/global-template', [EmailTemplateController::class, 'globalEmailTemplateSettings'])->name('admin.email.global.template');
            Route::match(['get', 'post'], '/user/register/template', [EmailTemplateController::class, 'userRegisterTemplate'])->name('admin.email.user.register.template');
            Route::match(['get', 'post'], '/user/identity-verification/template', [EmailTemplateController::class, 'userIdentityVerificationTemplate'])->name('admin.email.user.identity.verification.template');
            Route::match(['get', 'post'], '/user/email-verify/template', [EmailTemplateController::class, 'userEmailVerifyTemplate'])->name('admin.email.user.verify.template');
            Route::match(['get', 'post'], '/user/wallet-deposit/template', [EmailTemplateController::class, 'userWalletDepositTemplate'])->name('admin.email.user.wallet.deposit.template');
            Route::match(['get', 'post'], '/user/new-listing-approval/template', [EmailTemplateController::class, 'userNewListingApprovalTemplate'])->name('admin.email.user.new.listing.approval.template');
            Route::match(['get', 'post'], '/user/new-listing-publish/template', [EmailTemplateController::class, 'userNewListingPublishTemplate'])->name('admin.email.user.new.listing.publish.template');
            Route::match(['get', 'post'], '/user/new-listing-unpublished/template', [EmailTemplateController::class, 'userNewListingUnpublishedTemplate'])->name('admin.email.user.new.listing.unpublished.template');
            Route::match(['get', 'post'], '/user/guest-listing-add/template', [EmailTemplateController::class, 'userGuestAddNewListingTemplate'])->name('admin.email.user.guest.add.listing.template');
            Route::match(['get', 'post'], '/user/guest-listing-approve/template', [EmailTemplateController::class, 'userGuestApproveListingTemplate'])->name('admin.email.user.guest.approve.listing.template');
            Route::match(['get', 'post'], '/user/guest-listing-publish/template', [EmailTemplateController::class, 'userGuestPublishListingTemplate'])->name('admin.email.user.guest.publish.listing.template');
        });

        // --- Promotions ---
        Route::prefix('promotions')->name('promotions.')->group(function () {
            // Packages Management
            Route::prefix('packages')->name('packages.')->group(function () {
                Route::get('/', [PromotionController::class, 'packages'])->name('index');
                Route::get('/create', [PromotionController::class, 'createPackage'])->name('create');
                Route::post('/store', [PromotionController::class, 'storePackage'])->name('store');
                Route::get('/{package}/edit', [PromotionController::class, 'editPackage'])->name('edit');
                Route::put('/{package}', [PromotionController::class, 'updatePackage'])->name('update');
                Route::delete('/{package}', [PromotionController::class, 'deletePackage'])->name('delete');
                Route::post('/{package}/toggle-status', [PromotionController::class, 'togglePackageStatus'])->name('toggle-status');
            });

            // Promotion Requests Management
            Route::prefix('requests')->name('requests.')->group(function () {
                Route::get('/', [PromotionController::class, 'requests'])->name('index');
                Route::get('/{request}/show', [PromotionController::class, 'showRequest'])->name('show');
                Route::post('/{request}/approve-bank-transfer', [PromotionController::class, 'approveBankTransfer'])->name('approve-bank-transfer');
                Route::post('/{request}/reject-bank-transfer', [PromotionController::class, 'rejectBankTransfer'])->name('reject-bank-transfer');
                Route::delete('/{request}', [PromotionController::class, 'deleteRequest'])->name('delete');
                Route::post('/bulk-approve-bank-transfers', [PromotionController::class, 'bulkApproveBankTransfers'])->name('bulk-approve-bank-transfers');
            });
        });

    }); // End of Authenticated Admin Routes
}); // End of Admin Prefix
