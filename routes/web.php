<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\AuthController;
use App\Http\Controllers\Controller;


/*
|--------------------------------------------------------------------------
| Frontend Checkout Routes
|--------------------------------------------------------------------------
*/

Route::controller(AuthController::class)->group(function () {
    // Show the checkout page (GET)
    Route::get('checkout', 'checkout')->name('checkout');
    Route::post('checkout/callback', 'callback')->name('checkout.callback');

    // NEW: AJAX endpoints to check uniqueness before payment
    Route::post('checkout/check-email', 'checkEmail')->name('checkout.checkEmail');
    Route::post('checkout/check-phone', 'checkPhone')->name('checkout.checkPhone');

    // LOGIN ROUTES
    Route::get('login', 'login')->name('login');
    Route::post('login', 'doLogin')->name('login.doLogin');
    Route::get('forget-password', 'forgetPassword')->name('forget-password');
    Route::post('forget-password', 'forgetPassword')->name('forget-password.post');

});



// First page is login
Route::get('/', [Controller::class, 'index'])->name('index');
Route::get('/home', [Controller::class, 'login'])->name('home');

// Optional: Home page (static)
Route::get('/dashboard', [Controller::class, 'dashboard'])->name('dashboard');

// Domain routes
Route::get('/domain', [Controller::class, 'domain'])->name('domain');
Route::get('/integrate-domain', [Controller::class, 'integrateDomain'])->name('integrate-domain');
Route::get('/subscription', [Controller::class, 'subscription'])->name('subscription');
Route::get('/send-notification', [Controller::class, 'sendNotification'])->name('send-notification');
Route::get('/campaign-reports', [Controller::class, 'campaignReports'])->name('campaign-reports');
Route::get('/profile', [Controller::class, 'profile'])->name('profile');

// Settings routes (using the same Controller)
Route::prefix('settings')->group(function () {
    Route::get('/general',       [Controller::class, 'generalSettings'])->name('settings.general');
    Route::get('/email',         [Controller::class, 'emailSettings'])->name('settings.email');
    Route::get('/server-info',   [Controller::class, 'serverInfo'])->name('settings.server-info');
    Route::get('/utilities',     [Controller::class, 'utilities'])->name('settings.utilities');
    Route::post('/utilities/purge-cache',  [Controller::class, 'purgeCache'])->name('settings.utilities.purge-cache');
    Route::post('/utilities/clear-log',    [Controller::class, 'clearLog'])->name('settings.utilities.clear-log');
    Route::post('/utilities/make-cache',   [Controller::class, 'makeCache'])->name('settings.utilities.make-cache');
    Route::get('/upgrade', [Controller::class, 'upgrade'])->name('settings.upgrade');
    Route::get('/backup-subscribers', [Controller::class, 'backupSubscribersPage'])->name('settings.backup-subscribers'); // Fixed naming
});


// Install Wizard Routes (views only)
Route::prefix('install')->group(function () {
    Route::get('/',                 [Controller::class, 'installWelcome'])->name('install.welcome');
    Route::get('/environment',      [Controller::class, 'installEnvironment'])->name('install.environment');
    Route::get('/license',          [Controller::class, 'installLicense'])->name('install.license');
    Route::get('/database',         [Controller::class, 'installDatabase'])->name('install.database');
    Route::get('/cron',             [Controller::class, 'installCron'])->name('install.cron');
    Route::get('/admin-setup',      [Controller::class, 'adminSetup'])->name('install.admin-setup');
    Route::get('/complete',         [Controller::class, 'installComplete'])->name('install.complete');
});

Route::get('/addons', [Controller::class, 'addons'])->name('addons');

