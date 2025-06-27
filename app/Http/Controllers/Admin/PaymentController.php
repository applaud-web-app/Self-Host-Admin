<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\License;
use App\Models\UserDetail;
use DataTables;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Services\PDFService;
use Carbon\Carbon;
use Mpdf\Mpdf;

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
                ->addColumn('invoice', function ($row) {
                    $url = route('admin.payment.invoice');
                    $param = ['id'=>$row->id];
                    $invoiceUrl = encryptUrl($url,$param);
                    // download="invoice.pdf"
                    return '<a href="'.$invoiceUrl.'" target="_blank" class="text-capitalize badge badge-warning text-white badge-sm generateInvoice">Download</a>';
                })
                // ->addColumn('action', function($row) {
                //     // If there's already a license key, show “Copy Key”
                //     if ($row->license && $row->license->raw_key) {
                //         $key = e($row->license->raw_key);
                //         return "<button
                //                     class='btn btn-sm btn-primary btn-copy-key'
                //                     data-key='{$key}'>
                //                     Copy Key
                //                 </button>";
                //     }

                //     // Otherwise show “Generate Key”
                //     $uuid = e($row->product->uuid);
                //     $id   = $row->id;
                //     return "<button
                //                 class='btn btn-sm btn-info btn-generate-key'
                //                 data-uuid='{$uuid}'
                //                 data-id='{$row->license->id}'>
                //                 Generate Key
                //             </button>";
                // })
                ->rawColumns(['status','action','invoice'])
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

    public function licenseList(Request $request)
    {
        if ($request->ajax()) {
            $query = License::with(['product:id,name,type,uuid', 'user:id,name,email', 'payment:id,status,amount,razorpay_order_id,created_at'])
                ->when($request->filled('search_term'), function ($q) use ($request) {
                    $term = $request->input('search_term');
                    $q->where('raw_key', 'like', "%{$term}%")
                    ->orWhereHas('product', function ($q2) use ($term) {
                        $q2->where('name', 'like', "%{$term}%");
                    });
                })
                ->when($request->filled('user_id'), function ($q) use ($request) {
                    $q->where('user_id', $request->input('user_id'));
                })
                ->when($request->filled('product_type'), function ($q) use ($request) {
                    $q->whereHas('product', function ($q2) use ($request) {
                        $q2->where('type', $request->input('product_type'));
                    });
                })
                ->orderBy('issued_at', 'desc');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('payment_details', function ($row) {
                    $orderId = $row->payment->razorpay_order_id ?? '—';
                    $paymentStatus = ucfirst($row->payment->status) ?? '—';

                    return "<strong>OID:</strong> {$orderId}<br>
                        <small class='mt-1 badge badge-" . ($row->payment->status == 'paid' ? 'success' : 'danger') . "'>{$paymentStatus}</small><br>";
                })
                ->addColumn('user_info', function ($row) {
                    $username = $row->user->name ?? '—';
                    $userEmail = $row->user->email ?? '—';
                    return "<p class='mb-1'>{$username}</p><small>{$userEmail}</small>";
                })
                ->addColumn('product_info', function ($row) {
                    $productName = $row->product->name ?? '—';
                    $productType = strtoupper($row->product->type ?? '—');
                    return "<strong>{$productName}</strong><br><small class='mt-1 badge badge-primary'>{$productType}</small>";
                })
                ->addColumn('activation_info', function ($row) {
                    $activationDomain = $row->activated_domain ?? '—';
                    $activationIp = $row->activated_ip ?? '—';
                    $activationStatus = $row->is_activated ? 'Activated' : 'Not Activated';
                    $activationBadge = $row->is_activated ? 'success' : 'danger';

                    return "<small><strong>Domain:</strong> {$activationDomain} | <strong>SIP:</strong> {$activationIp}</small><br>
                        <span class='mt-1 badge badge-{$activationBadge}'>{$activationStatus}</span>";
                })
                ->addColumn('amount', fn($row) => "₹" . $row->payment->amount ?? '—')
                ->addColumn('paid_at', fn($row) => $row->payment->created_at ? $row->payment->created_at->format('d M Y, h:i A') : '—')
                ->addColumn('action', function ($row) {
                    if ($row->raw_key) {
                        $key = e($row->raw_key);
                        return "<button class='btn btn-sm btn-primary btn-copy-key' data-key='{$key}'>Copy Key</button>";
                    }
                    $uuid = e($row->product->uuid);
                    $id   = $row->id;
                    return "<button class='btn btn-sm btn-info btn-generate-key' data-uuid='{$uuid}' data-id='{$row->id}'>Generate Key</button>";
                })
                ->rawColumns(['payment_details','user_info','product_info','activation_info','action'])
                ->toJson();
        }
        return view('admin.license.show');
    }

    public function generateInvoice(Request $request)
    {
        // Validate request
        $request->validate([
            'eq' => 'required'
        ]);

        // Decrypt the request parameter
        $response = decryptUrl($request->eq);

        // Fetch payment details with related user and product data
        $payment = Payment::with(['user', 'product'])->where('id', $response['id'])->firstOrFail();

        if (!$payment) {
            abort(404, "Payment record not found.");
        }

        // Fetch the coupon if provided and stored in the payment record
        $coupon = null;
        $discount = $payment->discount_amount ?? 0; // Use the discount stored in the payment record
        
        // Fetch the coupon if it was stored in the payment and it's valid
        if ($payment->coupon_code) {
            $coupon = Coupon::where('coupon_code', $payment->coupon_code)
                            ->where('status', 'active')
                            ->whereDate('expiry_date', '>=', now())
                            ->first();
            
            // If the coupon exists, recalculate the discount (in case of percentage-based discount)
            if ($coupon) {
                if ($coupon->discount_type == 'percentage') {
                    $discount = ($payment->amount * $coupon->discount_amount) / 100;
                } else {
                    $discount = $coupon->discount_amount;
                }
            }
        }

        // Fetch user's billing information from UserDetail
        $userDetail = UserDetail::where('user_id', $payment->user_id)->first();

        // Final calculation (adjust for discount)
        $totalAmount = $payment->amount - $discount;

        // Prepare invoice data
        $data = [
            'invoiceNumber' => $payment->razorpay_order_id, // Using Razorpay order ID as invoice number
            'invoiceDate' => date('d/m/y', strtotime($payment->created_at)),
            'billingFrom' => [
                'email' => $payment->user->email,
                'phone' => $payment->user->contact_no,
                'name' => isset($userDetail) ? ($userDetail->billing_name ?? $payment->user->name) : $payment->user->name,
                'address' => isset($userDetail) 
                    ? trim(($userDetail->address ?? '') . ' ' . 
                            ($userDetail->city ?? '') . ' ' . 
                            ($userDetail->state ?? '') . ' ' . 
                            ($userDetail->pin_code ?? '')) 
                    : '',
                'state' => isset($userDetail) ? ($userDetail->state ?? '') : '',
                'pan_card' => isset($userDetail) ? ($userDetail->pan_card ?? '') : '',
                'gst_number' => isset($userDetail) ? ($userDetail->gst_number ?? '') : '',
            ],
            'billingTo' => [
                'name' => 'Applaud Web Media Pvt. Ltd.',
                'address' => 'Near Indian Overseas Bank Racecourse, Dehradun, Uttarakhand, India 248001',
            ],
            'items' => [
                'description' => $payment->product->name ?? 'Unknown Product',
                'quantity' => 1,
                'amount' => $payment->amount,
                'discount_amount' => $payment->discount_amount,
                'duration' => "Life Time",
            ],
            'coupon' => $coupon ? $coupon->coupon_code : null,
            'discount' => $discount,
            'totalAmount' => $totalAmount,
            'note' => 'Thank you for your payment. If you need to update your payment information, please contact us at info@aplu.com.',
            'footerMessage' => 'This is an automatically generated payment receipt. If you have any queries, please contact support at info@aplu.com or call us at +91-9874563210.',
            'companyLogo' => 'https://push.aplu.io/images/logo-main.png',
        ];

        // Load the invoice HTML view and pass data
        $html = view('admin.payment.generate-pdf', $data)->render();

        // Create an instance of Mpdf
        $mpdf = new Mpdf();

        // Write HTML to PDF
        $mpdf->WriteHTML($html);

        // Output the PDF to the browser
        return $mpdf->Output('invoice.pdf', 'I');
    }



}
