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
        // Validation errors will already return 422 JSON responses
        $data = $request->validate([
            'license_key' => ['required', 'string'],
            'domain'      => ['required', 'string'],
        ]);

        try {
            /* ─────────────────── 1. core licence + eager-loaded data (query 1) */
            $license = License::with([
                    'user.licenses' => function ($q) {
                        $q->select('id', 'user_id', 'product_id')
                          ->whereHas('product', fn ($q) => $q->where('type', 'addon'))
                          ->with('product:id,type');
                    },
                ])
                ->select([
                    'id', 'user_id', 'product_id', 'status',
                    'activated_domain', 'activated_ip', 'issued_at', 'raw_key'
                ])
                ->where('raw_key',          $data['license_key'])
                ->where('activated_domain', $data['domain'])
                ->first();

            if (! $license) {
                return response()->json(['error' => 'Invalid license key.'], 404);
            }

            /* ─────────────────── 2. purchased add-ons in memory */
            $purchasedIds = $license->user
                ->licenses
                ->pluck('product_id')
                ->unique();

            /* ─────────────────── 3. all add-on products (cached, query 2 on miss) */
            $allAddons = Cache::remember(
                'all_addon_products',                          // cache key
                now()->addHour(),                              // TTL
                fn () => Product::select('id','name','slug','version','price')
                                ->where('type', 'addon')
                                ->get()
            );

            /* ─────────────────── 4. construct payload */
            $addonList = $allAddons->map(
                fn ($addon) => [
                    'name'    => $addon->name,
                    'description' => $addon->description,
                    'icon'    => asset('storage/icons/'.$addon->icon),
                    'version' => $addon->version,
                    'price'   => "₹".$addon->price,
                    'status'  => $purchasedIds->contains($addon->id) ? 'purchased' : 'available',
                ]
            );

            return response()->json([
                'status'=>'success',
                'addons' => $addonList,
            ], 200);

        } catch (\Throwable $e) {
            /* ─────────────────── 5. graceful degradation ────────────────── */
            Log::error('addonList API failed', [
                'input'  => $data,
                'error'  => $e->getMessage(),
                'trace'  => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status'=>'error',
                'addons'   => [],
            ], 500);
        }
    }
}
