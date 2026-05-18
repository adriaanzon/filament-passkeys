<?php

declare(strict_types=1);

use AdriaanZon\FilamentPasskeys\Tests\Fixtures\User;

it('returns confirmation options for an MFA-pending user', function () {
    $user = User::create([
        'name' => 'Test',
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $encryptedUserId = encrypt($user->getKey());

    $response = $this->getJson('/admin/passkeys/confirm/options?user=' . urlencode($encryptedUserId));

    $response->assertOk();
    $response->assertJsonStructure(['options']);
});

it('rejects confirmation options request without user parameter', function () {
    $this->getJson('/admin/passkeys/confirm/options')
        ->assertStatus(403);
});

it('rejects confirmation options request with invalid encrypted user', function () {
    $this->getJson('/admin/passkeys/confirm/options?user=invalid-garbage')
        ->assertStatus(403);
});
