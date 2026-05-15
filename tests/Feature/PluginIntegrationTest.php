<?php

use AdriaanZon\FilamentPasskeys\FilamentPasskeysPlugin;
use Filament\Facades\Filament;

it('registers the plugin on the panel', function () {
    $plugin = Filament::getPanel('admin')->getPlugin('filament-passkeys');

    expect($plugin)->toBeInstanceOf(FilamentPasskeysPlugin::class);
});

it('includes PasskeyAuthentication in MFA providers', function () {
    $providers = Filament::getMultiFactorAuthenticationProviders();
    $ids = array_map(fn ($p) => $p->getId(), $providers);

    expect($ids)->toContain('passkey');
});

it('registers passkey routes on the panel', function () {
    $routes = collect(app('router')->getRoutes()->getRoutes())
        ->map(fn ($route) => $route->getName())
        ->filter()
        ->values()
        ->all();

    expect($routes)->toContain('filament.admin.passkeys.register.options');
    expect($routes)->toContain('filament.admin.passkeys.register');
    expect($routes)->toContain('filament.admin.passkeys.verify.options');
    expect($routes)->toContain('filament.admin.passkeys.verify');
});
