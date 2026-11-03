<?php

declare(strict_types=1);

use AdriaanZon\FilamentPasskeys\Tests\Fixtures\User;
use Filament\Facades\Filament;
use Illuminate\Testing\TestResponse;

beforeEach(function () {
    $this->user = User::create([
        'name' => 'Test',
        'email' => 'test@example.com',
        'password' => 'password',
    ]);
});

function confirmationOptions(string $token): TestResponse
{
    return test()->getJson('/admin/passkeys/confirm/options?user=' . urlencode($token));
}

/**
 * Mirrors the state Filament's login page stores for the user undertaking MFA.
 */
function loginPageMfaState(User $user, ?string $userKey = null): string
{
    return encrypt([
        'identifier' => $user->getAuthIdentifier(),
        'userKey' => $userKey ?? Filament::getUserScopedAuthIdentifier($user),
    ]);
}

it('returns confirmation options for an MFA-pending user', function () {
    confirmationOptions(loginPageMfaState($this->user))
        ->assertOk()
        ->assertJsonStructure(['options']);
});

it('rejects confirmation options request without user parameter', function () {
    $this->getJson('/admin/passkeys/confirm/options')
        ->assertStatus(403);
});

it('rejects confirmation options request with invalid encrypted user', function () {
    confirmationOptions('invalid-garbage')
        ->assertStatus(403);
});

it('rejects a bare encrypted user id', function () {
    confirmationOptions(encrypt($this->user->getKey()))
        ->assertStatus(403);
});

it('rejects a user key scoped to another principal', function () {
    confirmationOptions(loginPageMfaState($this->user, hash('sha256', 'other')))
        ->assertStatus(403);
});

it('rejects a state without a user key', function () {
    confirmationOptions(encrypt(['identifier' => $this->user->getKey()]))
        ->assertStatus(403);
});

it('rejects a state for an unknown user', function () {
    $state = loginPageMfaState($this->user);

    $this->user->delete();

    confirmationOptions($state)
        ->assertStatus(403);
});
