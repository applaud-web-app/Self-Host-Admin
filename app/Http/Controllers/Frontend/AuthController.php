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
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentSuccess;

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

        $product = Product::select('price','uuid','name')
                        ->where('uuid', $requestedUuid)
                        ->where('status', 1)
                        ->where('type','core')
                        ->first();

        if(empty($product)){
            return back()->withErrors('Invalid Request! Please contact site admin');
        }

        // Calculate amounts (price is inclusive of 18% GST)
        $totalAmount = (float) $product->price;
        $gstAmount = $totalAmount * 0.18; // Extract base price before GST
        $subtotal = $totalAmount - $gstAmount;

        // Pass data to view
        $data = [
            'razorpayKey' => config('services.razorpay.key'),
            'product'     => $product,
            'unit_price'  => $subtotal, // This is the price before GST
            'subtotal'    => $subtotal,
            'gstAmount'   => $gstAmount,
            'totalAmount' => $totalAmount,
        ];

        return view('frontend.auth.checkout', $data);
    }

    public function callback(Request $request)
    {
        // Sanitize phone input
        $request->merge([
            'phone' => preg_replace('/\D+/', '', $request->input('phone'))
        ]);
       
        // Validate incoming data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'country_code' => 'required|string|max:5',
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users')->where(function ($query) use ($request) {
                    return $query->where('country_code', $request->input('country_code'));
                }),
            ],
            'password' => 'required|string|min:8|confirmed',
            'product_uuid' => 'required|string|exists:products,uuid',
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'billing_name' => 'required|string|max:255',
            'state' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'pin_code' => 'required|string|min:4|max:10',
            'address' => 'required|string|max:500',
            'pan_card' => 'nullable|string|max:20',
            'gst_number' => 'nullable|string|max:20',
            'coupon_code' => 'nullable|string|max:50',
        ]);

        // Process the coupon code if provided
        $couponCode = $request->input('coupon_code');
        $discountAmount = 0;
        
        // Get product
        $product = Product::where('uuid', $request->product_uuid)->where('status', 1)->first();
        if (!$product) {
            return view('frontend.auth.failure', ['error' => 'Product not found or inactive.']);
        }

        // Calculate amounts
        $totalAmount = $product->price; // 100
        $gstAmount = $totalAmount * 0.18; // 18
        $subtotal = $totalAmount - $gstAmount; // 100 - 18

        if ($couponCode) {
            // Fetch and validate the coupon again (for security)
            $coupon = Coupon::where('coupon_code', $couponCode)
                ->where('status', 1)
                ->where('expiry_date', '>=', now())
                ->first();

            if ($coupon) {
                // Calculate the discount (on subtotal before GST)
                $discountAmount = $coupon->discount_type === 'percentage'
                    ? ($coupon->discount_amount / 100) * $subtotal
                    : $coupon->discount_amount;

                // Ensure discount doesn't exceed the subtotal
                $discountAmount = min($discountAmount, $subtotal);
                
                // Recalculate amounts with discount
                $subtotal = $subtotal - $discountAmount;
                $gstAmount = $subtotal * 0.18;
                $totalAmount = $subtotal + $gstAmount;
            }
        }

        // Verify the Razorpay payment signature
        try {
            $api = new Api(Config::get('services.razorpay.key'), Config::get('services.razorpay.secret'));
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ]);
        } catch (\Exception $e) {
            return view('frontend.auth.failure', ['error' => 'Payment verification failed.']);
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

            // c) Assign "customer" role (as before)
            $user->assignRole('customer');

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

            // Store the payment with all amount details
            $payment = Payment::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
                'amount' => $totalAmount,
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

            try {
                // $user->email
                Mail::to('tdevansh099@gmail.com')->send(new PaymentSuccess($user, $product, $payment, $request->razorpay_order_id));
            } catch (\Throwable $th) {
                Log::error('mail error ' . $th->getMessage());
            }

            \DB::commit();

            // Automatically log the user in
            Auth::login($user);

            return view('frontend.auth.success', [
                'user' => $user,
                'payment' => $payment
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Checkout failed: ' . $e->getMessage());
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
            'amount' => 'required|numeric|min:0', // Unit price (before any discounts or tax)
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

            // Calculate the discount amount (on unit price)
            $unitPrice = $request->amount;
            $discountAmount = $coupon->discount_type === 'percentage'
                ? ($coupon->discount_amount / 100) * $unitPrice
                : $coupon->discount_amount;

            // Ensure discount doesn't exceed the unit price
            $discountAmount = min($discountAmount, $unitPrice);
            
            // Calculate new subtotal after discount
            $newSubtotal = $unitPrice - $discountAmount;
            
            // Calculate GST (18% of new subtotal)
            $gstAmount = $newSubtotal * 0.18;
            
            // Calculate final total
            $finalTotal = $newSubtotal + $gstAmount;

            // Check if total becomes zero or negative
            if ($finalTotal <= 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'This coupon is applicable only on purchases above ₹' . $coupon->discount_amount,
                ], 200);
            }

            // Return success response
            return response()->json([
                'status' => true,
                'message' => 'Coupon applied successfully!',
                'data' => [
                    'unit_price' => round($unitPrice, 2),
                    'discount_type' => $coupon->discount_type,
                    'discount_amount' => round($discountAmount, 2),
                    'subtotal' => round($newSubtotal, 2),
                    'gst_amount' => round($gstAmount, 2),
                    'final_amount' => round($finalTotal, 2),
                    'coupon_code' => $coupon->coupon_code
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while verifying the coupon. Please try again.',
                'error' => $e->getMessage(),
            ], 200);
        }
    }

    public function razorpayOrderCreate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'product_uuid' => 'required|exists:products,uuid'
        ]);

        try {
            $product = Product::where('uuid',$request->product_uuid)->first();
            $amount = intval($request->amount * 100); // Convert to paise

            $api = new Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );

            $order = $api->order->create([
                'amount' => $amount,
                'currency' => 'INR',
                'receipt' => 'rcpt_'.time(),
                'payment_capture' => 1
            ]);

            return response()->json([
                'status' => true,
                'order_id' => $order->id
            ]);

        } catch (\Exception $e) {
            Log::error('Razorpay Order Error: '.$e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Payment initialization failed'
            ], 500);
        }
    }
    
}
