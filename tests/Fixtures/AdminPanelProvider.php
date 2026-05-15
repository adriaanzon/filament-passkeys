<?php

namespace AdriaanZon\FilamentPasskeys\Tests\Fixtures;

use AdriaanZon\FilamentPasskeys\Auth\PasskeyAuthentication;
use AdriaanZon\FilamentPasskeys\FilamentPasskeysPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->plugin(FilamentPasskeysPlugin::make())
            ->multiFactorAuthentication([
                PasskeyAuthentication::make(),
            ])
            ->middleware([
                'web',
                DispatchServingFilamentEvent::class,
                DisableBladeIconComponents::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
