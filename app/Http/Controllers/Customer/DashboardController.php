<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\License;

class DashboardController extends Controller
{
    public function dashboard(){
        $userId = Auth::id();
        $lic = License::with('product')
        ->where('user_id', Auth::id())
        ->where('status','active')
        ->latest()
        ->firstOrFail();

        return view('frontend.customer.dashboard', [
           'licenseString' => $lic->raw_key,
            'product'       => $lic->product,
        ]);
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('login');
    }

}
