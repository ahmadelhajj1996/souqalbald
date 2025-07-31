<?php

use App\Http\Controllers\FakerController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => 'user',
        'controller' => UserController::class,
    ],
    function () {
        Route::post('toggleActivation', 'toggleActivation')->middleware(['role:admin']);
        Route::get('getProfile', 'getProfile');
        Route::post('editProfile', 'editProfile');
    }
);

Route::group(
    [
        'prefix' => 'seller',
        'controller' => SellerController::class,
    ],
    function () {
        Route::get('getProfile', 'getProfile');
        Route::post('editProfile', 'editProfile')->middleware(['role:seller']);
    }
);

// searchs
Route::group(
    [
        'prefix' => 'search',
        'controller' => SearchController::class,
    ],
    function (): void {

        Route::post('byTitle/{type?}', 'byTitle');
        Route::post('byLocation/{type?}', 'byLocation');
    }
);

// fakers
Route::group(
    [
        'prefix' => 'faker',
        'controller' => FakerController::class,
    ],
    function (): void {

        Route::post('createFake', 'createFake');
        Route::post('fakeProduct', 'fakeProduct');
        Route::post('fakeService', 'fakeService');
        Route::post('fakeJob', 'fakeJob');
    }
);

Route::group(
    [
        'middleware' => ['role:admin|seller'],
        'prefix' => 'admin',
    ],
    function () {

        Route::group([
            'prefix' => 'products',
            'controller' => ProductController::class,
        ], function () {
            Route::post('toggleActivation', 'toggleActivation');
            Route::delete('delete', 'delete');
        });

        Route::group([
            'prefix' => 'services',
            'controller' => ServiceController::class,
        ], function () {
            Route::post('toggleActivation', 'toggleActivation');
            Route::delete('delete', 'delete');
        });

        Route::group([
            'prefix' => 'jobs',
            'controller' => JobController::class,
        ], function () {
            Route::post('toggleActivation', 'toggleActivation');
            Route::delete('delete', 'delete');
        });
    }
);
