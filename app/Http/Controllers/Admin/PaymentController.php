<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use DataTables;

class PaymentController extends Controller
{
   public function showPayment(Request $request)
    {
        // If this is an AJAX request, return JSON for DataTables:
        if ($request->ajax()) {
            $query = Payment::select([
                    'id',
                    'razorpay_order_id',
                    'product_id',
                    'user_id',             // include user_id so we can filter & eager‐load
                    'amount',
                    'status',
                    'created_at',
                ])
                ->with([
                    'product' => function ($q) {
                        $q->select('id', 'name', 'type');
                    },
                    'user' => function ($q) {
                        // assuming User has id, name, email
                        $q->select('id', 'email');
                    }
                ])
                // SINGLE “search_term” filter for both Razorpay Order ID and Product Name:
                ->when($request->filled('search_term'), function ($q) use ($request) {
                    $term = $request->input('search_term');
                    return $q->where(function ($sub) use ($term) {
                        $sub->where('razorpay_order_id', 'LIKE', "%{$term}%")
                            ->orWhereHas('product', function ($q2) use ($term) {
                                $q2->where('name', 'LIKE', "%{$term}%");
                            });
                    });
                })
                // FILTER: by user_id (exact match)
                ->when($request->filled('user_id'), function ($q) use ($request) {
                    return $q->where('user_id', $request->input('user_id'));
                })
                ->orderBy('created_at', 'desc');

            return DataTables::eloquent($query)
                ->addIndexColumn()  // produces DT_RowIndex
                // Product Name column
                ->addColumn('product_name', function ($row) {
                    return $row->product
                        ? e($row->product->name)
                        : '—';
                })
                // Product Type column
                ->addColumn('product_type', function ($row) {
                    return $row->product
                        ? strtoupper(e($row->product->type))
                        : '—';
                })
                // User Email column
                ->addColumn('user_email', function ($row) {
                    return $row->user
                        ? e($row->user->email)
                        : '—';
                })
                // Paid At formatted
                ->addColumn('paid_at', function ($row) {
                    return $row->created_at->format('d-m-Y');
                })
                // Amount formatted
                ->addColumn('amount', function ($row) {
                    return '₹'.number_format($row->amount, 2);
                })
                // Status badge
                ->addColumn('status', function ($row) {
                    $badgeClass = $row->status === 'paid'
                        ? 'success'
                        : 'danger';
                    return '<span class="badge badge-'. $badgeClass .'">'. ucfirst(e($row->status)) .'</span>';
                })
                ->rawColumns(['status'])
                ->toJson();
        }

        // If not AJAX, show the Blade view. JS will fetch via AJAX.
        return view('admin.payment.show');
    }
}
