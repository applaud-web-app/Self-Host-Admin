<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\License;
class LicenseController extends Controller
{
    /**
     * POST /api/license/verify
     */
    public function verify(Request $request)
    {
        // 1) Only require the key; ignore any user-supplied domain/ip in the body
        $data = $request->validate([
            'license_key' => 'required|string',
        ]);
        
        // 2) Pull the real requester info from the HTTP layer:
        $domain = $request->getHost();  // equivalent to Host header
        $ip     = $request->ip();
        // 3) Find the license (with its payment & product)
        $license = License::with(['payment', 'product'])
                          ->where('key', $data['license_key'])
                          ->first();
        if (! $license) {
            return response()->json([
                'valid'   => false,
                'message' => 'License not found.'
            ], 404);
        }
        // 4) Check that the license has been issued/activated/paid
        if ($license->status !== 'active'
            || ! $license->payment
            || $license->payment->status !== 'paid'
        ) {
            return response()->json([
                'valid'   => false,
                'message' => 'Payment or license status invalid.'
            ], 403);
        }
        // 5) (Optional) enforce “core product first”
        if ($license->product->type !== 'core') {
            return response()->json([
                'valid'   => false,
                'message' => 'This license is not for a core product.'
            ], 403);
        }
        // 6) (Optional) record the very first activation domain/ip
        if (! $license->activated_domain) {
            $license->update([
                'activated_domain' => $domain,
                'activated_ip'     => $ip,
            ]);
        }
        // 7) (Optional) prevent activation on a different domain later
        if ($license->activated_domain !== $domain) {
            return response()->json([
                'valid'   => false,
                'message' => 'License already activated on another domain.',
            ], 403);
        }
        // 8) All good!
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