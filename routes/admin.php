<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| All routes in this file will be prefixed with "admin" (see 
| RouteServiceProvider configuration below). You can add as many
| admin-related endpoints as you like here.
|
*/

Route::prefix('admin')->as('admin.')->middleware('admin')->group( function () {

    Route::controller(DashboardController::class)->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('logout', 'logout')->name('logout');
    });

    Route::controller(ProfileController::class)->group(function () {
        Route::get('profile', 'showProfile')->name('profile.show');
        Route::post('profile', 'updateProfile')->name('profile.update');
        Route::post('password', 'updatePassword')->name('password.update');
    });
    
    Route::controller(PaymentController::class)->group(function () {
        Route::get('payment', 'showPayment')->name('payment.show');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('users', 'showUsers')->name('users.show');
        Route::get('edit-user', 'editUser')->name('users.edit');
        Route::post('update-user', 'updateUser')->name('users.update');
        Route::get('users-ajax', 'ajaxUsers')->name('users.ajax');
        Route::get('user-features', 'showFeaturesUser')->name('user.features');
    });

    Route::controller(ProductController::class)->group(function () {
        Route::get('addons', 'showAddons')->name('addons.show');
        Route::post('addons', 'storeAddons')->name('addons.store');
        Route::post('addons/upload-zip','uploadZip')->name('addons.uploadZip');
        Route::get('download-addon/{uuid}', 'downloadAddons')->name('addons.download');
        Route::post('edit-addon/{uuid}', 'editAddons')->name('addons.edit');
        Route::post('addons/delete-zip', 'deleteZip')->name('addons.deleteZip');
    });

});