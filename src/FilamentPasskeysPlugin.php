<?php

declare(strict_types=1);

namespace AdriaanZon\FilamentPasskeys;

use AdriaanZon\FilamentPasskeys\Http\Controllers\PasskeyLoginController;
use AdriaanZon\FilamentPasskeys\Http\Controllers\PasskeyRegistrationController;
use AdriaanZon\FilamentPasskeys\Http\Controllers\PasskeyVerificationController;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\Facades\Route;

class FilamentPasskeysPlugin implements Plugin
{
    protected bool $passwordlessLogin = false;

    public function getId(): string
    {
        return 'filament-passkeys';
    }

    public function register(Panel $panel): void
    {
        $throttle = (array) config('passkeys.throttle');

        $panel->authenticatedRoutes(function () use ($throttle): void {
            Route::prefix('passkeys')->middleware($throttle)->group(function (): void {
                Route::get('register/options', [PasskeyRegistrationController::class, 'index'])
                    ->name('passkeys.register.options');
                Route::post('register', [PasskeyRegistrationController::class, 'store'])
                    ->name('passkeys.register');
            });
        });

        $panel->routes(function () use ($throttle): void {
            Route::prefix('passkeys')->middleware($throttle)->group(function (): void {
                Route::get('verify/options', [PasskeyVerificationController::class, 'index'])
                    ->name('passkeys.verify.options');
                Route::post('verify', [PasskeyVerificationController::class, 'store'])
                    ->name('passkeys.verify');
            });
        });

        if ($this->passwordlessLogin) {
            $panel->routes(function () use ($throttle): void {
                Route::prefix('passkeys')
                    ->middleware($throttle)
                    ->group(function (): void {
                        Route::get('login/options', [PasskeyLoginController::class, 'index'])
                            ->name('passkeys.login.options');
                        Route::post('login', [PasskeyLoginController::class, 'store'])
                            ->name('passkeys.login');
                    });
            });
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public function passwordlessLogin(bool $enabled = true): static
    {
        $this->passwordlessLogin = $enabled;

        return $this;
    }

    public function hasPasswordlessLogin(): bool
    {
        return $this->passwordlessLogin;
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
