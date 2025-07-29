<?php

use App\Http\Controllers\AdsController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Middleware\EnsureApiTokenIsValid;
use App\Http\Middleware\SetLanguage;
use Illuminate\Support\Facades\Route;

Route::middleware([SetLanguage::class])->group(function () {

    Route::post('/login', [RegisterController::class, 'login']);
    Route::post('customer/register', [RegisterController::class, 'customerRegister']);
    Route::post('seller/register', [RegisterController::class, 'sellerRegister']);
    Route::post('guest/register', [RegisterController::class, 'guestRegister']);

    Route::post('register/social/{provider}', [RegisterController::class, 'socialRegister']);

    Route::post('password/forgot', [RegisterController::class, 'sendResetOtp']);
    Route::post('password/verify-otp', [RegisterController::class, 'verifyOtp']);
    Route::post('password/reset', [RegisterController::class, 'resetPassword']);

    Route::middleware([EnsureApiTokenIsValid::class, 'auth:api'])->group(function () {
        Route::post('update-password', [RegisterController::class, 'updatePassword']);
        Route::post('logout', [RegisterController::class, 'logout']);

        Route::get('categories', [CategoryController::class, 'index']);
        Route::get('categories/{category}', [CategoryController::class, 'show']);
        Route::get('sub-categories', [SubCategoryController::class, 'index']);
        Route::get('sub-categories/{sub_category}', [SubCategoryController::class, 'show']);

        Route::get('/sellers', [SellerController::class, 'index']);
        Route::get('/customers', [CustomerController::class, 'index']);

        Route::get('products', [ProductController::class, 'index']);
        Route::get('products/me', [ProductController::class, 'myProducts']);

        Route::get('services', [ServiceController::class, 'index']);
        Route::get('services/user', [ServiceController::class, 'getUserServices']);

        Route::get('jobs', [JobController::class, 'index']);
        Route::get('jobs/user', [JobController::class, 'getUserJobs']);

        Route::get('stores', [SellerController::class, 'stores']);

        Route::get('favorites', [ProductController::class, 'getFavourites']);
        Route::post('favorites', [ProductController::class, 'MarkAsFav']);
        Route::post('deleteFavorite', [ProductController::class, 'deleteFavorite']);

        Route::post('products/{product}/reviews', [ProductController::class, 'addReview']);
        Route::get('products/{product}/reviews', [ProductController::class, 'getReviews']);
        Route::post('products/{product}/violations', [ProductController::class, 'addViolation']);

        Route::get('offers', [OfferController::class, 'index']);

        Route::post('/user/fcm-token', [NotificationController::class, 'storeFcmToken']);
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::patch('/notifications/{id}', [NotificationController::class, 'markOneAsRead']);
        Route::patch('/notifications', [NotificationController::class, 'markAllAsRead']);

        Route::post('chat/check-or-create', [ChatController::class, 'checkOrCreateChat']);
        Route::get('/chats/{chatId}/messages', [ChatController::class, 'getMessages']);
        Route::post('/chats', [ChatController::class, 'store']);
        Route::post('/send-message', [ChatController::class, 'send']);
        Route::get('/chats', [ChatController::class, 'getUserChats']);

        Route::middleware(['role:admin|seller|Admin'])->group(function () {
            Route::post('categories', [CategoryController::class, 'store']);
            Route::put('categories/{category}', [CategoryController::class, 'update']);
            Route::delete('categories/{category}', [CategoryController::class, 'destroy']);

            Route::post('sub-categories', [SubCategoryController::class, 'store']);
            Route::put('sub-categories/{sub_category}', [SubCategoryController::class, 'update']);
            Route::delete('sub-categories/{sub_category}', [SubCategoryController::class, 'destroy']);

            Route::post('offers', [OfferController::class, 'store']);

            //     Route::post('products', [ProductController::class, 'store']);

            Route::post('ads', [AdsController::class, 'store']);
        });

        // Admin routes
        Route::middleware(['role:admin|Admin'])->prefix('admin')->group(function () {
            Route::post('seller/{seller}/status', [SellerController::class, 'updateSellerStatus']);
            Route::patch('products/{id}/feature', [ProductController::class, 'markAsFeatured']);
            Route::patch('store/{id}/featured', [SellerController::class, 'markSellerAsFeatured']);
            Route::get('products/{product}/violations', [ProductController::class, 'getViolations']);

            Route::patch('users/{id}/inactive', [RegisterController::class, 'markAsInactive']);
            Route::delete('users/{id}', [RegisterController::class, 'destroy']);
        });

        // Seller routes
        Route::middleware(['role:seller'])->prefix('seller')->group(function () {});

        // Customer routes
        Route::middleware(['role:customer'])->prefix('customer')->group(function () {
            Route::post('upgrade-to-seller', [RegisterController::class, 'upgradeToSeller']);
        });

        /**
         * require the file that include
         * 1- search endpoints
         * 2- activations endpoints
         **/
        require 'h1.php';
    });
});

// https://github.com/ahmadelhajj1996/souqalbald

Route::get('/test-notification', [NotificationController::class, 'test']);

Route::post('/broadcast-read', function (\Illuminate\Http\Request $request) {
    event(new \App\Events\MessagesRead($request->message_id, $request->reader_id));

    return response()->json(['status' => 'read']);
});
