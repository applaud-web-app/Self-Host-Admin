<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LicenseController;
use App\Http\Controllers\Api\CrossPlatformController;

Route::post('license/verify', [LicenseController::class, 'verify']);
// Route::post('license/debug-domain', [App\Http\Controllers\Api\LicenseController::class, 'debugDomain']);

// ADDONS API
Route::post('license/addon-list', [CrossPlatformController::class, 'addonList'])->withoutMiddleware('throttle:api');
Route::post('license/subscriber', [CrossPlatformController::class, 'subscriber'])->withoutMiddleware('throttle:api');

// ACTIVE PACKAGE
// Route::post('license/debug-domain', [App\Http\Controllers\Api\LicenseController::class, 'debugDomain']);