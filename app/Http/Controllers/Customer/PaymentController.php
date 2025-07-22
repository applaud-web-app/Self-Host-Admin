<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;
use App\Models\Coupon;
use App\Models\UserDetail;
use DataTables;
use App\Services\PDFService;
use Illuminate\Support\Facades\Log;
use Throwable;
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
                ->where('is_grouped', 0)
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
                ->addColumn('invoice', function ($row) {
                    $url = route('customer.payment.invoice');
                    $param = ['id'=>$row->id];
                    $invoiceUrl = encryptUrl($url,$param);
                    // download="invoice.pdf"
                    return '<a href="'.$invoiceUrl.'" target="_blank" class="text-capitalize badge badge-warning text-white badge-sm generateInvoice">Download</a>';
                })
                ->rawColumns(['status','invoice'])
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

    

    // public function generateInvoice(Request $request)
    // {
    //     // Validate request
    //     $request->validate([
    //         'eq' => 'required'
    //     ]);

    //     // Decrypt the request parameter
    //     $response = decryptUrl($request->eq);

    //     // Fetch payment details with related user and product data
    //     $payment = Payment::with(['user', 'product'])->where('id', $response['id'])->firstOrFail();

    //     if (!$payment) {
    //         abort(404, "Payment record not found.");
    //     }

    //     // Fetch the coupon if provided and stored in the payment record
    //     $coupon = null;
    //     $discount = $payment->discount_amount ?? 0; // Use the discount stored in the payment record
        
    //     // Fetch the coupon if it was stored in the payment and it's valid
    //     if ($payment->coupon_code) {
    //         $coupon = Coupon::where('coupon_code', $payment->coupon_code)
    //                         ->where('status', 'active')
    //                         ->whereDate('expiry_date', '>=', now())
    //                         ->first();
            
    //         // If the coupon exists, recalculate the discount (in case of percentage-based discount)
    //         if ($coupon) {
    //             if ($coupon->discount_type == 'percentage') {
    //                 $discount = ($payment->amount * $coupon->discount_amount) / 100;
    //             } else {
    //                 $discount = $coupon->discount_amount;
    //             }
    //         }
    //     }

    //     // Fetch user's billing information from UserDetail
    //     $userDetail = UserDetail::where('user_id', $payment->user_id)->first();

    //     // Final calculation (adjust for discount)
    //     $totalAmount = $payment->product->price;

    //     // Prepare invoice data
    //     $data = [
    //         'invoiceNumber' => $payment->razorpay_order_id, // Using Razorpay order ID as invoice number
    //         'invoiceDate' => date('d/m/y', strtotime($payment->created_at)),
    //         'billingFrom' => [
    //             'email' => $payment->user->email,
    //             'phone' => $payment->user->contact_no,
    //             'name' => isset($userDetail) ? ($userDetail->billing_name ?? $payment->user->name) : $payment->user->name,
    //             'address' => isset($userDetail) 
    //                 ? trim(($userDetail->address ?? '') . ' ' . 
    //                         ($userDetail->city ?? '') . ' ' . 
    //                         ($userDetail->state ?? '') . ' ' . 
    //                         ($userDetail->pin_code ?? '')) 
    //                 : '',
    //             'state' => isset($userDetail) ? ($userDetail->state ?? '') : '',
    //             'pan_card' => isset($userDetail) ? ($userDetail->pan_card ?? '') : '',
    //             'gst_number' => isset($userDetail) ? ($userDetail->gst_number ?? '') : '',
    //         ],
    //         'billingTo' => [
    //             'name' => 'Applaud Web Media Pvt. Ltd.',
    //             'address' => 'Near Indian Overseas Bank Racecourse, Dehradun, Uttarakhand, India 248001',
    //         ],
    //         'items' => [
    //             'description' => $payment->product->name ?? 'Unknown Product',
    //             'quantity' => 1,
    //             'amount' => $payment->amount,
    //             'discount_amount' => $payment->discount_amount,
    //             'duration' => "Life Time",
    //         ],
    //         'coupon' => $coupon ? $coupon->coupon_code : null,
    //         'discount' => $discount,
    //         'totalAmount' => $totalAmount,
    //         'paid_amount' => $payment->amount,
    //         'note' => 'Thank you for your payment. If you need to update your payment information, please contact us at info@aplu.com.',
    //         'footerMessage' => 'This is an automatically generated payment receipt. If you have any queries, please contact support at info@aplu.com or call us at +91-9874563210.',
    //         'companyLogo' => 'https://push.aplu.io/images/logo-main.png',
    //     ];

    //     // Load the invoice HTML view and pass data
    //     $html = view('admin.payment.generate-pdf', $data)->render();

    //     // Create an instance of Mpdf
    //     $mpdf = new Mpdf();

    //     // Write HTML to PDF
    //     $mpdf->WriteHTML($html);

    //     // Output the PDF to the browser
    //     return $mpdf->Output('invoice.pdf', 'I');
    // }

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

        // Decode metadata
        $metadata = json_decode($payment->metadata, true);
        
        // Core product price from metadata (should be used instead of product->price)
        $corePrice = $metadata['product_price'] ?? $payment->product->price;
        
        // Addons from metadata
        $addons = collect($metadata['addons'] ?? []);
        $addonsTotal = $addons->sum(function($price) {
            return is_numeric($price) ? $price : 0;
        });
        
        // Support calculations
        $supportPrice = 0;
        if (isset($metadata['support_year']) && $metadata['support_year'] > 1) {
            // Only charge for additional years beyond the first (which is free)
            $supportPrice = ($metadata['support_price'] ?? 0);
        }

        // Subtotal (product + addons + additional support years)
        $subtotal = $corePrice + $addonsTotal + $supportPrice;

        // Discount calculation
        $discount = $metadata['coupon_discount'] ?? 0;
        
        // Apply coupon if exists (this will override the metadata discount if coupon is valid)
        if ($payment->coupon_code) {
            $coupon = Coupon::where('coupon_code', $payment->coupon_code)
                            ->where('status', 'active')
                            ->whereDate('expiry_date', '>=', now())
                            ->first();

            if ($coupon) {
                if ($coupon->discount_type == 'percentage') {
                    $discount = ($subtotal * $coupon->discount_amount) / 100;
                } else {
                    $discount = $coupon->discount_amount;
                }
            }
        }

        // Subtotal after discount
        $subtotalAfterDiscount = $subtotal - $discount;
        if ($subtotalAfterDiscount < 0) $subtotalAfterDiscount = 0; // prevent negative values

        // GST Calculation (18%)
        $gstAmount = $subtotalAfterDiscount * 0.18;

        // Final total amount (subtotal after discount + GST)
        $totalAmount = $subtotalAfterDiscount + $gstAmount;

        // Fetch user's billing information from UserDetail
        $userDetail = UserDetail::where('user_id', $payment->user_id)->first();

        // Prepare invoice data
        $data = [
            'invoiceNumber' => $payment->razorpay_order_id,
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
            'coreProduct' => [
                'description' => $payment->product->name,
                'price' => $corePrice,
            ],
            'addons' => $addons,
            'supportPrice' => $supportPrice,
            'support_year' => $metadata['support_year'],
            'discount' => $discount,
            'gstAmount' => $gstAmount,
            'subtotal' => $subtotal,
            'subtotalAfterDiscount' => $subtotalAfterDiscount,
            'totalAmount' => $totalAmount,
            'paidAmount' => $payment->amount,
            'coupon' => $payment->coupon_code,
            'note' => 'Thank you for your payment. If you need to update your payment information, please contact us at info@aplu.com.',
            'footerMessage' => 'This is an automatically generated payment receipt. If you have any queries, please contact support at info@aplu.com or call us at +91-9874563210.',
            'companyLogo' => 'https://push.aplu.io/images/logo-main.png',
            'payment' => $payment
        ];

        return view('admin.payment.generate-pdf', $data);

        // Load the invoice HTML view and pass data
        // $html = view('admin.payment.generate-pdf', $data)->render();

        // Create an instance of Mpdf
        $mpdf = new Mpdf();

        // Write HTML to PDF
        $mpdf->WriteHTML($html);

        // Output the PDF to the browser
        return $mpdf->Output('invoice.pdf', 'I');
    }



}
