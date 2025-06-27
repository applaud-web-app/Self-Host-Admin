<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CouponManagement;

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
        Route::get('generate-script', 'generateScript')->name('generate-script');
        Route::get('logout', 'logout')->name('logout');
    });

    Route::controller(ProfileController::class)->group(function () {
        Route::get('profile', 'showProfile')->name('profile.show');
        Route::post('profile', 'updateProfile')->name('profile.update');
        Route::post('password', 'updatePassword')->name('password.update');
    });
    
    Route::controller(PaymentController::class)->group(function () {
        Route::get('payment', 'showPayment')->name('payment.show');
        Route::post('generate-key', 'generateKey')->name('generate-key.show');
        Route::get('/generate-pdf', 'generatePdf')->name('payment.pdf');
        Route::get('/license-list', 'licenseList')->name('license.show');
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

    Route::controller(CouponManagement::class)->group(function () {
        Route::get('/coupons', 'coupons')->name('coupons.show');
        Route::get('/add-coupon', 'addCoupon')->name('coupons.add');
        Route::post('/store-coupon', 'storeCoupon')->name('coupons.store');
        Route::get('/edit-coupon', 'editCoupon')->name('coupons.edit');
        Route::post('/update-coupon', 'updateCoupon')->name('coupons.update');
        Route::post('/check-coupon', 'checkCoupon')->name('coupons.check');
        Route::get('/remove-coupon', 'removeCoupon')->name('coupons.remove');
    });

});