<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Http\FormRequest;
use App\Services\LicenseVerificationService;
use App\Services\LicenseResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Exception;
use Log;
use App\Models\License;

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
            if($domain === "unknown"){
                return response()->json([
                    'valid' => false,
                    'message' => 'Domain not found..',
                ], 403);
            }
            $ip = $request->ip();

            $license = $this->verificationService->findAndValidateLicense($data['license_key'], $domain, $ip, $data['email'], $data['username']);

            $this->verificationService->verifyLicenseCredentials($data['license_key'], $domain);

            $this->verificationService->activateLicense($license, $domain, $ip);

            return $this->responseService->successResponse($license);

        } catch (ValidationException $e) {
            return $this->responseService->errorResponse(
                // 'Validation error: ' . collect($e->errors())->flatten()->join(' '),
                'Invalid Request Found',
                422
            );
        } catch (ModelNotFoundException $e) {
            return $this->responseService->errorResponse(
                'License not found or not eligible for activation. : '. $e->getMessage(),
                404
            );
        } catch (ThrottleRequestsException $e) {
            return $this->responseService->errorResponse(
                'Too many attempts. Try again later.',
                429
            );
        } catch (Exception $e) {
            return $this->responseService->errorResponse(
                $e->getMessage(),
                403
            );
        } catch (Exception $e) {
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
                'License verification failed',
                500
            );
        }
    }

    public function addonVerify(Request $request)
    {
        try {
            // Validate request for addon verification
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
                'addon_name' => ['required', 'string'],
                'addon_version' => ['required', 'string'],
            ]);

            // Apply rate limiting based on the license key and IP
            $this->verificationService->applyRateLimiting($request->ip(), $data['license_key']);
            $ip = $request->ip();
            
            // Validate and find the addon license
            $dataLicense = $this->verificationService->findAndValidateAddonLicense(
                $data['license_key'], $ip, $data['email'], $data['username']
            );

            return [
                "license" => $dataLicense['license'],
                "domain" => $dataLicense['domain']
            ];

            // Validate the addon license credentials
            $this->verificationService->verifyLicenseCredentials($dataLicense['license'], $dataLicense['domain']);

            // Activate the addon license --
            $this->verificationService->activateLicense($dataLicense['license'], $dataLicense['domain'], $ip);

            return $this->responseService->successResponse($dataLicense['license']);

        } catch (ValidationException $e) {
            return $this->responseService->errorResponse(
                'Validation Error: Payload is Required',
                422
            );
        } catch (ModelNotFoundException $e) {
            return $this->responseService->errorResponse(
                'Addon license not found or not eligible for activation.',
                404
            );
        } catch (ThrottleRequestsException $e) {
            return $this->responseService->errorResponse(
                'Too many attempts. Try again later.',
                429
            );
        } catch (Exception $e) {
            Log::error('Addon license verification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->responseService->errorResponse(
                'Invalid : ' . $e->getMessage(),
                500
            );
        }
    }

    public function verifyStatus(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'n' => 'required|string',
                'y' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'Invalid Request.',
                ], 200);
            }

            $domain     = strtolower($request->input('n'));

            if($domain === "localhost"){
                return response()->json([
                    'status'  => 1,
                    'message' => 'License is valid and activated.',
                ], 200);
            }
            $licenseKey = $request->input('y');

            // // Build a unique cache key for this domain+licenseKey
            // $cacheKey = "license:valid:{$licenseKey}:{$domain}";

            // // Cache the boolean existence check for 10 minutes
            // $isValid = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($licenseKey, $domain) {
            //     return License::where('raw_key', $licenseKey)
            //                   ->whereRaw('LOWER(activated_domain) = ?', [$domain])
            //                   ->where('is_activated', 1)
            //                   ->where('status', 'active')
            //                   ->exists();
            // });

            $isValid = License::where('raw_key', $licenseKey)
            ->whereRaw('LOWER(activated_domain) = ?', [$domain])
            ->where('is_activated', 1)
            ->where('status', 'active')
            ->exists();

            if ($isValid) {
                return response()->json([
                    'status'  => 1,
                    'message' => 'License is valid and activated.',
                ], 200);
            }

            return response()->json([
                'status'  => 0,
                'message' => 'License key not found or not active.',
            ], 200);

        } catch (Exception $e) {
            \Log::error('License verify failed: '.$e->getMessage(), ['trace'=>$e->getTrace()]);
            // Catch any exceptions and return an error response
            return response()->json([
                'status' => 2,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }


}
