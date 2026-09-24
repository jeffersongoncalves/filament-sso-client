<?php

namespace JeffersonGoncalves\Filament\SsoClient\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use JeffersonGoncalves\SsoClient\Http\Middleware\EnsureSsoAuthenticated;
use Symfony\Component\HttpFoundation\Response;

/**
 * Runs laravel-sso-client's "sso.auth" for signed-in users only, so sessions
 * ended by Single Logout are closed on the panel. Guests are left to
 * Filament's Authenticate, which sends them to the panel login page.
 */
class EndRevokedSsoSession
{
    public function __construct(
        protected EnsureSsoAuthenticated $ssoAuth,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (Filament::auth()->guest()) {
            return $next($request);
        }

        return $this->ssoAuth->handle($request, $next, Filament::getAuthGuard());
    }
}
