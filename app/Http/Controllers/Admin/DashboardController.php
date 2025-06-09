<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard()
    {
       // 1) Count total users
        $totalUsers = User::role('customer')->count();

        // 2) Count total products (add-ons)
        $totalProducts = Product::count();

        // 3) Fetch all three payment sums (total / today / month-to-date) in one query
        $today        = Carbon::today()->toDateString();
        $startOfMonth = Carbon::now()->startOfMonth()->toDateTimeString();

        $paymentSums = Payment::selectRaw("
                SUM(amount) AS total_payments,
                SUM(CASE WHEN DATE(created_at) = ? THEN amount ELSE 0 END) AS today_payments,
                SUM(CASE WHEN created_at >= ? THEN amount ELSE 0 END) AS month_payments
            ", [
                $today,
                $startOfMonth
            ])->first();

        // Extract into variables (fallback to 0 if null)
        $totalPayments = $paymentSums->total_payments   ?: 0;
        $todayPayment  = $paymentSums->today_payments   ?: 0;
        $monthPayment  = $paymentSums->month_payments   ?: 0;

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalProducts',
            'totalPayments',
            'todayPayment',
            'monthPayment'
        ));
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('login');
    }

}
