<?php

namespace JeffersonGoncalves\Filament\SsoClient;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SsoClientServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-sso-client')
            ->hasTranslations();
    }
}
