<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\License;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class LicenseController extends Controller
{
    public function verify(Request $request)
    {
        // Rate limit by IP and key
        $key = 'verify:' . $request->ip() . ':' . $request->input('license_key');
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json([
                'valid' => false,
                'message' => 'Too many attempts. Try again later.'
            ], 429);
        }
        RateLimiter::hit($key, 60); // 60 seconds decay

        // Validate license key format (extra)
        $data = $request->validate([
            'license_key' => 'required|string|min:12|max:60', // adjust as needed
        ]);



        // Get domain from Origin or Referer
        $origin  = $request->headers->get('origin');
        $referer = $request->headers->get('referer');
        $domain = null;

        if ($origin) {
            $domain = parse_url($origin, PHP_URL_HOST);
        } elseif ($referer) {
            $domain = parse_url($referer, PHP_URL_HOST);
        }
        $domain = $domain ?: 'unknown';

        // Allow only whitelisted domains in production (configurable)
        // $allowedDomains = ['yourfrontend.com', 'yourclientsite.com'];
        // if (app()->environment('production') && !in_array($domain, $allowedDomains)) {
        //     Log::warning("Verification from unauthorized domain: $domain");
        //     return response()->json([
        //         'valid' => false,
        //         'message' => 'Unauthorized domain.'
        //     ], 403);
        // }

        $ip = $request->ip();

        // Get the whole request body for debug
        $body = $request->all();

        // Return only domain and the request body
        return response()->json([
            'domain' => $domain,
            'body'   => $body,
        ]);

        $license = License::with(['payment', 'product'])
            ->where('key', $data['license_key'])
            ->first();

        // Fail if not found
        if (!$license) {
            Log::info('License not found', ['key' => $data['license_key'], 'domain' => $domain, 'ip' => $ip]);
            return response()->json([
                'valid'   => false,
                'message' => 'License not found.'
            ], 404);
        }

        // Fail if not active/paid
        if (
            $license->status !== 'active' ||
            !$license->payment ||
            $license->payment->status !== 'paid'
        ) {
            Log::info('License/payment invalid', ['license' => $license->key, 'domain' => $domain]);
            return response()->json([
                'valid'   => false,
                'message' => 'Payment or license status invalid.'
            ], 403);
        }

        // Fail if not a core product
        if ($license->product->type !== 'core') {
            Log::info('Product type not core', ['license' => $license->key, 'domain' => $domain]);
            return response()->json([
                'valid'   => false,
                'message' => 'This license is not for a core product.'
            ], 403);
        }

        // Bind on first use, only if not already bound
        if (!$license->activated_domain && $domain !== 'unknown') {
            $license->update([
                'activated_domain' => $domain,
                'activated_ip'     => $ip,
            ]);
        }

        // Prevent use on different domain (after bound)
        if ($license->activated_domain && $license->activated_domain !== $domain) {
            Log::warning('License domain mismatch', [
                'license' => $license->key,
                'expected_domain' => $license->activated_domain,
                'got_domain' => $domain,
            ]);
            return response()->json([
                'valid'   => false,
                'message' => 'License already activated on another domain.',
            ], 403);
        }

        // Log successful verifications
        Log::info('License verification successful', [
            'license' => $license->key,
            'domain' => $domain,
            'ip' => $ip,
        ]);

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

    public function debugDomain(Request $request)
    {
        // Get domain from Origin or Referer
        $origin  = $request->headers->get('origin');
        $referer = $request->headers->get('referer');
        $domain = null;

        if ($origin) {
            $domain = parse_url($origin, PHP_URL_HOST);
        } elseif ($referer) {
            $domain = parse_url($referer, PHP_URL_HOST);
        }
        $domain = $domain ?: 'unknown';

        // Get the whole request body for debug
        $body = $request->all();

        // Return only domain and the request body
        return response()->json([
            'domain' => $domain,
            'body'   => $body,
        ]);
    }
}