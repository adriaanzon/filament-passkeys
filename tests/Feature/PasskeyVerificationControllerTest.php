<?php

declare(strict_types=1);

use AdriaanZon\FilamentPasskeys\Tests\Fixtures\User;

it('returns verification options for an MFA-pending user', function () {
    $user = User::create([
        'name' => 'Test',
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $encryptedUserId = encrypt($user->getKey());

    $response = $this->getJson('/admin/passkeys/verify/options?user=' . urlencode($encryptedUserId));

    $response->assertOk();
    $response->assertJsonStructure(['options']);
});

it('rejects verification options request without user parameter', function () {
    $this->getJson('/admin/passkeys/verify/options')
        ->assertStatus(403);
});

it('rejects verification options request with invalid encrypted user', function () {
    $this->getJson('/admin/passkeys/verify/options?user=invalid-garbage')
        ->assertStatus(403);
});
