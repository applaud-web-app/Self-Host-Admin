<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use DataTables;

class PaymentController extends Controller
{
    public function showPayment(Request $request)
    {
        $serverIp = $request->server('SERVER_ADDR');
        dd($serverIp);
        if ($request->ajax()) {
            $query = Payment::with(['product:id,name,type,uuid','user:id,email'])
                ->when($request->filled('search_term'), function ($q) use ($request) {
                    $term = $request->input('search_term');
                    $q->where('razorpay_order_id', 'like', "%{$term}%")
                      ->orWhereHas('product', function ($q2) use ($term) {
                          $q2->where('name', 'like', "%{$term}%");
                      });
                })
                ->when($request->filled('user_id'), function ($q) use ($request) {
                    $q->where('user_id', $request->input('user_id'));
                })
                ->orderBy('created_at', 'desc');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('product_name', fn($row) => $row->product->name ?? '—')
                ->addColumn('product_type', fn($row) => strtoupper($row->product->type ?? '—'))
                ->addColumn('user_email', fn($row) => $row->user->email ?? '—')
                ->addColumn('amount', fn($row) => '₹'.number_format($row->amount, 2))
                ->addColumn('paid_at', fn($row) => $row->created_at->format('d-m-Y'))
                ->addColumn('status', function($row) {
                    $class = $row->status==='paid'?'success':'danger';
                    return "<span class='badge badge-{$class}'>".ucfirst($row->status)."</span>";
                })
                ->addColumn('generate_key', fn($row) => "<button class='btn btn-sm btn-info btn-generate-key' data-uuid='{$row->product->uuid}'>Generate Key</button>")
                ->rawColumns(['status','generate_key'])
                ->toJson();
        }
        return view('admin.payment.show');
    }
}
