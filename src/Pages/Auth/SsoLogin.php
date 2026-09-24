<?php

namespace JeffersonGoncalves\Filament\SsoClient\Pages\Auth;

use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use JeffersonGoncalves\Filament\SsoClient\SsoClientPlugin;

class SsoLogin extends Login
{
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
        abort_unless(SsoClientPlugin::get()->hasLoginForm(), 403);

        return parent::authenticate();
    }

    public function content(Schema $schema): Schema
    {
        if (SsoClientPlugin::get()->hasLoginForm()) {
            return parent::content($schema);
        }

        return $schema->components([
            Actions::make([$this->getSsoAction()])->fullWidth(),
        ]);
    }

    protected function getFormActions(): array
    {
        return [
            ...parent::getFormActions(),
            $this->getSsoAction(),
        ];
    }

    protected function getSsoAction(): Action
    {
        return Action::make('sso')
            ->label(SsoClientPlugin::get()->getButtonLabel())
            ->icon(Heroicon::ArrowRightEndOnRectangle)
            ->color('gray')
            ->url(route('sso-client.redirect'));
    }
}
