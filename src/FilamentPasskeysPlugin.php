<?php

declare(strict_types=1);

namespace AdriaanZon\FilamentPasskeys;

use AdriaanZon\FilamentPasskeys\Http\Controllers\PasskeyRegistrationController;
use AdriaanZon\FilamentPasskeys\Http\Controllers\PasskeyVerificationController;
use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\Facades\Route;

class FilamentPasskeysPlugin implements Plugin
{
    protected string | Closure | null $loginFormLabel = null;

    public function getId(): string
    {
        return 'filament-passkeys';
    }

    public function register(Panel $panel): void
    {
        $panel->authenticatedRoutes(function (): void {
            Route::prefix('passkeys')->middleware('throttle:filament-passkeys.register')->group(function (): void {
                Route::get('register/options', [PasskeyRegistrationController::class, 'index'])
                    ->name('passkeys.register.options');
                Route::post('register', [PasskeyRegistrationController::class, 'store'])
                    ->name('passkeys.register');
            });
        });

        $panel->routes(function (): void {
            Route::prefix('passkeys')->middleware('throttle:filament-passkeys.verify')->group(function (): void {
                Route::get('verify/options', [PasskeyVerificationController::class, 'index'])
                    ->name('passkeys.verify.options');
                Route::post('verify', [PasskeyVerificationController::class, 'store'])
                    ->name('passkeys.verify');
            });
        });
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public function loginFormLabel(string | Closure $label): static
    {
        $this->loginFormLabel = $label;

        return $this;
    }

    public function getLoginFormLabel(): ?string
    {
        return $this->loginFormLabel instanceof Closure
            ? ($this->loginFormLabel)()
            : $this->loginFormLabel;
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
