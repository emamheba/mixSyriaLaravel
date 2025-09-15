<?php

use Illuminate\Support\Facades\Route;
use Modules\Membership\app\Http\Controllers\Backend\MembershipTypeController;
use Modules\Membership\app\Http\Controllers\Backend\MembershipController;
use Modules\Membership\app\Http\Controllers\Backend\MembershipSettingsController;
use Modules\Membership\app\Http\Controllers\Backend\UserMembershipController;
use Modules\Membership\app\Http\Controllers\Backend\MembershipHistoryController;
use Modules\Membership\app\Http\Controllers\Backend\MembershipEmailTemplateController;
use Modules\Membership\app\Http\Controllers\Backend\EnquiryFormController;
use Modules\Membership\app\Http\Controllers\User\BusinessHoursController;
use Modules\Membership\app\Http\Controllers\User\UserMembershipController as UserUserMembershipController;
use Modules\Membership\app\Http\Controllers\User\UserEnquiryFormController;
use Modules\Membership\app\Http\Controllers\Frontend\EnquiryFormController as FrontendEnquiryFormController;
use Modules\Membership\app\Http\Controllers\Frontend\FrontendMembershipController;
use Modules\Membership\app\Http\Controllers\Frontend\BuyMembershipController;
use Modules\Membership\app\Http\Controllers\Frontend\RenewMembershipController;
use Modules\Membership\app\Http\Controllers\Frontend\BuyMembershipIPNController;
use Modules\Membership\app\Http\Controllers\Frontend\RenewMembershipIPNController;


//backend membership type
Route::prefix('admin/membership/type')->middleware(['auth:admin'])->group(function() {
    Route::match(['get','post'],'/', [MembershipTypeController::class, 'all_type'])->name('admin.membership.type.all')->middleware('permission:membership-type-list');
    Route::post('edit-type', [MembershipTypeController::class, 'edit_type'])->name('admin.membership.type.edit')->middleware('permission:membership-type-edit');
    Route::post('delete/{id}', [MembershipTypeController::class, 'delete_type'])->name('admin.membership.type.delete')->middleware('permission:membership-type-delete');
    Route::post('bulk-action', [MembershipTypeController::class, 'bulk_action_type'])->name('admin.membership.type.delete.bulk.action')->middleware('permission:membership-type-bulk-delete');
    Route::get('paginate/data', [MembershipTypeController::class, 'pagination'])->name('admin.membership.type.paginate.data');
    Route::get('search-type', [MembershipTypeController::class, 'search_type'])->name('admin.membership.type.search');
});

//backend membership
Route::prefix('admin/membership')->middleware(['auth:admin'])->group(function() {
    Route::get('/', [MembershipController::class, 'all_membership'])->name('admin.membership.all')->middleware('permission:membership-list');
    Route::match(['get','post'],'add-membership', [MembershipController::class, 'add_membership'])->name('admin.membership.add')->middleware('permission:membership-add');
    Route::match(['get','post'],'edit-membership/{id}', [MembershipController::class, 'edit_membership'])->name('admin.membership.edit')->middleware('permission:membership-edit');
    Route::post('delete/{id}', [MembershipController::class, 'delete_membership'])->name('admin.membership.delete')->middleware('permission:membership-delete');
    Route::post('status/{id}', [MembershipController::class, 'status'])->name('admin.membership.status')->middleware('permission:membership-status-change');
    Route::post('bulk-action', [MembershipController::class, 'bulk_action_membership'])->name('admin.membership.delete.bulk.action')->middleware('permission:membership-bulk-delete');
    Route::get('paginate/data', [MembershipController::class, 'pagination'])->name('admin.membership.paginate.data');
    Route::get('search-type', [MembershipController::class, 'search_membership'])->name('admin.membership.search');
});

//backend membership settings
Route::prefix('admin/membership/settings')->middleware(['auth:admin'])->group(function() {
    Route::match(['get', 'post'], '/', [MembershipSettingsController::class, 'membership_settings'])->name('admin.membership.settings')->middleware('permission:membership-settings');
});

//backend user membership
Route::prefix('admin/membership/user')->middleware(['auth:admin'])->group(function() {
    Route::get('/', [UserMembershipController::class, 'all_membership'])->name('admin.user.membership.all')->middleware('permission:user-membership-list');
    Route::get('paginate/data', [UserMembershipController::class, 'pagination'])->name('admin.user.membership.paginate.data');
    Route::get('search-type', [UserMembershipController::class, 'search_membership'])->name('admin.user.membership.search');
    Route::post('status/change/{id}', [UserMembershipController::class, 'change_status'])->name('admin.user.membership.status')->middleware('permission:user-membership-status-change');
    Route::get('get/active/membership', [UserMembershipController::class, 'active_membership'])->name('admin.user.membership.active')->middleware('permission:user-membership-active');
    Route::get('get/inactive/membership', [UserMembershipController::class, 'inactive_membership'])->name('admin.user.membership.inactive')->middleware('permission:user-membership-inactive');
    Route::get('get/manual/membership', [UserMembershipController::class, 'manual_membership'])->name('admin.user.membership.manual')->middleware('permission:user-membership-manual');
    Route::get('notification/read/unread/{id}', [UserMembershipController::class, 'read_unread'])->name('admin.user.membership.read.unread');
    Route::post('update/manual/payment/status', [UserMembershipController::class, 'update_manual_payment'])->name('admin.user.membership.update.manual.payment')->middleware('permission:user-membership-manual-payment-status-change');
    Route::post('history/update/manual/payment/status', [UserMembershipController::class, 'history_update_manual_payment'])->name('admin.user.membership.history.update.manual.payment');
    Route::get('/send-email/{id?}', [UserMembershipController::class, 'send_email_to_user'])->name('admin.user.membership.email.sent');
});

//backend membership history
Route::prefix('admin/membership/user/history')->middleware(['auth:admin'])->group(function() {
    Route::get('membership/{id}', [MembershipHistoryController::class, 'user_membership_history'])->name('admin.user.membership.history');
    Route::get('paginate/data', [MembershipHistoryController::class, 'pagination'])->name('admin.user.membership.history.paginate.data');
    Route::get('search-type', [MembershipHistoryController::class, 'search_membership'])->name('admin.user.membership.history.search');
});

//backend email settings
Route::prefix('admin/membership/email-settings')->middleware(['auth:admin'])->group(function() {
    Route::match(['get', 'post'], '/user/membership/free/template', [MembershipEmailTemplateController::class, 'userMembershipFreeTemplate'])->name('admin.email.user.membership.free.template');
    Route::match(['get', 'post'], '/user/membership/purchase/template', [MembershipEmailTemplateController::class, 'userMembershipPurchaseTemplate'])->name('admin.email.user.membership.purchase.template');
    Route::match(['get', 'post'], '/user/membership/renew/template', [MembershipEmailTemplateController::class, 'userMembershipRenewTemplate'])->name('admin.email.user.membership.renew.template');
    Route::match(['get', 'post'], '/user/membership/active/template', [MembershipEmailTemplateController::class, 'userMembershipActiveTemplate'])->name('admin.email.user.membership.active.template');
    Route::match(['get', 'post'], '/user/membership/inactive/template', [MembershipEmailTemplateController::class, 'userMembershipInactiveTemplate'])->name('admin.email.user.membership.inactive.template');
    Route::match(['get', 'post'], '/user/membership/manual-payment-complete/template', [MembershipEmailTemplateController::class, 'userMembershipManualPaymentCompleteTemplate'])->name('admin.email.user.membership.manual.payment.complete.template');
    Route::match(['get', 'post'], '/user/membership/manual-payment-complete/to-admin/template', [MembershipEmailTemplateController::class, 'userMembershipManualPaymentCompleteToAdminTemplate'])->name('admin.email.user.membership.manual.payment.complete.to.admin.template');
});

//backend enquiry form
Route::prefix('admin/membership/enquiry')->middleware(['auth:admin'])->group(function() {
  Route::match(['get','post'], '/', [EnquiryFormController::class, 'all_enquiry'])->name('admin.enquiry.form.all')->middleware('permission:enquiry-form-list');
  Route::post('delete/{id}', [EnquiryFormController::class, 'delete_enquiry'])->name('admin.enquiry.form.delete')->middleware('permission:enquiry-form-delete');
  Route::post('bulk-action', [EnquiryFormController::class, 'bulk_action_enquiry'])->name('admin.enquiry.form.delete.bulk.action')->middleware('permission:enquiry-form-bulk-delete');
  Route::get('paginate/data', [EnquiryFormController::class, 'pagination'])->name('admin.enquiry.form.paginate.data');
  Route::get('search-type', [EnquiryFormController::class, 'search_enquiry'])->name('admin.enquiry.form.search');
});

//user subscription
Route::prefix('user/membership')->middleware(['auth','userEmailVerify','globalVariable', 'maintains_mode'])->group(function() {
    Route::get('all', [UserUserMembershipController::class, 'all_membership'])->name('user.membership.all');
    Route::get('paginate/data', [UserUserMembershipController::class, 'pagination'])->name('user.membership.paginate.data');
    Route::get('search-history', [UserUserMembershipController::class, 'search_history'])->name('user.membership.search');
});

//user business hours
Route::prefix('user/membership/business-hours')->middleware(['auth','userEmailVerify','globalVariable', 'maintains_mode'])->group(function() {
    Route::post('add', [BusinessHoursController::class, 'business_hours_add'])->name('user.business.hours.add');
});

//user enquiry
Route::prefix('user/enquiries')->middleware(['auth','userEmailVerify','globalVariable', 'maintains_mode'])->group(function() {
    Route::get('all', [UserEnquiryFormController::class, 'all_enquiries'])->name('user.enquiries.all');
    Route::get('paginate/data', [UserEnquiryFormController::class, 'pagination'])->name('user.enquiries.paginate.data');
    Route::get('search-history', [UserEnquiryFormController::class, 'search_history'])->name('user.enquiries.search');
    Route::post('delete/{id}', [UserEnquiryFormController::class, 'delete_enquiry'])->name('user.enquiry.form.delete');
});

//visitor enquiry form
Route::prefix('visitor/enquiry')->middleware(['globalVariable', 'maintains_mode'])->group(function() {
    Route::post('submit', [FrontendEnquiryFormController::class, 'enquiry_form_submit'])->name('visitor.enquiry.form.submit');
});

//frontend membership buy and renew
Route::middleware(['globalVariable', 'maintains_mode'])->group(function() {
    Route::post('membership/user/login', [FrontendMembershipController::class, 'user_login'])->name('membership.user.login');
    Route::post('membership/buy', [BuyMembershipController::class, 'buy_membership'])->name('user.membership.buy');
    Route::get('membership/buy/cancel-static', [BuyMembershipController::class, 'membership_payment_cancel_static'])->name('membership.buy.payment.cancel.static');
    Route::post('membership/renew', [RenewMembershipController::class, 'renew_membership'])->name('user.membership.renew');
    Route::get('membership/renew/cancel-static', [RenewMembershipController::class, 'renew_membership_payment_cancel_static'])->name('membership.renew.payment.cancel.static');
});

//membership IPN routes
Route::prefix('buy-membership')->group(function() {
    Route::get('/paypal-ipn', [BuyMembershipIPNController::class, 'paypal_ipn_for_membership'])->name('user.paypal.ipn.membership');
    Route::post('/paytm-ipn', [BuyMembershipIPNController::class, 'paytm_ipn_for_membership'])->name('user.paytm.ipn.membership');
    Route::get('/paystack-ipn', [BuyMembershipIPNController::class, 'paystack_ipn_for_membership'])->name('user.paystack.ipn.membership');
    Route::get('/mollie/ipn', [BuyMembershipIPNController::class, 'mollie_ipn_for_membership'])->name('user.mollie.ipn.membership');
    Route::get('/stripe/ipn', [BuyMembershipIPNController::class, 'stripe_ipn_for_membership'])->name('user.stripe.ipn.membership');
    Route::post('/razorpay-ipn', [BuyMembershipIPNController::class, 'razorpay_ipn_for_membership'])->name('user.razorpay.ipn.membership');
    Route::get('/flutterwave/ipn', [BuyMembershipIPNController::class, 'flutterwave_ipn_for_membership'])->name('user.flutterwave.ipn.membership');
    Route::get('/midtrans-ipn', [BuyMembershipIPNController::class, 'midtrans_ipn_for_membership'])->name('user.midtrans.ipn.membership');
    Route::post('/payfast-ipn', [BuyMembershipIPNController::class, 'payfast_ipn_for_membership'])->name('user.payfast.ipn.membership');
    Route::get('/cashfree-ipn', [BuyMembershipIPNController::class, 'cashfree_ipn_for_membership'])->name('user.cashfree.ipn.membership');
    Route::get('/instamojo-ipn', [BuyMembershipIPNController::class, 'instamojo_ipn_for_membership'])->name('user.instamojo.ipn.membership');
    Route::get('/marcadopago-ipn', [BuyMembershipIPNController::class, 'marcadopago_ipn_for_membership'])->name('user.marcadopago.ipn.membership');
    Route::get('/squareup-ipn', [BuyMembershipIPNController::class, 'squareup_ipn_for_membership'])->name('user.squareup.ipn.membership');
    Route::post('/cinetpay-ipn', [BuyMembershipIPNController::class, 'cinetpay_ipn_for_membership'])->name('user.cinetpay.ipn.membership');
    Route::post('/paytabs-ipn', [BuyMembershipIPNController::class, 'paytabs_ipn_for_membership'])->name('user.paytabs.ipn.membership');
    Route::post('/billplz-ipn', [BuyMembershipIPNController::class, 'billplz_ipn_for_membership'])->name('user.billplz.ipn.membership');
    Route::post('/zitopay-ipn', [BuyMembershipIPNController::class, 'zitopay_ipn_for_membership'])->name('user.zitopay.ipn.membership');
    Route::post('/toyyibpay-ipn', [BuyMembershipIPNController::class, 'toyyibpay_ipn_for_membership'])->name('user.toyyibpay.ipn.membership');
});

//membership renew IPN routes
Route::prefix('renew-membership')->group(function() {
    Route::get('/paypal-ipn', [RenewMembershipIPNController::class, 'paypal_ipn_for_membership'])->name('user.paypal.ipn.membership.renew');
    Route::post('/paytm-ipn', [RenewMembershipIPNController::class, 'paytm_ipn_for_membership'])->name('user.paytm.ipn.membership.renew');
    Route::get('/paystack-ipn', [RenewMembershipIPNController::class, 'paystack_ipn_for_membership'])->name('user.paystack.ipn.membership.renew');
    Route::get('/mollie/ipn', [RenewMembershipIPNController::class, 'mollie_ipn_for_membership'])->name('user.mollie.ipn.membership.renew');
    Route::get('/stripe/ipn', [RenewMembershipIPNController::class, 'stripe_ipn_for_membership'])->name('user.stripe.ipn.membership.renew');
    Route::post('/razorpay-ipn', [RenewMembershipIPNController::class, 'razorpay_ipn_for_membership'])->name('user.razorpay.ipn.membership.renew');
    Route::get('/flutterwave/ipn', [RenewMembershipIPNController::class, 'flutterwave_ipn_for_membership'])->name('user.flutterwave.ipn.membership.renew');
    Route::get('/midtrans-ipn', [RenewMembershipIPNController::class, 'midtrans_ipn_for_membership'])->name('user.midtrans.ipn.membership.renew');
    Route::post('/payfast-ipn', [RenewMembershipIPNController::class, 'payfast_ipn_for_membership'])->name('user.payfast.ipn.membership.renew');
    Route::get('/cashfree-ipn', [RenewMembershipIPNController::class, 'cashfree_ipn_for_membership'])->name('user.cashfree.ipn.membership.renew');
    Route::get('/instamojo-ipn', [RenewMembershipIPNController::class, 'instamojo_ipn_for_membership'])->name('user.instamojo.ipn.membership.renew');
    Route::get('/marcadopago-ipn', [RenewMembershipIPNController::class, 'marcadopago_ipn_for_membership'])->name('user.marcadopago.ipn.membership.renew');
    Route::get('/squareup-ipn', [RenewMembershipIPNController::class, 'squareup_ipn_for_membership'])->name('user.squareup.ipn.membership.renew');
    Route::post('/cinetpay-ipn', [RenewMembershipIPNController::class, 'cinetpay_ipn_for_membership'])->name('user.cinetpay.ipn.membership.renew');
    Route::post('/paytabs-ipn', [RenewMembershipIPNController::class, 'paytabs_ipn_for_membership'])->name('user.paytabs.ipn.membership.renew');
    Route::post('/billplz-ipn', [RenewMembershipIPNController::class, 'billplz_ipn_for_membership'])->name('user.billplz.ipn.membership.renew');
    Route::post('/zitopay-ipn', [RenewMembershipIPNController::class, 'zitopay_ipn_for_membership'])->name('user.zitopay.ipn.membership.renew');
    Route::post('/toyyibpay-ipn', [RenewMembershipIPNController::class, 'toyyibpay_ipn_for_membership'])->name('user.toyyibpay.ipn.membership.renew');
});
