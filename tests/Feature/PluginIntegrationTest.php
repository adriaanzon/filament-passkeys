<?php

declare(strict_types=1);

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

    expect($routes)->toContain('filament.admin.passkey.registration-options');
    expect($routes)->toContain('filament.admin.passkey.store');
    expect($routes)->toContain('filament.admin.passkey.confirm-options');
    expect($routes)->toContain('filament.admin.passkey.confirm');
});

it('reports passwordless login disabled by default', function () {
    expect(FilamentPasskeysPlugin::make()->hasPasswordlessLogin())->toBeFalse();
});

it('enables passwordless login via the builder', function () {
    $plugin = FilamentPasskeysPlugin::make()->passwordlessLogin();

    expect($plugin->hasPasswordlessLogin())->toBeTrue();
});
