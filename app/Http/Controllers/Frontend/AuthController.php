<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Payment;
use App\Models\UserDetail; 
use App\Models\Product;
use App\Models\License;
use App\Models\Coupon;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Razorpay\Api\Api;       
use Illuminate\Support\Facades\Crypt;

class AuthController extends Controller
{
    private const PRODUCT_UUID = '3f1b0c9a-e8f7-4aee-a2d4-b67f5e3c9d1a';

    public function login(){

        if (Auth::check()) {
            $user = Auth::user();

            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->hasRole('customer')) {
                return redirect()->route('customer.dashboard');
            }

            // If user has no recognized role, log out and send back to login
            Auth::logout();
        }

        return view('frontend.auth.login');
    }

    public function doLogin(Request $request)
    {
        // 1. Strong server‐side validation
        $request->validate([
            'email'       => 'required|string|email|max:255',
            'password'    => 'required|string|min:8',
            'remember_me' => 'nullable',
        ]);

        // 2. Attempt to authenticate with "remember me"
        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember_me') ?? 0; // true if checkbox was checked

        if (Auth::attempt($credentials, $remember)) {
            // Authentication passed. Now check role:
            $user = Auth::user();

            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard')->with('success', 'Welcome back, '. $user->name . '!');
            }

            if ($user->hasRole('customer')) {
                return redirect()->route('customer.dashboard')->with('success', 'Welcome back, '. $user->name . '!');
            }

            // If neither 'admin' nor 'customer', log out immediately and send back
            Auth::logout();
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Unauthorized access for this account.']);
        }

        // 3. If authentication failed, redirect back with an error
        return back()->withErrors([
            'email' => 'Invalid credentials. Please try again.'
        ])->withInput($request->only('email', 'remember_me'));
    }

    public function forgetPassword()
    {
        return view('frontend.auth.forget-password');
    }

    public function checkout(Request $request)
    {
        $requestedUuid = $request->input('product_uuid', self::PRODUCT_UUID);

        if ($requestedUuid) {
            $product = Product::select('price','uuid','name')->where('uuid', $requestedUuid)->where('status', 1)->where('type','core')->first();
        }

        if(empty($product)){
            return back()->withErrors('Invalid Request! Please contact site admin');
        }

        $priceInRupees = $product->price;
        $priceInPaise  = intval($priceInRupees * 100);

        // 1) Instantiate Razorpay API client
        $razorpayKey    = Config::get('services.razorpay.key');
        $razorpaySecret = Config::get('services.razorpay.secret');
        $api = new Api($razorpayKey, $razorpaySecret);

        // 2) Create a new Razorpay Order
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
            return back()->withErrors('Unable to initialize payment. Please try again later.');
        }

        // 3) Pass data to view:
        $data = [
            'orderId'     => $razorpayOrder['id'],
            'amount'      => $priceInPaise,
            'razorpayKey' => $razorpayKey,
            'product'     => $product,
        ];

        return view('frontend.auth.checkout', $data);
    }

    // public function callback(Request $request)
    // {
    //     $request->merge([
    //         'phone' => preg_replace('/\D+/', '', $request->input('phone'))
    //     ]);

    //     // 1) Validate incoming data:
    //     //    We’ve extended validation to include all of the billing fields.
    //     $request->validate([
    //         'name'                   => 'required|string|max:255',
    //         'email'                  => 'required|string|email|max:255|unique:users,email',
    //         'country_code'           => 'required|string|max:5',
    //         'phone'                  => [
    //             'required',
    //             'string',
    //             'max:20',
    //             // composite-unique: phone must be unique where country_code = input
    //             Rule::unique('users')->where(function ($query) use ($request) {
    //                 return $query->where('country_code', $request->input('country_code'));
    //             }),
    //         ],
    //         'password'               => 'required|string|min:8|confirmed', 
    //         'password_confirmation'  => 'required|string|min:8',
    //         'product_uuid'           => 'required|string|exists:products,uuid',

    //         // Razorpay fields:
    //         'razorpay_payment_id'    => 'required|string',
    //         'razorpay_order_id'      => 'required|string',
    //         'razorpay_signature'     => 'required|string',

    //         // New Billing/Detail fields:
    //         'billing_name'           => 'required|string|max:255',
    //         'state'                  => 'required|string|max:100',
    //         'city'                   => 'required|string|max:100',
    //         'pin_code'               => 'required|string|min:4|max:10',
    //         'address'                => 'required|string|max:500',
    //         'pan_card'               => 'nullable|string|max:20',
    //         'gst_number'             => 'nullable|string|max:20',
    //     ]);

    //     // 2) Grab all validated input:
    //     $name         = $request->input('name');
    //     $email        = $request->input('email');
    //     $countryCode  = $request->input('country_code');
    //     $phone        = $request->input('phone');

    //     // Billing/Detail inputs:
    //     $billingName  = $request->input('billing_name');
    //     $state        = $request->input('state');
    //     $city         = $request->input('city');
    //     $pinCode      = $request->input('pin_code');
    //     $address      = $request->input('address');
    //     $panCard      = $request->input('pan_card');      // may be null
    //     $gstNumber    = $request->input('gst_number');    // may be null

    //     // Razorpay fields:
    //     $paymentId    = $request->input('razorpay_payment_id');
    //     $orderId      = $request->input('razorpay_order_id');
    //     $signature    = $request->input('razorpay_signature');

    //     $productUuid  = $request->input('product_uuid');

    //     // 3) Re‐instantiate Razorpay API for signature verification
    //     $razorpayKey    = Config::get('services.razorpay.key');
    //     $razorpaySecret = Config::get('services.razorpay.secret');
    //     $api = new Api($razorpayKey, $razorpaySecret);

    //     // 4) Verify payment signature
    //     $attributes = [
    //         'razorpay_order_id'   => $orderId,
    //         'razorpay_payment_id' => $paymentId,
    //         'razorpay_signature'  => $signature,
    //     ];

    //     try {
    //         $api->utility->verifyPaymentSignature($attributes);
    //     } catch (\Exception $e) {
    //         Log::warning('Razorpay signature verification failed: ' . $e->getMessage());
    //         return view('frontend.auth.failure', [
    //             'error' => 'Payment verification failed. Please contact support.'
    //         ]);
    //     }

    //     // 5) Fetch the payment details from Razorpay
    //     try {
    //         $razorpayPayment = $api->payment->fetch($paymentId);
    //     } catch (\Exception $e) {
    //         Log::error('Fetching Razorpay payment details failed: ' . $e->getMessage());
    //         return view('frontend.auth.failure', [
    //             'error' => 'Unable to retrieve payment details. Contact support.'
    //         ]);
    //     }

    //     // 6) Confirm that Razorpay payment is “captured”
    //     if ($razorpayPayment->status !== 'captured') {
    //         return view('frontend.auth.failure', [
    //             'error' => 'Payment was not captured. Status: ' . $razorpayPayment->status
    //         ]);
    //     }

    //     // 7) Fetch the Product (ensure it’s active)
    //     $product = Product::where('uuid', $productUuid)->where('status', 1)->first();
    //     if (! $product) {
    //         Log::error("Product with UUID {$productUuid} not found or inactive.");
    //         return view('frontend.auth.failure', ['error' => 'Selected product is unavailable. Please contact support.']);
    //     }

    //     // 7) Create User, assign role, store Payment & UserDetail in one DB transaction
    //     try {
    //         \DB::beginTransaction();

    //         // a) Hash the password (we have 'password' and 'password_confirmation' in the request)
    //         $hashedPassword = Hash::make($request->input('password'));

    //         // b) Create the new user
    //         $user = User::create([
    //             'name'      => $name,
    //             'email'     => $email,
    //             'phone'     => $phone,
    //             'password'  => $hashedPassword,
    //             'country_code' => $countryCode,
    //         ]);

    //         // c) Assign "customer" role (as before)
    //         $user->assignRole('customer');

    //         // d) Insert into user_details table
    //         UserDetail::create([
    //             'user_id'      => $user->id,
    //             'billing_name' => $billingName,
    //             'state'        => $state,
    //             'city'         => $city,
    //             'pin_code'     => $pinCode,
    //             'address'      => $address,
    //             'pan_card'     => $panCard,
    //             'gst_number'   => $gstNumber,
    //         ]);

    //         // e) Store a Payment row
    //         $paymentModel = Payment::create([
    //             'user_id'             => $user->id,
    //             'product_id'          => $product->id,
    //             'razorpay_order_id'   => $orderId,
    //             'razorpay_payment_id' => $paymentId,
    //             'razorpay_signature'  => $signature,
    //             'amount'              => $razorpayPayment->amount/100,  // in ruppe
    //             'status'              => $razorpayPayment->status === "captured" ? "paid" : $razorpayPayment->status,  // should be “captured”
    //         ]);

    //         // $raw    = Str::upper(Str::random(32));
    //         // $salt   = Str::random(16);
    //         // $pepper = config('app.license_pepper');

    //         // // Argon2ID hash of pepper|salt|rawKey
    //         // $hashInput = "{$pepper}|{$salt}|{$raw}";
    //         // $keyHash   = password_hash($hashInput, PASSWORD_ARGON2ID);

    //         License::create([
    //             'user_id'    => $user->id,
    //             'product_id' => $product->id,
    //             'payment_id' => $paymentModel->id,
    //             'status'     => 'active',
    //             'issued_at'  => now()
    //         ]);

    //         \DB::commit();
    //     } catch (\Exception $e) {
    //         \DB::rollBack();
    //         Log::error('Database error during user/payment insert: ' . $e->getMessage());
    //         return view('frontend.auth.failure', [
    //             'error' => 'Unable to register and save payment. Contact support.'
    //         ]);
    //     }

    //     // ─── Automatically log in the newly created user ───
    //     Auth::login($user);

    //     // 8) Show success view (pass both $user and Razorpay payment)
    //     return view('frontend.auth.success', [
    //         'user'    => $user,
    //         'payment' => $razorpayPayment,
    //     ]);
    // }

    public function callback(Request $request)
    {
        // Sanitize phone input
        $request->merge([
            'phone' => preg_replace('/\D+/', '', $request->input('phone'))
        ]);

        // Validate incoming data
        $request->validate([
            'name'                   => 'required|string|max:255',
            'email'                  => 'required|string|email|max:255|unique:users,email',
            'country_code'           => 'required|string|max:5',
            'phone'                  => [
                'required',
                'string',
                'max:20',
                Rule::unique('users')->where(function ($query) use ($request) {
                    return $query->where('country_code', $request->input('country_code'));
                }),
            ],
            'password'               => 'required|string|min:8|confirmed',
            'product_uuid'           => 'required|string|exists:products,uuid',
            'razorpay_payment_id'    => 'required|string',
            'razorpay_order_id'      => 'required|string',
            'razorpay_signature'     => 'required|string',
            'billing_name'           => 'required|string|max:255',
            'state'                  => 'required|string|max:100',
            'city'                   => 'required|string|max:100',
            'pin_code'               => 'required|string|min:4|max:10',
            'address'                => 'required|string|max:500',
            'pan_card'               => 'nullable|string|max:20',
            'gst_number'             => 'nullable|string|max:20',
        ]);

        // Process the coupon code if provided
        $couponCode = $request->input('coupon_code', null);
        $discountAmount = 0;

        if ($couponCode) {
            // Fetch and validate the coupon
            $coupon = Coupon::where('coupon_code', $couponCode)
                ->where('status', 1)
                ->where('expiry_date', '>=', now())
                ->first();

            if (!$coupon) {
                return view('frontend.auth.failure', ['error' => 'Invalid or expired coupon.']);
            }

            // Calculate the discount
            $discountAmount = $coupon->discount_type === 'percentage'
                ? ($coupon->discount_amount / 100) * $request->input('amount')
                : $coupon->discount_amount;

            $discountAmount = min($discountAmount, $request->input('amount')); // Ensure discount doesn't exceed the total amount
        }

        // Proceed with other fields like Razorpay payment validation
        $paymentId = $request->input('razorpay_payment_id');
        $orderId = $request->input('razorpay_order_id');
        $signature = $request->input('razorpay_signature');
        $productUuid = $request->input('product_uuid');

        // Verify the Razorpay payment signature
        $razorpayKey = Config::get('services.razorpay.key');
        $razorpaySecret = Config::get('services.razorpay.secret');
        $api = new Api($razorpayKey, $razorpaySecret);

        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature
            ]);
        } catch (\Exception $e) {
            return view('frontend.auth.failure', ['error' => 'Payment verification failed.']);
        }

        // Fetch the product
        $product = Product::where('uuid', $productUuid)->where('status', 1)->first();
        if (!$product) {
            return view('frontend.auth.failure', ['error' => 'Product not found or inactive.']);
        }

        // Now create user, payment, and license
        try {
            \DB::beginTransaction();

            // Create the user
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'password' => Hash::make($request->input('password')),
                'country_code' => $request->input('country_code')
            ]);

            // Insert into user details
            UserDetail::create([
                'user_id' => $user->id,
                'billing_name' => $request->input('billing_name'),
                'state' => $request->input('state'),
                'city' => $request->input('city'),
                'pin_code' => $request->input('pin_code'),
                'address' => $request->input('address'),
                'pan_card' => $request->input('pan_card'),
                'gst_number' => $request->input('gst_number')
            ]);

            // Store the payment
            $payment = Payment::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'razorpay_order_id' => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature,
                'amount' => $request->input('amount') - $discountAmount,
                'coupon_code' => $couponCode,
                'discount_amount' => $discountAmount,
                'status' => 'paid'
            ]);

            // Create the license
            License::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'payment_id' => $payment->id,
                'status' => 'active',
                'issued_at' => now()
            ]);

            // Commit the transaction
            \DB::commit();

            // Automatically log the user in
            Auth::login($user);

            // Show success view
            return view('frontend.auth.success', [
                'user' => $user,
                'payment' => $payment
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            return view('frontend.auth.failure', ['error' => 'Failed to process the payment.']);
        }
    }

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255'
        ]);

        $exists = User::where('email', $request->input('email'))->exists();

        return response()->json(!$exists);
    }

    public function checkPhone(Request $request)
    {
        $request->validate([
            'country_code' => 'required|string|max:5',
            'phone'        => 'required|string|max:20'
        ]);

        $exists = User::where('country_code', $request->input('country_code'))
                      ->where('phone', $request->input('phone'))
                      ->exists();

        return response()->json(!$exists);
    }

    public function verifyCoupon(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'code' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0', // Total amount before discount
        ]);

        try {
            // Fetch the coupon by code and active status
            $coupon = Coupon::where('coupon_code', $request->code)
                ->where('status', 1)
                ->first();

            // Check if the coupon exists
            if (!$coupon) {
                return response()->json([
                    'status' => false,
                    'message' => 'The coupon code you entered is invalid or inactive.',
                ], 200);
            }

            // Check if the coupon has expired
            if ($coupon->expiry_date && $coupon->expiry_date < now()->toDateString()) {
                return response()->json([
                    'status' => false,
                    'message' => 'This coupon has expired.',
                ], 200);
            }

            // Check usage limit
            $useCount = Payment::where('coupon_code', $coupon->coupon_code)->count();
            $usageLimit = $coupon->usage_type === 'multiple' ? $coupon->usage_limit : 1;

            if ($useCount >= $usageLimit) {
                return response()->json([
                    'status' => false,
                    'message' => 'The usage limit for this coupon has been reached.',
                ], 200);
            }

            // Calculate the discount amount
            $discountAmount = round($coupon->discount_type === 'percentage'
                ? ($coupon->discount_amount / 100) * $request->amount
                : $coupon->discount_amount, 2);

            // Ensure discount doesn't exceed the total amount
            $discountAmount = min($discountAmount, $request->amount);

            // Ensure final total after discount is valid
            $finalTotal = round($request->amount - $discountAmount, 2);

            if ($finalTotal <= 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'This coupon is applicable only on purchases above ₹' . round($coupon->discount_amount, 2),
                ], 200);
            }

            $finalGst = round($finalTotal * 0.18, 2);
            $finalAmount = round($finalTotal + $finalGst, 2);

            // Return success response
            return response()->json([
                'status' => true,
                'message' => 'Coupon applied successfully!',
                'data' => [
                    'discount_type' => $coupon->discount_type,
                    'discount_amount' => round($discountAmount, 2),
                    'total' => round($finalTotal, 2),
                    'finalGst' => round($finalGst, 2),
                    'finalTotal' => round($finalAmount, 2)
                ],
            ], 200);

        } catch (\Exception $e) {
            // Catch unexpected errors and return a generic response
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while verifying the coupon. Please try again.',
                'error' => $e->getMessage(),
            ], 200);
        }
    }

    
}
