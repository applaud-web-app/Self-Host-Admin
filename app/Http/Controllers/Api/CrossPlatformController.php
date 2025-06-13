<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\License;
use App\Models\Product;

class CrossPlatformController extends Controller
{
    public function addonList(Request $request)
    {
        $data = $request->validate([
            'license_key' => 'required|string',
            'domain'      => 'required|string',
        ]);

        try {
            // 1) Find the core license
            $license = License::where('raw_key', $data['license_key'])->where('activated_domain', $data['domain'])
            ->first();

            if (! $license) {
                return response()->json([
                    'error' => 'Invalid license key.'
                ], 404);
            }

            // 3) Grab all "addon" products
            $allAddons = Product::where('type', 'addon')->get();

            // 4) Find which addons this user already owns
            $purchasedIds = $license->user
            ->licenses()
            ->whereHas('product', fn($q) => $q->where('type', 'addon'))
            ->pluck('product_id')
            ->toArray();

            // 5) Build the response payload
            $addonList = $allAddons->map(function($addon) use ($purchasedIds) {
                return [
                    'id'      => $addon->id,
                    'name'    => $addon->name,
                    'slug'    => $addon->slug,
                    'version' => $addon->version,
                    'price'   => $addon->price,
                    'status'  => in_array($addon->id, $purchasedIds) ? 'purchased' : 'available',
                ];
            });

            return response()->json([
                'core_license' => [
                    'id'               => $license->id,
                    'user_id'          => $license->user_id,
                    'product_id'       => $license->product_id,
                    'status'           => $license->status,
                    'activated_domain' => $license->activated_domain,
                    'activated_ip'     => $license->activated_ip,
                    'issued_at'        => $license->issued_at,
                ],
                'addons' => $addonList,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'false',
                'error' => $e->getMessage(),
            ], 200);
        }
    }
}
