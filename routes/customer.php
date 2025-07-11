<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Customer\ProductController;
use App\Services\PDFService;

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
|
| All routes in this file will be prefixed with "customer" (see 
| RouteServiceProvider configuration below). You can add as many
| customer-related endpoints as you like here.
|
*/

Route::prefix('customer')->as('customer.')->middleware('customer')->group( function () {

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
        Route::get('/generate-pdf', 'generatePdf')->name('payment.pdf');
        Route::get('/generate-invoice','generateInvoice')->name('payment.invoice');
    });

    Route::controller(ProductController::class)->group(function () {
        Route::get('addons', 'showAddons')->name('addons.show');
        Route::get('checkout', 'showCheckout')->name('addons.checkout');
        // Route::get('download-addon/{uuid}', 'downloadAddons')->name('addons.download');
        Route::delete('addon/{uuid}', 'destroy')->name('addon.destroy');
        Route::get('/purchase', 'purchase')->name('addons.purchase');
        Route::post('/payment/callback', 'paymentCallback')->name('addons.callback');
    });

});