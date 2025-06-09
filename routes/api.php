<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LicenseController;

Route::post('license/verify', [LicenseController::class, 'verify'])->middleware('throttle:10,1'); // optional rate-limit