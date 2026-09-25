<?php

use Filament\Facades\Filament;
use JeffersonGoncalves\Filament\SsoClient\Pages\Auth\SsoLogin;
use JeffersonGoncalves\Filament\SsoClient\SsoClientPlugin;
use JeffersonGoncalves\Filament\SsoClient\Tests\Fixtures\User;
use JeffersonGoncalves\SsoClient\Services\SsoClientManager;
use Livewire\Livewire;

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

function user(): User
{
    return User::create(['name' => 'Jane', 'email' => 'jane@example.com', 'password' => 'x']);
}

it('uses the SSO login page', function (): void {
    expect(Filament::getCurrentPanel()->getLoginRouteAction())->toBe(SsoLogin::class);
});

it('shows the SSO button next to the login form', function (): void {
    $this->get('/admin/login')
        ->assertOk()
        ->assertSee(route('sso-client.redirect'), false)
        ->assertSee('Sign in with SSO')
        ->assertSee('wire:submit="authenticate"', false);
});

it('uses a custom button label', function (): void {
    SsoClientPlugin::get()->buttonLabel('Corporate account');

    $this->get('/admin/login')->assertSee('Corporate account');
});

it('hides the login form and refuses credentials', function (): void {
    SsoClientPlugin::get()->loginForm(false);

    $this->get('/admin/login')
        ->assertOk()
        ->assertSee(route('sso-client.redirect'), false)
        ->assertDontSee('wire:submit="authenticate"', false);

    Livewire::test(SsoLogin::class)->call('authenticate')->assertForbidden();
});

it('redirects guests straight to the SSO Server', function (): void {
    SsoClientPlugin::get()->forceRedirect();

    Livewire::test(SsoLogin::class)->assertRedirect(route('sso-client.redirect'));
});

it('sends guests of the panel to the login page', function (): void {
    $this->get('/admin')->assertRedirect('/admin/login');
});

it('points the user menu logout at the federated logout', function (): void {
    $this->actingAs(user());

    $logout = Filament::getCurrentPanel()->getUserMenuItems()['logout'];

    expect($logout->getUrl())->toBe(route('sso-client.logout'));
});

it('ends panel sessions revoked by Single Logout', function (): void {
    $this->actingAs(user())
        ->withSession([
            SsoClientManager::SESSION_SUB => 'sub-1',
            SsoClientManager::SESSION_AUTHENTICATED_AT => microtime(true) - 10,
        ]);

    app(SsoClientManager::class)->logoutSubject('sub-1');

    $this->get('/admin')->assertRedirect(route('sso-client.redirect'));
    $this->assertGuest();
});
