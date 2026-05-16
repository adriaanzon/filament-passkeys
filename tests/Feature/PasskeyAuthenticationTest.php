<?php

declare(strict_types=1);

use AdriaanZon\FilamentPasskeys\PasskeyAuthentication;
use AdriaanZon\FilamentPasskeys\Tests\Fixtures\User;

it('returns passkey as the provider id', function () {
    expect(PasskeyAuthentication::make()->getId())->toBe('passkey');
});

it('returns a login form label', function () {
    expect(PasskeyAuthentication::make()->getLoginFormLabel())->toBe('Use a passkey');
});

it('reports enabled when user has passkeys', function () {
    $user = User::create([
        'name' => 'Test',
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $user->passkeys()->create([
        'name' => 'Test Key',
        'credential_id' => 'test-credential-id',
        'credential' => ['id' => 'test'],
    ]);

    expect(PasskeyAuthentication::make()->isEnabled($user))->toBeTrue();
});

it('reports disabled when user has no passkeys', function () {
    $user = User::create([
        'name' => 'Test',
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    expect(PasskeyAuthentication::make()->isEnabled($user))->toBeFalse();
});

it('throws LogicException when user does not implement PasskeyUser', function () {
    $user = new Illuminate\Foundation\Auth\User;

    PasskeyAuthentication::make()->isEnabled($user);
})->throws(LogicException::class);
