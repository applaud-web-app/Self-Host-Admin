<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use DataTables;
use App\Services\PDFService;

class PaymentController extends Controller
{
    protected $pdfService;

    public function __construct(PDFService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function showPayment(Request $request)
    {
        $userId = Auth::id();

        if ($request->ajax()) {
            $query = Payment::select([
                    'id',
                    'razorpay_order_id',
                    'product_id',
                    'amount',
                    'status',
                    'created_at',
                ])
                ->with([
                    'product' => function ($q) {
                        $q->select('id', 'name', 'type');
                    }
                ])
                ->where('user_id', $userId)
                // Filter: Order ID (partial)
                ->when($request->filled('order_id'), function ($q) use ($request) {
                    $term = $request->input('order_id');
                    return $q->where('razorpay_order_id', 'LIKE', "%{$term}%");
                })
                // Filter: Product Name (partial, via whereHas)
                ->when($request->filled('product_name'), function ($q) use ($request) {
                    $term = $request->input('product_name');
                    return $q->whereHas('product', function ($q2) use ($term) {
                        $q2->where('name', 'LIKE', "%{$term}%");
                    });
                })
                // Filter: Product Type (exact match)
                ->when($request->filled('product_type'), function ($q) use ($request) {
                    $type = $request->input('product_type');
                    return $q->whereHas('product', function ($q2) use ($type) {
                        $q2->where('type', $type);
                    });
                })
                ->orderBy('created_at', 'desc');

            return DataTables::eloquent($query)
                ->addIndexColumn()  // produces DT_RowIndex
                ->addColumn('product_name', function ($row) {
                    return $row->product
                        ? e($row->product->name)
                        : '—';
                })
                ->addColumn('product_type', function ($row) {
                    return $row->product
                        ? strtoupper(e($row->product->type))
                        : '—';
                })
                ->addColumn('paid_at', function ($row) {
                    return $row->created_at->format('d-m-Y');
                })
                ->addColumn('amount', function ($row) {
                    return '₹'.number_format($row->amount, 2);
                })
                ->addColumn('status', function ($row) {
                    $badgeClass = $row->status === 'paid'
                        ? 'success'
                        : 'danger';
                    return '<span class="badge badge-'. $badgeClass .'">'. ucfirst(e($row->status)) .'</span>';
                })
                ->rawColumns(['status'])
                ->toJson();
        }

        // If not AJAX, just return the view; JS will fetch via AJAX.
        return view('frontend.customer.payment.show');
    }

    public function generatePdf()
    {
        // Sample data to pass to the view
        $data = [
            'title' => 'Laravel mPDF Example',
            'content' => 'This PDF was generated using mPDF in Laravel!'
        ];

        // Call the PDF service to generate the PDF
        return $this->pdfService->generatePdf('frontend.customer.payment.invoice', $data);
    }

}
