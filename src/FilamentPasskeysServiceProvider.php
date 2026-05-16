<?php

declare(strict_types=1);

namespace AdriaanZon\FilamentPasskeys;

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentPasskeysServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-passkeys';

    public static string $viewNamespace = 'filament-passkeys';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews(static::$viewNamespace)
            ->hasTranslations();
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Js::make('filament-passkeys', __DIR__ . '/../resources/dist/filament-passkeys.js'),
            Css::make('filament-passkeys', __DIR__ . '/../resources/dist/filament-passkeys.css'),
        ], package: 'adriaanzon/filament-passkeys');

        RateLimiter::for('filament-passkeys.verify', function (Request $request): Limit {
            $encryptedUser = $request->query('user')
                ?? $request->session()->get('passkey.mfa_user');

            $key = is_string($encryptedUser) && filled($encryptedUser)
                ? sha1($encryptedUser)
                : $request->ip();

            return Limit::perMinute(5)->by('filament-passkeys.verify:' . $key);
        });

        RateLimiter::for('filament-passkeys.register', function (Request $request): Limit {
            $id = $request->user()?->getAuthIdentifier();
            $key = is_string($id) || is_int($id) ? $id : $request->ip();

            return Limit::perMinute(5)->by('filament-passkeys.register:' . $key);
        });
    }
}
