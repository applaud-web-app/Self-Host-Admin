<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class ProductController extends Controller
{
    public function showAddons(Request $request)
    {
        // 1) Build a base query: only “addon” products that are active.
        $query = Product::select('id','uuid','slug','name','icon','price','description','created_at')
                        ->where('type', 'addon')
                        ->where('status', 1);

        // 2) If “search” is present, filter by name LIKE %search%
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        // 3) Filter by purchase_status (if provided)
        //    We assume there is a relationship `payment` on Product that belongs to the current user
        if ($request->filled('purchase_status')) {
            $status = $request->input('purchase_status');

            if ($status === 'purchased') {
                // Only keep products where the current user has a payment record
                $query->whereHas('payment', function($q) {
                    $q->where('user_id', Auth::id());
                });
            }
            elseif ($status === 'not_purchased') {
                // Only keep products where the current user has NO payment record
                $query->whereDoesntHave('payment', function($q) {
                    $q->where('user_id', Auth::id());
                });
            }
            // If “purchase_status” is empty or not one of those two, we do nothing (i.e. show all)
        }

        // 4) Sort: “oldest” or default to “latest”
        if ($request->filled('sort') && $request->input('sort') === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // 5) Eager‐load the current user’s payment & license, then paginate
        $products = $query
            ->with([
                'payment' => function($q) {
                    // Only eager‐load this user’s payment row (if any)
                    $q->where('user_id', Auth::id());
                },
                'license' => function($q) {
                    // Likewise, only load this user’s license (if you also filter by license elsewhere)
                    $q->where('user_id', Auth::id());
                },
            ])
            ->paginate(8)
            ->appends($request->only('search','sort','purchase_status'));

        // 6) If AJAX (filter, sort, or page link), return only the partial (cards + pagination)
        if ($request->ajax()) {
            return view('frontend.customer.addons.partials.cards', [
                'products' => $products,
            ]);
        }

        // 7) Normal page load: return full “show” view
        return view('frontend.customer.addons.show', [
            'products' => $products,
        ]);
    }
}
