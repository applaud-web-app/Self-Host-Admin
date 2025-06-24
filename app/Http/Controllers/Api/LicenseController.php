<?php

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

            $license = $this->verificationService->findAndValidateLicense($data['license_key'], $domain, $ip, $data['email'], $data['username']);

            $this->verificationService->verifyLicenseCredentials($license, $data['license_key']);

            $this->verificationService->activateLicense($license, $domain, $ip);

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
                'An unexpected error occurred. Please try again later. : '. $e->getMessage(),
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

            // Validate the addon license credentials
            $this->verificationService->verifyLicenseCredentials($dataLicense['license'], $data['license_key']);

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
         // Start try-catch to handle potential errors
        try {
            // Validate incoming request to ensure necessary fields are provided
            $request->validate([
                'domain' => 'required|string|domain',
                'licenseKey' => 'required|string',
            ]);

            // Retrieve the domain and license key from the request
            $domain = $request->input('domain');
            $licenseKey = $request->input('licenseKey');

            // Look for the license in the database based on the provided license key
            $license = License::where('raw_key', $licenseKey)->first();

            // Check if the license exists
            if (!$license) {
                return response()->json([
                    'status' => 0,
                    'message' => 'License key not found.',
                ], 404);
            }

            // Check if the license is activated
            if (!$license->is_activated) {
                return response()->json([
                    'status' => 0,
                    'message' => 'License is not activated.',
                ], 403);
            }

            // Check if the license is activated on the correct domain
            if ($license->activated_domain !== $domain) {
                return response()->json([
                    'status' => 0,
                    'message' => 'License is not activated for this domain.',
                ], 403);
            }

            // License is valid and activated
            return response()->json([
                'status' => 1,
                'message' => 'License is valid and activated.',
            ]);

        } catch (Exception $e) {
            // Catch any exceptions and return an error response
            return response()->json([
                'status' => 2,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }


}
