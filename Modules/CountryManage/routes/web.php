<?php

use Illuminate\Support\Facades\Route;

use \Modules\CountryManage\app\Http\Controllers\CountryController;
use Modules\CountryManage\app\Http\Controllers\LocationController;
use \Modules\CountryManage\app\Http\Controllers\StateController;
use \Modules\CountryManage\app\Http\Controllers\CityController;

Route::group(['prefix' => 'admin/location', 'middleware' => ['auth:admin']], function () {
  Route::group(['prefix' => 'country'], function () {
    Route::match(['get', 'post'], '/all-country', [CountryController::class, 'all_country'])->name('admin.country.all')->permission('country-list');
    Route::post('edit-country/{id?}', [CountryController::class, 'edit_country'])->name('admin.country.edit')->permission('country-edit');
    Route::post('change-status/{id}', [CountryController::class, 'change_status_country'])->name('admin.country.status')->permission('country-status-change');
    Route::post('delete/{id}', [CountryController::class, 'delete_country'])->name('admin.country.delete')->permission('country-delete');
    Route::post('bulk-action', [CountryController::class, 'bulk_action_country'])->name('admin.country.delete.bulk.action')->permission('country-bulk-delete');
    Route::get('paginate/data', [CountryController::class, 'pagination'])->name('admin.country.paginate.data');
    Route::get('search-country', [CountryController::class, 'search_country'])->name('admin.country.search');
    Route::get('csv/import', [CountryController::class, 'import_settings'])->name('admin.country.import.csv.settings')->permission('country-csv-file-import');
    Route::post('csv/import', [CountryController::class, 'update_import_settings'])->name('admin.country.import.csv.update.settings');
    Route::post('csv/import/database', [CountryController::class, 'import_to_database_settings'])->name('admin.country.import.database');
  });

  Route::group(['prefix' => 'state'], function () {
    Route::match(['get', 'post'], 'all-state', [StateController::class, 'all_state'])->name('admin.state.all')->permission('state-list');
    Route::post('edit-state/{id?}', [StateController::class, 'edit_state'])->name('admin.state.edit')->permission('state-edit');
    Route::post('change-status/{id}', [StateController::class, 'change_status_state'])->name('admin.state.status')->permission('state-status-change');
    Route::post('delete/{id}', [StateController::class, 'delete_state'])->name('admin.state.delete')->permission('state-delete');
    Route::post('bulk-action', [StateController::class, 'bulk_action_state'])->name('admin.state.delete.bulk.action')->permission('state-bulk-delete');
    Route::get('paginate/data', [StateController::class, 'pagination'])->name('admin.state.paginate.data');
    Route::get('search-state', [StateController::class, 'search_state'])->name('admin.state.search');
    Route::get('csv/import', [StateController::class, 'import_settings'])->name('admin.state.import.csv.settings')->permission('state-csv-file-import');
    Route::post('csv/import', [StateController::class, 'update_import_settings'])->name('admin.state.import.csv.update.settings');
    Route::post('csv/import/database', [StateController::class, 'import_to_database_settings'])->name('admin.state.import.database');
    Route::get('get-states', [StateController::class, 'getStates'])->name('get.states');
  });


  Route::group(['prefix' => 'city'], function () {
    Route::match(['get', 'post'], 'all-city', [CityController::class, 'all_city'])->name('admin.city.all')->permission('city-list');
    Route::post('edit-city/{id?}', [CityController::class, 'edit_city'])->name('admin.city.edit')->permission('city-edit');
    Route::post('change-status/{id}', [CityController::class, 'city_status'])->name('admin.city.status')->permission('city-status-change');
    Route::post('delete/{id}', [CityController::class, 'delete_city'])->name('admin.city.delete')->permission('city-delete');
    Route::post('bulk-action', [CityController::class, 'bulk_action_city'])->name('admin.city.delete.bulk.action')->permission('city-bulk-delete');
    Route::get('paginate/data', [CityController::class, 'pagination'])->name('admin.city.paginate.data');
    Route::get('search-city', [CityController::class, 'search_city'])->name('admin.city.search');
    Route::get('csv/import', [CityController::class, 'import_settings'])->name('admin.city.import.csv.settings')->permission('city-csv-file-import');
    Route::post('csv/import', [CityController::class, 'update_import_settings'])->name('admin.city.import.csv.update.settings');
    Route::post('csv/import/database', [CityController::class, 'import_to_database_settings'])->name('admin.city.import.database');
    Route::get('get-cities', [CityController::class, 'getCities'])->name('get.cities');
  });







              // states Routes
    Route::match(['get', 'post'], 'all-state', [LocationController::class, 'all_state'])->name('admin.all.state');
    Route::post('edit-state', [LocationController::class, 'edit_state'])->name('admin.edit.state');
    Route::post('change-status-state/{id}', [LocationController::class, 'change_status_state'])->name('admin.state.status');
    Route::post('delete-state/{id}', [LocationController::class, 'delete_state'])->name('admin.delete.state');
    Route::post('bulk-action-state', [LocationController::class, 'bulk_action_state'])->name('admin.bulk.action.state');
    Route::get('search-state', [LocationController::class, 'search_state'])->name('admin.search.state');
                  
    // Cities Routes
    Route::match(['get', 'post'], 'all-city', [LocationController::class, 'all_city'])->name('admin.all.city');
    Route::post('edit-city', [LocationController::class, 'edit_city'])->name('admin.edit.city');
    Route::post('change-status-city/{id}', [LocationController::class, 'city_status'])->name('admin.city.status');
    Route::post('delete-city/{id}', [LocationController::class, 'delete_city'])->name('admin.delete.city');
    Route::post('bulk-action-city', [LocationController::class, 'bulk_action_city'])->name('admin.bulk.action.city');
    Route::get('search-city', [LocationController::class, 'search_city'])->name('admin.search.city');

    //  DISTRICT ROUTES ---
    Route::match(['get', 'post'], 'all-district', [LocationController::class, 'all_district'])->name('admin.all.district');
    Route::post('edit-district', [LocationController::class, 'edit_district'])->name('admin.edit.district');
    Route::post('change-status-district/{id}', [LocationController::class, 'district_status'])->name('admin.district.status');
    Route::post('delete-district/{id}', [LocationController::class, 'delete_district'])->name('admin.delete.district');
    Route::post('bulk-action-district', [LocationController::class, 'bulk_action_district'])->name('admin.bulk.action.district');
    Route::get('search-district', [LocationController::class, 'search_district'])->name('admin.search.district');

    // AJAX Routes
    Route::get('get-states', [LocationController::class, 'getStates'])->name('admin.get.states');
    Route::get('get-cities', [LocationController::class, 'getCities'])->name('admin.get.cities');
    Route::get('get-districts', [LocationController::class, 'getDistricts'])->name('admin.get.districts'); // New
});
