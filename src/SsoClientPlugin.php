<?php

namespace JeffersonGoncalves\Filament\SsoClient;

use Closure;
use Filament\Actions\Action;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use JeffersonGoncalves\Filament\SsoClient\Http\Middleware\EndRevokedSsoSession;
use JeffersonGoncalves\Filament\SsoClient\Pages\Auth\SsoLogin;

class SsoClientPlugin implements Plugin
{
    use EvaluatesClosures;

    protected bool|Closure $forceRedirect = false;

    protected bool|Closure $hasLoginForm = true;

    protected string|Closure|null $buttonLabel = null;

    public function getId(): string
    {
        return 'filament-sso-client';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->login(SsoLogin::class)
            // Guests pass through to Filament's Authenticate (login page + canAccessPanel());
            // signed-in users are checked against Single Logout on every request.
            ->authMiddleware([EndRevokedSsoSession::class], isPersistent: true)
            ->userMenuItems([
                'logout' => fn (Action $action): Action => $action->url(route('sso-client.logout')),
            ]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static */
        return filament(app(static::class)->getId());
    }

    /**
     * Send guests straight to the SSO Server instead of showing the login page.
     */
    public function forceRedirect(bool|Closure $condition = true): static
    {
        $this->forceRedirect = $condition;

        return $this;
    }

    public function isForceRedirect(): bool
    {
        return (bool) $this->evaluate($this->forceRedirect);
    }

    /**
     * Keep the local email/password form next to the SSO button.
     */
    public function loginForm(bool|Closure $condition = true): static
    {
        $this->hasLoginForm = $condition;

        return $this;
    }

    public function hasLoginForm(): bool
    {
        return (bool) $this->evaluate($this->hasLoginForm);
    }

    public function buttonLabel(string|Closure|null $label): static
    {
        $this->buttonLabel = $label;

        return $this;
    }

    public function getButtonLabel(): string
    {
        return $this->evaluate($this->buttonLabel) ?? __('filament-sso-client::messages.button');
    }
}
