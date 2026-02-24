<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = $this->only('email', 'password');
        $remember = $this->boolean('remember');

        // When tenant is initialized (tenant domain), scope login to that tenant
        if (function_exists('tenancy') && tenancy()->initialized) {
            $user = \App\Models\User::where('email', $credentials['email'])
                ->where('tenant_id', tenant()->getTenantKey())
                ->first();

            if (! $user || ! Hash::check($credentials['password'], $user->password)) {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'email' => __('auth.failed'),
                ]);
            }

            Auth::login($user, $remember);
        } else {
            // Central domain: only users with null tenant_id (e.g. superadmin)
            $user = \App\Models\User::where('email', $credentials['email'])
                ->whereNull('tenant_id')
                ->first();

            if (! $user || ! Hash::check($credentials['password'], $user->password)) {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'email' => __('auth.failed'),
                ]);
            }

            Auth::login($user, $remember);
        }

        // Check if user account is inactive
        $user = Auth::user();
        if ($user->status === 'inactive') {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => __('Your account is inactive. Please contact administrator.'),
            ]);
        }

        // Check if client account is inactive (for client users)
        if ($user->type === 'client') {
            $client = \App\Models\Client::where('email', $user->email)
                ->where('tenant_id', $user->tenant_id)
                ->first();
            if ($client && $client->status === 'inactive') {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => __('Your account is inactive. Please contact administrator.'),
                ]);
            }
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
