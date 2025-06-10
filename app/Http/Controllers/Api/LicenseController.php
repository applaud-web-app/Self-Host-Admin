<?php
// namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Models\License;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\RateLimiter;
// use Illuminate\Support\Facades\Validator;
// use Illuminate\Validation\ValidationException;
// use Illuminate\Database\Eloquent\ModelNotFoundException;
// use Exception;

// class LicenseController extends Controller
// {
    
//     // Constants for configuration
//     const MAX_ATTEMPTS = 10;
//     const DECAY_SECONDS = 60;
//     const LICENSE_KEY_MIN_LENGTH = 12;
//     const LICENSE_KEY_MAX_LENGTH = 64;
//     const USERNAME_MAX_LENGTH = 60;
//     const EMAIL_MAX_LENGTH = 100;

//     public function verify(Request $request)
//     {
//         // Rate limit by IP and key
//         $key = 'verify:' . $request->ip() . ':' . $request->input('license_key');
//         if (RateLimiter::tooManyAttempts($key, 10)) {
//             return response()->json([
//                 'valid' => false,
//                 'message' => 'Too many attempts. Try again later.'
//             ], 429);
//         }
//         RateLimiter::hit($key, 60); // 60 seconds decay

//         // Validate license key format (extra)
//         $data = $request->validate([
//             'license_key' => 'required|string|min:12|max:64',
//             'username'    => 'required|string|max:60',
//             'email'       => 'required|email|max:100'
//         ]);

//         // Get domain from Origin or Referer
//         $domain  = $origin
//         ? parse_url($origin, PHP_URL_HOST)
//         : ($referer ? parse_url($referer, PHP_URL_HOST) : null);
//         $domain  = $domain ?: 'unknown';
//         $ip = $request->ip();

//         $license = License::where([
//             ['raw_key', '=', $data['license_key']],
//             ['type', '=', 'core'],
//             ['status', '=', 'active'],
//             ['is_activated', '=', false]
//         ])->first();

//         if (! $license) {
//             $alreadyActivated = License::where('raw_key', $data['license_key'])
//                 ->where('is_activated', true)
//                 ->first();
//             if ($alreadyActivated) {
//                 return response()->json([
//                     'valid' => false,
//                     'message' => 'This license key has already been used and is locked to another domain.'
//                 ], 403);
//             }
//             return response()->json([
//                 'valid' => false,
//                 'message' => 'License not found or not eligible for activation.'
//             ], 404);
//         }

//         // Fail if payment not active/paid
//         if (
//             $license->status !== 'active' ||
//             !$license->payment ||
//             $license->payment->status !== 'paid'
//         ) {
//             Log::info('License/payment invalid', ['license' => $license->key, 'domain' => $domain]);
//             return response()->json([
//                 'valid'   => false,
//                 'message' => 'Payment or license status invalid.'
//             ], 403);
//         }

//         // 6. Salt+pepper+password_verify check
//         $pepper = config('app.license_pepper');
//         $check  = "{$pepper}|{$license->key_salt}|{$data['license_key']}";
//         if (! password_verify($check, $license->key_hash)) {
//             return response()->json([
//                 'valid' => false,
//                 'message' => 'Invalid license credentials.'
//             ], 403);
//         }

//         $license->activated_domain = $domain;
//         $license->activated_ip     = $ip;
//         $license->activated_email  = $data['email'];
//         $license->activated_user   = $data['username'];
//         $license->is_activated     = true;
//         $license->activated_at     = now();
//         $license->save();

//         return response()->json([
//             'valid'    => true,
//             'message'  => 'License activated successfully.',
//             'license'  => [
//                 'key'      => $license->raw_key,
//                 'domain'   => $license->activated_domain,
//             ],
//             'product'  => [
//                 'slug'    => $license->product->slug ?? null,
//                 'version' => $license->product->version ?? null,
//                 'name'    => $license->product->name ?? null,
//             ]
//         ]);


//         // Allow only whitelisted domains in production (configurable)
//         // $allowedDomains = ['yourfrontend.com', 'yourclientsite.com'];
//         // if (app()->environment('production') && !in_array($domain, $allowedDomains)) {
//         //     Log::warning("Verification from unauthorized domain: $domain");
//         //     return response()->json([
//         //         'valid' => false,
//         //         'message' => 'Unauthorized domain.'
//         //     ], 403);
//         // }

        

//         // Fail if not a core product
//         if ($license->product->type !== 'core') {
//             Log::info('Product type not core', ['license' => $license->key, 'domain' => $domain]);
//             return response()->json([
//                 'valid'   => false,
//                 'message' => 'This license is not applicable here.'
//             ], 403);
//         }

//         // Bind on first use, only if not already bound
//         if (!$license->activated_domain && $domain !== 'unknown') {
//             $license->update([
//                 'activated_domain' => $domain,
//                 'activated_ip'     => $ip,
//             ]);
//         }

//         // Prevent use on different domain (after bound)
//         if ($license->activated_domain && $license->activated_domain !== $domain) {
//             Log::warning('License domain mismatch', [
//                 'license' => $license->key,
//                 'expected_domain' => $license->activated_domain,
//                 'got_domain' => $domain,
//             ]);
//             return response()->json([
//                 'valid'   => false,
//                 'message' => 'License already activated on another domain.',
//             ], 403);
//         }

//         // Log successful verifications
//         Log::info('License verification successful', [
//             'license' => $license->key,
//             'domain' => $domain,
//             'ip' => $ip,
//         ]);

//         return response()->json([
//             'valid'       => true,
//             'license_key' => $license->key,
//             'activated'   => [
//                 'domain' => $license->activated_domain,
//                 'ip'     => $license->activated_ip,
//             ],
//             'product' => [
//                 'slug'    => $license->product->slug,
//                 'version' => $license->product->version,
//                 'name'    => $license->product->name,
//             ],
//         ]);
//     }

//     public function debugDomain(Request $request)
//     {
//         // Get domain from Origin or Referer
//         $origin  = $request->headers->get('origin');
//         $referer = $request->headers->get('referer');
//         $domain = null;

//         if ($origin) {
//             $domain = parse_url($origin, PHP_URL_HOST);
//         } elseif ($referer) {
//             $domain = parse_url($referer, PHP_URL_HOST);
//         }
//         $domain = $domain ?: 'unknown';

//         // Get the whole request body for debug
//         $body = $request->all();

//         // Return only domain and the request body
//         return response()->json([
//             'domain' => $domain,
//             'body'   => $body,
//         ]);
//     }
// }

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LicenseVerificationService;
use App\Services\LicenseResponseService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Exception;
use Log;

class LicenseController extends Controller
{
    private $verificationService;
    private $responseService;

    public function __construct(
        LicenseVerificationService $verificationService,
        LicenseResponseService $responseService
    ) {
        $this->verificationService = $verificationService;
        $this->responseService = $responseService;
    }

    public function verify(Request $request)
    {
        try {
            $data = $request->validate([
                'license_key' => [
                    'required', 'string',
                    'min:' . LicenseVerificationService::LICENSE_KEY_MIN_LENGTH,
                    'max:' . LicenseVerificationService::LICENSE_KEY_MAX_LENGTH,
                ],
                'username' => [
                    'required', 'string',
                    'max:' . LicenseVerificationService::USERNAME_MAX_LENGTH,
                ],
                'email' => [
                    'required', 'email',
                    'max:' . LicenseVerificationService::EMAIL_MAX_LENGTH,
                ],
            ]);

            $this->verificationService->applyRateLimiting($request->ip(), $data['license_key']);

            $domain = $this->verificationService->getRequestDomain($request);
            $ip = $request->ip();

            $license = $this->verificationService->findAndValidateLicense($data['license_key'], $domain, $ip);

            $this->verificationService->verifyLicenseCredentials($license, $data['license_key']);

            $this->verificationService->activateLicense($license, $domain, $ip, $data['email'], $data['username']);

            return $this->responseService->successResponse($license);

        } catch (ValidationException $e) {
            return $this->responseService->errorResponse(
                'Validation error: ' . collect($e->errors())->flatten()->join(' '),
                422
            );
        } catch (ModelNotFoundException $e) {
            return $this->responseService->errorResponse(
                'License not found or not eligible for activation.',
                404
            );
        } catch (ThrottleRequestsException $e) {
            return $this->responseService->errorResponse(
                'Too many attempts. Try again later.',
                429
            );
        } catch (\App\Exceptions\LicenseAlreadyActivatedException $e) {
            return $this->responseService->errorResponse(
                $e->getMessage(),
                403
            );
        } catch (\App\Exceptions\LicenseVerificationException $e) {
            return $this->responseService->errorResponse(
                $e->getMessage(),
                403
            );
        } catch (Exception $e) {
            Log::error('License verification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->responseService->errorResponse(
                'An unexpected error occurred. Please try again later.',
                500
            );
        }
    }
}
