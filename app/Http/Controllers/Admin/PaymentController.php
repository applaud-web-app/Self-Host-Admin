<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\License;
use DataTables;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Throwable;
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
        if ($request->ajax()) {
            $query = Payment::with(['product:id,name,type,uuid','user:id,email','license'])
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
                ->addColumn('action', function($row) {
                    // If there's already a license key, show “Copy Key”
                    if ($row->license && $row->license->raw_key) {
                        $key = e($row->license->raw_key);
                        return "<button
                                    class='btn btn-sm btn-primary btn-copy-key'
                                    data-key='{$key}'>
                                    Copy Key
                                </button>";
                    }

                    // Otherwise show “Generate Key”
                    $uuid = e($row->product->uuid);
                    $id   = $row->id;
                    return "<button
                                class='btn btn-sm btn-info btn-generate-key'
                                data-uuid='{$uuid}'
                                data-id='{$row->license->id}'>
                                Generate Key
                            </button>";
                })
                ->rawColumns(['status','action'])
                ->toJson();
        }
        return view('admin.payment.show');
    }

    public function generateKey(Request $request)
    {
        $data = $request->validate([
            'licence_id'   => 'required|integer|exists:licenses,id',
            'product_uuid' => 'required|string',
            'server_ip'    => 'required|ip',
            'domain_name'  => 'required|string',
        ]);

        try {
            // 1. Find the license or throw a 404
            $license = License::findOrFail($data['licence_id']);

            // 2. Build payload & HMAC
            $payload = implode('|', [
                $data['product_uuid'],
                $data['server_ip'],
                $data['domain_name'],
            ]);
            $secret = Config::get('licenses.secret');
            $hmac   = hash_hmac('sha256', $payload, $secret);

            // 3. Truncate & format
            $short = strtoupper(substr($hmac, 0, 20));
            $key   = trim(chunk_split($short, 5, '-'), '-');

            // 4. Update the existing license instance
            $license->update([
                'activated_ip'     => $data['server_ip'],
                'activated_domain' => $data['domain_name'],
                'raw_key'          => $key,
                'is_activated'     => false,
            ]);

            return redirect()->back()->with('success','License generated successfully.');

        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error','License not found.');
        } catch (Throwable $e) {
            return redirect()->back()->with('error','An internal error occurred. :' . $e->getMessage());
        }
    }

    
  public function generatePdf()
{
    // Sample data to pass to the view
    $data = [
        'invoiceNumber' => 'INV123456',
        'invoiceDate' => '2025-06-26',
        'placeOfSupply' => 'Uttarakhand',
        'billTo' => [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+91-1234567890',
            'address' => '123 Main St, Dehradun, Uttarakhand',
            'pan' => 'ABCD1234E',
            'gst' => '05ABCDE1234F1Z1'
        ],
        'items' => [
            [
                'description' => 'Self Hosting Service',
                'hsn' => '998315',
                'cgst' => '₹90.00',
                'sgst' => '₹90.00',
                'amount' => '₹1000.00'
            ]
        ],
        'summary' => [
            'totalWords' => 'Indian Rupee One Thousand Only',
            'total' => '₹1180.00',
            'gst' => '₹180.00',
            'serviceAmount' => '₹1000.00'
        ]
    ];


    return $this->pdfService->generatePdf('frontend.customer.payment.invoice', $data);
}



}
