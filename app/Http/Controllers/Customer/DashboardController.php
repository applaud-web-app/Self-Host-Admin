<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\License;

class DashboardController extends Controller
{
    public function dashboard(){
        $userId = Auth::id();
        $licenceKey = License::select('key','product_id')->with('product:id,name,uuid')->where('user_id', $userId)->where('status', 'active')->latest()->first();
        $licenseArray = $licenceKey->toArray();
        return view('frontend.customer.dashboard', compact('licenseArray'));
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('login');
    }

}
