<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\License;
use App\Models\Product;
use App\Models\Payment; 

class CrossPlatformController extends Controller
{
   public function addonList(Request $request)
    {
        /* 1 ─── Validate ---------------------------------------------------- */
        $data = $request->validate([
            'license_key' => ['required', 'string'],
            'domain'      => ['required', 'string'],
        ]);

        try {
            /* 2 ─── Core licence + just-enough eager data  (query 1) -------- */
            $license = License::with([
                    'user.licenses' => function ($q) {
                        $q->select('id', 'user_id', 'product_id')          // bare minimum
                        ->whereHas('product', fn ($q) => $q->where('type', 'addon'));
                    },
                ])
                ->select('id', 'user_id')                                   // nothing else used
                ->where('raw_key',          $data['license_key'])
                ->where('activated_domain', $data['domain'])
                ->first();

            if (!$license) {
                return response()->json(['error' => 'Invalid license key.'], 404);
            }

            $purchasedIds = $license->user->licenses->pluck('product_id')->unique();

            /* 3 ─── All add-on products (cached 1 hr, query 2 on miss) ----- */
            $allAddons = Cache::remember(
                'all_addon_products',
                now()->addHour(),                              
                fn () => Product::select('id', 'uuid','name', 'description', 'icon', 'version', 'price')
                                ->where('type', 'addon')
                                ->get()
            );

            /* 4 ─── Payload -------------------------------------------------- */
            $addonList = $allAddons->map(fn ($addon) => [
                'name'        => $addon->name ?? '',
                'description' => $addon->description ?? '',
                'icon'        => $addon->icon ? asset('storage/icons/'.$addon->icon) : asset('images/17335857.png'),
                'version'     => $addon->version ?? '',
                'price'       => '₹'.($addon->price ?? ''),
                'status'      => $purchasedIds->contains($addon->id) ? 'purchased' : 'available',
                'purchase_url'  => "https://selfhost.awmtab.in/purchase",
                'key'  => $addon->uuid,
            ]);

            return response()->json([
                'status' => 'success',
                'addons' => $addonList,
            ], 200);

        } catch (\Throwable $e) {
            Log::error('addonList API failed', [
                'input' => $data,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'addons' => [],
            ], 500);
        }
    }

    public function subscriber(Request $request)
    {
        /* 1 ─── Validate -------------------------------------------------- */
        $data = $request->validate([
            'license_key' => ['required', 'string'],
            'domain'      => ['required', 'string'],
        ]);

        try {
            /* 2 ─── Verify licence (query 1) ------------------------------ */
            $license = License::select('id', 'user_id')
                ->where('raw_key',          $data['license_key'])
                ->where('activated_domain', $data['domain'])
                ->first();

            if (!$license) {
                return response()->json(['error' => 'Invalid license key.'], 404);
            }

            /* 3 ─── Latest “core” payment for this user (query 2) ---------- */
            $payment = Payment::with('product:id,name,price,version,description')
                ->select(
                    'id', 'product_id',
                    'razorpay_order_id',
                    'razorpay_payment_id',
                    'amount',
                    'status',
                    'created_at'
                )
                ->where('user_id', $license->user_id)
                ->whereHas('product', fn ($q) => $q->where('type', 'core'))
                ->latest()                         // ORDER BY created_at DESC
                ->first();

            if (!$payment) {
                return response()->json(['error' => 'No core payment found.'], 404);
            }

            $product = $payment->product;          // eager-loaded, so no extra query

            /* 4 ─── Response ------------------------------------------------ */
            return response()->json([
                'status' => 'success',
                'data'   => [
                    'order_id'      => $payment->razorpay_order_id,
                    'payment_id'    => $payment->razorpay_payment_id,
                    'paid_amount'   => "₹".$payment->amount,
                    'status'        => $payment->status,
                    'name'          => $product?->name,
                    'version'       => $product?->version,
                    'description'   => $product?->description,
                    'purchase_date' => $payment->created_at->timezone('Asia/Kolkata')->format('d-M-Y'),
                    'supprot_mobile'=> "+919012400499",
                    'supprot_email' => "tdevansh099@gmail.com",
                ],
            ], 200);

        } catch (\Throwable $e) {
            Log::error('subscriber API failed', [
                'input' => $data,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'data'   => [],
            ], 500);
        }
    }
}
