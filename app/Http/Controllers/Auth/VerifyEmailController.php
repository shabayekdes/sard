<?php

namespace App\Http\Controllers\Auth;

use App\Events\TenantVerified;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email as verified using the signed link (works without prior login).
     */
    public function __invoke(Request $request, string $id, string $hash): RedirectResponse
    {
        $user = $this->resolveUser($request, $id);

        if (! $user || ! hash_equals((string) $hash, (string) sha1($user->getEmailForVerification()))) {
            abort(403, 'This action is unauthorized.');
        }

        if ($user->hasVerifiedEmail()) {
            return $this->redirectToDashboard($request);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        // When company first admin verifies, seed default tenant data
        if ($user->tenant_id) {
            $verifiedCount = User::where('tenant_id', $user->tenant_id)->whereNotNull('email_verified_at')->count();
            if ($verifiedCount === 1) {
                $tenant = Tenant::find($user->tenant_id);
                if ($tenant instanceof Tenant) {
                    event(new TenantVerified($tenant));
                }
            }
        }

        if (! Auth::check() || Auth::id() !== (int) $id) {
            Auth::login($user);
        }

        return $this->redirectToDashboard($request);
    }

    private function resolveUser(Request $request, string $id): ?User
    {
        if (Auth::check() && (string) Auth::id() === $id) {
            return Auth::user();
        }

        $query = User::where('id', $id);

        if (function_exists('tenant') && tenant() !== null) {
            $query->where('tenant_id', tenant()->getTenantKey());
        }

        return $query->first();
    }

    private function redirectToDashboard(Request $request): RedirectResponse
    {
        $url = $request->getSchemeAndHttpHost() . '/dashboard?verified=1';

        return redirect()->away($url);
    }
}
