<?php

use Illuminate\Support\Facades\Route;
use Modules\Brand\app\Http\Controllers\BrandController;



Route::group([
  'prefix'     => 'admin',
  'middleware' => ['auth:admin'],
], function () {
  Route::resource('brands', BrandController::class);
});

