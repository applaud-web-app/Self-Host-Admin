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

            // Get the domain of the request
            $domain = $this->verificationService->getRequestDomain($request);
            $ip = $request->ip();

            // Validate and find the addon license
            $license = $this->verificationService->findAndValidateAddonLicense(
                $data['license_key'], $domain, $ip, $data['email'], $data['username']
            );

            // Validate the addon license credentials
            $this->verificationService->verifyLicenseCredentials($license, $data['license_key']);

            // Activate the addon license
            $this->verificationService->activateLicense($license, $domain, $ip);

            return $this->responseService->successResponse($license);

        } catch (ValidationException $e) {
            return $this->responseService->errorResponse(
                'Validation error: ' . collect($e->errors())->flatten()->join(' '),
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
                'An unexpected error occurred. Please try again later: ' . $e->getMessage(),
                500
            );
        }
    }

}
