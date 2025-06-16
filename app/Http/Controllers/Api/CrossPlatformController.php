<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\License;
use App\Models\Product;

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
                fn () => Product::select('id', 'name', 'description', 'icon', 'version', 'price')
                                ->where('type', 'addon')
                                ->get()
            );

            /* 4 ─── Payload -------------------------------------------------- */
            $addonList = $allAddons->map(fn ($addon) => [
                'name'        => $addon->name ?? '',
                'description' => $addon->description ?? '',
                'icon'        => $addon->icon ? asset('storage/icons/'.$addon->icon) : '',
                'version'     => $addon->version ?? '',
                'price'       => '₹'.($addon->price ?? ''),
                'status'      => $purchasedIds->contains($addon->id) ? 'purchased' : 'available',
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
        /* 1 ─── Validate ------------------------------------------------- */
        $data = $request->validate([
            'license_key' => ['required', 'string'],
            'domain'      => ['required', 'string'],
        ]);

        try {
            /* 2 ─── One-shot query: licence + its payment + that product ---- */
            $license = License::select('id', 'payment_id')                 // only what we need
                ->where('raw_key',          $data['license_key'])
                ->where('activated_domain', $data['domain'])
                ->with([
                    'payment' => function ($q) {
                        $q->select(
                            'id', 'product_id',                 // joins
                            'razorpay_order_id',
                            'razorpay_payment_id',
                            'amount',
                            'status',
                            'created_at'
                        )
                        ->with('product:id,name,price');        // nested eager load
                    },
                ])
                ->first();

            if (!$license) {
                return response()->json(['error' => 'Invalid license key.'], 404);
            }

            $payment = $license->payment;
            if (!$payment) {
                return response()->json(['error' => 'Payment record not found.'], 404);
            }

            $product = $payment->product;                      // will be null-safe via eager load

            /* 3 ─── Return only the requested fields ----------------------- */
            return response()->json([
                'status' => 'success',
                'data'   => [
                    'order_id'      => $payment->razorpay_order_id,
                    'payment_id'    => $payment->razorpay_payment_id,
                    'amount'        => $payment->amount,
                    'status'        => $payment->status,
                    'name'          => $product?->name,
                    'price'         => $product?->price,
                    'purchase_date' => $payment->created_at,
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
