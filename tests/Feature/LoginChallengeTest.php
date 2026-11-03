<?php

declare(strict_types=1);

use AdriaanZon\FilamentPasskeys\Tests\Fixtures\User;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passkeys\Actions\VerifyPasskey;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

beforeEach(function () {
    config(['auth.providers.users.model' => User::class]);
    Auth::forgetGuards();

    Filament::setCurrentPanel('admin');

    $this->user = User::create([
        'name' => 'Test',
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);

    $this->user->passkeys()->create([
        'name' => 'Test Key',
        'credential_id' => 'dGVzdC1jcmVkZW50aWFsLWlk',
        'credential' => ['id' => 'test'],
    ]);
});

function loginUpToPasskeyChallenge(): Testable
{
    return Livewire::test(Login::class)
        ->fillForm(['email' => 'test@example.com', 'password' => 'password'])
        ->call('authenticate')
        ->assertHasNoFormErrors()
        ->assertSet('userUndertakingMultiFactorAuthentication', fn (mixed $state): bool => filled($state));
}

it('accepts the login page MFA state on the confirmation endpoint', function () {
    $state = loginUpToPasskeyChallenge()->get('userUndertakingMultiFactorAuthentication');

    $this->getJson('/admin/passkeys/confirm/options?user=' . urlencode($state))
        ->assertOk()
        ->assertJsonStructure(['options']);
});

it('signs the user in after the passkey is verified through the confirmation endpoint', function () {
    $component = loginUpToPasskeyChallenge();
    $state = $component->get('userUndertakingMultiFactorAuthentication');

    $this->getJson('/admin/passkeys/confirm/options?user=' . urlencode($state))
        ->assertOk();

    $this->mock(VerifyPasskey::class, function ($mock) {
        $mock->shouldReceive('__invoke')->once()->andReturn($this->user->passkeys()->first());
    });

    $this->postJson('/admin/passkeys/confirm', ['credential' => fakeAssertionCredential()])
        ->assertOk();

    expect(Filament::auth()->check())->toBeFalse();

    $component
        ->set('data.multiFactor.passkey.credential', 'verified')
        ->call('authenticate')
        ->assertHasNoFormErrors();

    expect(Filament::auth()->id())->toBe($this->user->getKey());
});

it('signs the user in once the passkey has been verified', function () {
    $component = loginUpToPasskeyChallenge();

    session()->put('filament-passkeys.mfa-verified.' . $this->user->getKey(), true);

    $component
        ->set('data.multiFactor.passkey.credential', 'verified')
        ->call('authenticate')
        ->assertHasNoFormErrors();

    expect(Filament::auth()->id())->toBe($this->user->getKey());
});

it('keeps the user signed out until the passkey has been verified', function () {
    loginUpToPasskeyChallenge()
        ->set('data.multiFactor.passkey.credential', 'verified')
        ->call('authenticate')
        ->assertHasFormErrors(['multiFactor.passkey.credential']);

    expect(Filament::auth()->check())->toBeFalse();
});
