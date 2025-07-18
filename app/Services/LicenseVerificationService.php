<?php

namespace App\Services;

use App\Models\License;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Exception;

class LicenseVerificationService
{
    const MAX_ATTEMPTS = 10;
    const DECAY_SECONDS = 60;
    const LICENSE_KEY_MIN_LENGTH = 12;
    const LICENSE_KEY_MAX_LENGTH = 64;
    const USERNAME_MAX_LENGTH = 60;
    const EMAIL_MAX_LENGTH = 100;
    const ADDON_PRODUCT_TYPE = 'addon';

    public function applyRateLimiting(string $ip, string $licenseKey): void
    {
        $key = 'verify:' . $ip . ':' . $licenseKey;
        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            Log::warning('Rate limit exceeded', ['ip' => $ip, 'licenseKey' => $licenseKey]);
            throw new HttpResponseException(response()->json([
                'message' => 'Too many attempts. Try again later.'
            ], 429));
        }
        RateLimiter::hit($key, self::DECAY_SECONDS);
    }

    public function getRequestDomain(\Illuminate\Http\Request $request): string
    {
        $origin = $request->header('Origin');
        $referer = $request->header('Referer');
        $domain = $origin 
            ? parse_url($origin, PHP_URL_HOST) 
            : ($referer ? parse_url($referer, PHP_URL_HOST) : null);
        return $domain ?: 'unknown';
    }

    public function findAndValidateLicense(string $licenseKey, string $domain, string $ip, string $email, string $username): License
    {
        // 1. Check for already activated license
        $alreadyActivated = License::where('raw_key', $licenseKey)
            ->where('is_activated', true)
            ->first();
        if ($alreadyActivated) {
            Log::warning('License already activated', [
                'license' => $licenseKey,
                'domain' => $domain,
                'activated_domain' => $alreadyActivated->activated_domain,
            ]);
            throw new Exception(
                'This license key has already been used.'
            );
        }

        // 2. Find eligible license
        $license = License::with(['payment', 'product','user'])
            ->where([
                ['raw_key', '=', $licenseKey],
                ['activated_domain', '=', $domain],
                ['status', '=', 'active'],
                ['is_activated', '=', false],
            ])
            ->first();

        if (! $license) {
            throw new ModelNotFoundException('License not found or not eligible for activation.');
        }

        // 3. Validate user credentials if user_id exists
        if ($license->user_id) {
            if (!$license->user) {
                throw new Exception('Associated user account not found.', 403);
            }

            // Normalize email comparison
            $providedEmail = strtolower(trim($email));
            $userEmail = strtolower(trim($license->user->email));
            
            if ($license->user->name !== $username || $userEmail !== $providedEmail) {
                Log::warning('User credentials mismatch', [
                    'license' => $licenseKey,
                    'expected_username' => $license->user->name,
                    'provided_username' => $username,
                    'expected_email' => $license->user->email,
                    'provided_email' => $email,
                ]);
                throw new Exception('Invalid License credentials.', 403);
            }
        }

        // 3. Validate payment status
        if (! $license->payment || $license->payment->status !== 'paid') {
            Log::info('License/payment invalid', [
                'license' => $license->raw_key, 'domain' => $domain
            ]);
            throw new Exception('Payment or license status invalid.', 403);
        }

        // 4. Validate product type
        if ($license->product->type !== 'core') {
            Log::info('Product type not core', [
                'license' => $license->raw_key, 'domain' => $domain
            ]);
            throw new Exception('This license is not applicable here.', 403);
        }

        return $license;
    }

    public function verifyLicenseCredentials(string $licenseKey, string $domain): void
    {

        $isValid = License::where('raw_key', $licenseKey)
        ->whereRaw('LOWER(activated_domain) = ?', [$domain])
        ->where('is_activated', 0)
        ->where('status', 'active')
        ->exists();

        if (! $isValid) {
           throw new Exception('Invalid license credentials.', 403);
        }
    }

    public function activateLicense(
        License $license,
        string $domain,
        string $ip,
    ): void {
        // Use transaction for safety
        \DB::transaction(function () use ($license, $domain, $ip) {
            $license->update([
                'is_activated' => true,
            ]);
        });

        Log::info('License activated successfully', [
            'license' => $license->raw_key,
            'domain' => $domain,
            'ip' => $ip,
        ]);
    }


    public function findAndValidateAddonLicense(string $licenseKey, string $ip, string $email, string $username): array
    {
        // 1. Check if the addon license has already been activated
        $alreadyActivated = License::whereHas('product', function($query) {
                $query->where('type', 'addon');
            })
            ->where('raw_key', $licenseKey)
            ->where('is_activated', 1)
            ->first();

        if ($alreadyActivated) {
            Log::warning('Addon license already activated', [
                'license' => $licenseKey,
                'activated_domain' => $alreadyActivated->activated_domain,
            ]);
            throw new Exception('This addon license key has already been used.');
        }

        // 2. Find the addon license (specifically checking for addon product type)
        $license = License::with(['payment', 'product', 'user'])
            ->whereHas('product', function($query) {
                $query->where('type', 'addon');
            })
            ->where([
                ['raw_key', '=', $licenseKey],
                ['status', '=', 'active'],
                ['is_activated', '=', 0],
            ])
            ->first();

        if (! $license) {
            throw new ModelNotFoundException('Addon license not found or not eligible for activation.');
        }

        // 3. Validate user credentials for addon license
        if ($license->user_id) {
            if (!$license->user) {
                throw new Exception('Associated user account not found for addon.', 403);
            }

            $domain = $this->getLatestCoreLicenseDomain($license->user_id);

            // Normalize email comparison for addon
            $providedEmail = strtolower(trim($email));
            $userEmail = strtolower(trim($license->user->email));

            if ($license->user->name !== $username || $userEmail !== $providedEmail) {
                throw new Exception('Invalid addon license credentials.', 403);
            }
        }

        // 4. Validate payment status (similar to core validation)
        if (! $license->payment || $license->payment->status !== 'paid') {
            Log::info('Addon license/payment invalid', [
                'license' => $license->raw_key, 'domain' => $domain
            ]);
            throw new Exception('Addon license payment or status invalid.', 403);
        }

        return [
            'license'=>$license,
            'domain'=>$domain
        ];
    }

    public function getLatestCoreLicenseDomain(int $userId): ?string
    {
        $license = License::with(['product'])
            ->where('user_id', $userId)
            ->whereHas('product', function($query) {
                $query->where('type', 'core');
            })
            ->where('is_activated', 1)
            ->latest('created_at')
            ->first();

        return $license ? $license->activated_domain : null;
    }
}