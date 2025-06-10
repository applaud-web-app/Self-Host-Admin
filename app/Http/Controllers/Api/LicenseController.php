<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\License;

class LicenseController extends Controller
{
    public function verify(Request $request)
    {
        $data = $request->validate([
            'license_key' => 'required|string',
        ]);

        $origin  = $request->headers->get('origin');
        $referer = $request->headers->get('referer');
        $domain = null;
        if ($origin) {
            $domain = parse_url($origin, PHP_URL_HOST);
        } elseif ($referer) {
            $domain = parse_url($referer, PHP_URL_HOST);
        }
        $domain = $domain ?: 'unknown';

        $ip = $request->ip();

        $license = License::with(['payment', 'product'])
            ->where('key', $data['license_key'])
            ->first();

        if (!$license) {
            return response()->json([
                'valid'   => false,
                'message' => 'License not found.'
            ], 404);
        }
        if ($license->status !== 'active' || !$license->payment || $license->payment->status !== 'paid') {
            return response()->json([
                'valid'   => false,
                'message' => 'Payment or license status invalid.'
            ], 403);
        }
        if ($license->product->type !== 'core') {
            return response()->json([
                'valid'   => false,
                'message' => 'This license is not for a core product.'
            ], 403);
        }
        if (!$license->activated_domain && $domain !== 'unknown') {
            $license->update([
                'activated_domain' => $domain,
                'activated_ip'     => $ip,
            ]);
        }
        if ($license->activated_domain && $license->activated_domain !== $domain) {
            return response()->json([
                'valid'   => false,
                'message' => 'License already activated on another domain.',
            ], 403);
        }
        return response()->json([
            'valid'       => true,
            'license_key' => $license->key,
            'activated'   => [
                'domain' => $license->activated_domain,
                'ip'     => $license->activated_ip,
            ],
            'product' => [
                'slug'    => $license->product->slug,
                'version' => $license->product->version,
                'name'    => $license->product->name,
            ],
        ]);
    }
}