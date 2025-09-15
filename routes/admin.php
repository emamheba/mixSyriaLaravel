<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;




// Route::prefix('admin')->group(function () {
// });

Route::post('/llogin',[LoginController::class,'adminLogin'])->name('admin.login');
