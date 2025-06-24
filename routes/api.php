<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LicenseController;
use App\Http\Controllers\Api\CrossPlatformController;

Route::post('verify', [LicenseController::class, 'verifyStatus'])->name('api.verify');


Route::post('license/verify', [LicenseController::class, 'verify']);
Route::post('license/addon-verify', [LicenseController::class, 'addonVerify']);
// Route::post('license/debug-domain', [App\Http\Controllers\Api\LicenseController::class, 'debugDomain']);

// ADDONS API
Route::post('license/addon-list', [CrossPlatformController::class, 'addonList']);
Route::post('license/subscriber', [CrossPlatformController::class, 'subscriber']);

// ACTIVE PACKAGE
// Route::post('license/debug-domain', [App\Http\Controllers\Api\LicenseController::class, 'debugDomain']);