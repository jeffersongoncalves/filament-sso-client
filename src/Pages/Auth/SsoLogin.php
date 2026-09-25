<?php

namespace JeffersonGoncalves\Filament\SsoClient\Pages\Auth;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login;
use JeffersonGoncalves\Filament\SsoClient\SsoClientPlugin;

class SsoLogin extends Login
{
    protected static string $view = 'filament-sso-client::pages.auth.login';

    public function mount(): void
    {
        // The guest was stored as "url.intended" by Filament; the SSO callback returns there.
        if (Filament::auth()->guest() && SsoClientPlugin::get()->isForceRedirect()) {
            $this->redirect(route('sso-client.redirect'));

            return;
        }

        parent::mount();
    }

    public function authenticate(): ?LoginResponse
    {
        // The form is hidden, so local credentials must not be accepted either.
        abort_unless($this->hasLoginForm(), 403);

        return parent::authenticate();
    }

    public function hasLoginForm(): bool
    {
        return SsoClientPlugin::get()->hasLoginForm();
    }

    protected function getFormActions(): array
    {
        return [
            ...($this->hasLoginForm() ? parent::getFormActions() : []),
            $this->getSsoAction(),
        ];
    }

    protected function getSsoAction(): Action
    {
        return Action::make('sso')
            ->label(SsoClientPlugin::get()->getButtonLabel())
            ->icon('heroicon-m-arrow-right-end-on-rectangle')
            ->color('gray')
            ->url(route('sso-client.redirect'));
    }
}
