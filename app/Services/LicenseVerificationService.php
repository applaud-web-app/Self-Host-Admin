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
                'This license key has already been used and is locked to domain: ' . $alreadyActivated->activated_domain
            );
        }

        // 2. Find eligible license
        $license = License::with(['payment', 'product','user'])
            ->where([
                ['raw_key', '=', $licenseKey],
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
                throw new Exception('License credentials do not match the associated user account.', 403);
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

    public function verifyLicenseCredentials(License $license, string $licenseKey): void
    {
        $pepper = config('app.license_pepper');
        $check = "{$pepper}|{$license->key_salt}|{$licenseKey}";
        if (!password_verify($check, $license->key_hash)) {
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
                'activated_domain' => $domain,
                'activated_ip' => $ip,
                'is_activated' => true,
                'activated_at' => now(),
            ]);
        });

        Log::info('License activated successfully', [
            'license' => $license->raw_key,
            'domain' => $domain,
            'ip' => $ip,
        ]);
    }
}