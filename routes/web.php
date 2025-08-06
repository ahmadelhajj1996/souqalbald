<?php

use App\Http\Controllers\Api\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('register/social/{provider}/callback', [RegisterController::class, 'handleSocialProviderCallback']);


// add for test
