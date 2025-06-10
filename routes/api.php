<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LicenseController;

Route::post('license/verify', [LicenseController::class, 'verify']);
Route::post('license/debug-domain', [App\Http\Controllers\Api\LicenseController::class, 'debugDomain']);