<?php

use Illuminate\Support\Facades\Route;
use Modules\Wallet\app\Http\Controllers\Frontend\UserWalletController;
use Modules\Wallet\app\Http\Controllers\Frontend\UserWalletPaymentController;
use Modules\Wallet\app\Http\Controllers\Backend\WalletController;

// backend routes
Route::group(['prefix' => 'admin/wallet', 'as' => 'admin.wallet.','middleware' => ['auth:admin', 'globalVariable']], function () {
    Route::match(['get','post'],'wallet/deposit-settings', [WalletController::class, 'deposit_settings'])->name('deposit.settings')->permission('deposit-settings');
    Route::get('/lists', [WalletController::class, 'wallet_lists'])->name('lists')->permission('deposit-list');
    Route::post('/status/{id}', [WalletController::class, 'change_status'])->name('status')->permission('complete-manual-deposit-status');
    Route::get('/history/records', [WalletController::class, 'wallet_history'])->name('history')->permission('deposit-history-details');
    Route::post('/history/records/status/{id}', [WalletController::class, 'wallet_history_status'])->name('history.status');
    Route::post('/deposit/create-by-admin', [WalletController::class, 'depositCreateByAdmin'])->name('deposit.create');
    Route::get('paginate/data', [WalletController::class,'pagination'])->name('paginate.data');
    Route::get('search-wallet', [WalletController::class,'search_wallet'])->name('search');
    Route::get('history/paginate/data', [WalletController::class,'pagination_history'])->name('history.paginate.data');
    Route::get('history/search-wallet', [WalletController::class,'search_wallet_history'])->name('history.search');
    Route::get('history/filter-wallet', [WalletController::class,'wallet_history_filter'])->name('history.filter');
  });

//user routes
Route::middleware(['auth', 'setlang', 'globalVariable'])->prefix('user')->as('user.')->group(function () {

  // Wallet history
  Route::get('/wallet-history', [UserWalletController::class, 'wallet_history'])->name('wallet.history');

  // Wallet deposit
  Route::post('/wallet/deposit', [UserWalletController::class, 'deposit'])->name('wallet.deposit');
  Route::get('wallet/deposit-cancel-static', [UserWalletController::class, 'deposit_payment_cancel_static'])->name('wallet.deposit.payment.cancel.static');
  Route::get('wallet/deposit-success', [UserWalletController::class, 'deposit_payment_success'])->name('wallet.deposit.payment.success');

  // Pagination and search
  Route::get('paginate/data', [UserWalletController::class, 'pagination'])->name('wallet.paginate.data');
  Route::get('search-history', [UserWalletController::class, 'search_history'])->name('wallet.search');
});

//wallet payment routes
Route::group(['prefix' => 'wallet'], function () {
    Route::get('/paypal-ipn', [UserWalletPaymentController::class,'paypal_ipn_for_wallet'])->name('user.paypal.ipn.wallet');
    Route::post('/paytm-ipn', [UserWalletPaymentController::class,'paytm_ipn_for_wallet'])->name('user.paytm.ipn.wallet');
    Route::get('/paystack-ipn', [UserWalletPaymentController::class,'paystack_ipn_for_wallet'])->name('user.paystack.ipn.wallet');
    Route::get('/mollie/ipn', [UserWalletPaymentController::class,'mollie_ipn_for_wallet'])->name('user.mollie.ipn.wallet');
    Route::get('/stripe/ipn', [UserWalletPaymentController::class,'stripe_ipn_for_wallet'])->name('user.stripe.ipn.wallet');
    Route::post('/razorpay-ipn', [UserWalletPaymentController::class,'razorpay_ipn_for_wallet'])->name('user.razorpay.ipn.wallet');
    Route::get('/flutterwave/ipn', [UserWalletPaymentController::class,'flutterwave_ipn_for_wallet'])->name('user.flutterwave.ipn.wallet');
    Route::get('/midtrans-ipn', [UserWalletPaymentController::class,'midtrans_ipn_for_wallet'])->name('user.midtrans.ipn.wallet');
    Route::post('/payfast-ipn', [UserWalletPaymentController::class,'payfast_ipn_for_wallet'])->name('user.payfast.ipn.wallet');
    Route::get('/cashfree-ipn', [UserWalletPaymentController::class,'cashfree_ipn_for_wallet'])->name('user.cashfree.ipn.wallet');
    Route::get('/instamojo-ipn', [UserWalletPaymentController::class,'instamojo_ipn_for_wallet'])->name('user.instamojo.ipn.wallet');
    Route::get('/marcadopago-ipn', [UserWalletPaymentController::class,'mercadopago_ipn_for_wallet'])->name('user.marcadopago.ipn.wallet');
    Route::get('/squareup-ipn', [UserWalletPaymentController::class,'squareup_ipn_for_wallet'])->name('user.squareup.ipn.wallet');
    Route::post('/cinetpay-ipn', [UserWalletPaymentController::class,'cinetpay_ipn_for_wallet'])->name('user.cinetpay.ipn.wallet');
    Route::post('/paytabs-ipn', [UserWalletPaymentController::class,'paytabs_ipn_for_wallet'])->name('user.paytabs.ipn.wallet');
    Route::post('/billplz-ipn', [UserWalletPaymentController::class,'billplz_ipn_for_wallet'])->name('user.billplz.ipn.wallet');
    Route::post('/zitopay-ipn', [UserWalletPaymentController::class,'zitopay_ipn_for_wallet'])->name('user.zitopay.ipn.wallet');
    Route::post('/kineticpay-ipn', [UserWalletPaymentController::class,'kineticpay_ipn_for_wallet'])->name('user.kineticpay.ipn.wallet');
    Route::post('/toyyibpay-ipn',[UserWalletPaymentController::class,'toyyibpay_ipn_for_wallet'])->name('user.toyyibpay.ipn.wallet');
});
