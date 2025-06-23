<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Razorpay\Api\Api;       
use Illuminate\Support\Facades\Crypt;
use App\Models\Payment;
use App\Models\License;

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

    public function purchase(Request $request) {
        try {
            
            $response = decryptUrl($request->eq);
            $requestedUuid = $response['uuid'];


            if ($requestedUuid) {
                $product = Product::select('price', 'uuid', 'name')
                                ->where('uuid', $requestedUuid)
                                ->where('status', 1)
                                ->where('type', 'addon')
                                ->first();
            }

            if (empty($product)) {
                return response()->json(['error' => 'Invalid Request! Please contact site admin'], 400);
            }

            $priceInRupees = $product->price;
            $priceInPaise  = intval($priceInRupees * 100);

            // Instantiate Razorpay API client
            $razorpayKey    = Config::get('services.razorpay.key');
            $razorpaySecret = Config::get('services.razorpay.secret');
            $api = new Api($razorpayKey, $razorpaySecret);

            try {
                $orderData = [
                    'amount'          => $priceInPaise,
                    'currency'        => 'INR',
                    'receipt'         => 'rcpt_' . time(),
                    'payment_capture' => 1, // Auto-capture
                ];
                $razorpayOrder = $api->order->create($orderData);
            } catch (\Exception $e) {
                Log::error('Razorpay Order creation failed: ' . $e->getMessage());
                return response()->json(['error' => 'Unable to initialize payment. Please try again later.'], 500);
            }

            return response()->json([
                'orderId'     => $razorpayOrder['id'],
                'amount'      => $priceInPaise,
                'razorpayKey' => $razorpayKey,
                'product'     => $product,
            ]);
        } catch (\Throwable $th) {
            return response()->json([]);
        }
    }

    public function paymentCallback(Request $request) {
        // Validate incoming request
        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id'   => 'required|string',
            'razorpay_signature'  => 'required|string',
            'product_uuid'        => 'required|string|exists:products,uuid',
        ]);

        // Grab payment and order details from the request
        $paymentId   = $request->input('razorpay_payment_id');
        $orderId     = $request->input('razorpay_order_id');
        $signature   = $request->input('razorpay_signature');
        $productUuid = $request->input('product_uuid');

        // Re-instantiate Razorpay API for signature verification
        $razorpayKey    = Config::get('services.razorpay.key');
        $razorpaySecret = Config::get('services.razorpay.secret');
        $api = new Api($razorpayKey, $razorpaySecret);

        // Verify payment signature
        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature'  => $signature,
            ]);
        } catch (\Exception $e) {
            Log::warning('Razorpay signature verification failed: ' . $e->getMessage());
            return response()->json(['error' => 'Payment verification failed. Please contact support.'], 400);
        }

        // Fetch Razorpay payment details
        try {
            $razorpayPayment = $api->payment->fetch($paymentId);
        } catch (\Exception $e) {
            Log::error('Fetching Razorpay payment details failed: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to retrieve payment details. Contact support.'], 500);
        }

        // Ensure the payment is "captured"
        if ($razorpayPayment->status !== 'captured') {
            return response()->json(['error' => 'Payment was not captured. Status: ' . $razorpayPayment->status], 400);
        }

        // Fetch the product
        $product = Product::where('uuid', $productUuid)->where('status', 1)->first();
        if (!$product) {
            Log::error("Product with UUID {$productUuid} not found or inactive.");
            return response()->json(['error' => 'Selected product is unavailable. Please contact support.'], 400);
        }

        // Store payment and license details
        try {
            \DB::beginTransaction();

            // Store the payment record
            $payment = Payment::create([
                'user_id' => Auth::id(),
                'product_id'          => $product->id,
                'razorpay_order_id'   => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature'  => $signature,
                'amount'              => $razorpayPayment->amount / 100, // Convert to INR
                'status'              => 'paid',
            ]);

            $raw    = Str::upper(Str::random(32));
            $salt   = Str::random(16);
            $pepper = config('app.license_pepper');

            // Argon2ID hash of pepper|salt|rawKey
            $hashInput = "{$pepper}|{$salt}|{$raw}";
            $keyHash   = password_hash($hashInput, PASSWORD_ARGON2ID);

            License::create([
                'user_id' => Auth::id(),
                'product_id'  => $product->id,
                'payment_id'  => $payment->id,
                'raw_key'    => $raw,
                'key_salt'   => $salt,
                'key_hash'   => $keyHash,
                'status'     => 'active',
                'issued_at'  => now()
            ]);

            \DB::commit();

            return response()->json(['success' => true, 'message' => 'Payment successful!']);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error saving payment/license: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to save payment/license details.'], 500);
        }
    }



}
