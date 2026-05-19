<?php

declare(strict_types=1);

namespace AdriaanZon\FilamentPasskeys;

use BladeUI\Icons\Factory as BladeIconsFactory;
use Filament\Facades\Filament;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
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

    public function packageRegistered(): void
    {
        $this->callAfterResolving(BladeIconsFactory::class, function (BladeIconsFactory $factory): void {
            $factory->add('filament-passkeys', [
                'path' => __DIR__ . '/../resources/icons',
                'prefix' => 'filamentpasskeys',
            ]);
        });
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([
            Js::make('filament-passkeys', __DIR__ . '/../resources/dist/filament-passkeys.js'),
            Css::make('filament-passkeys', __DIR__ . '/../resources/dist/filament-passkeys.css'),
        ], package: 'adriaanzon/filament-passkeys');

        FilamentView::registerRenderHook(
            PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
            static function (): ?string {
                $panel = Filament::getCurrentPanel();

                if ($panel === null || ! $panel->hasPlugin('filament-passkeys')) {
                    return null;
                }

                /** @var FilamentPasskeysPlugin $plugin */
                $plugin = $panel->getPlugin('filament-passkeys');

                if (! $plugin->hasPasswordlessLogin()) {
                    return null;
                }

                return view('filament-passkeys::login-button')->render();
            },
        );
    }
}
