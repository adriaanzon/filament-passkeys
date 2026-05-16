<?php

declare(strict_types=1);

use AdriaanZon\FilamentPasskeys\Tests\Fixtures\User;

beforeEach(function () {
    $this->user = User::create([
        'name' => 'Test',
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $this->actingAs($this->user);
});

it('returns registration options', function () {
    $response = $this->getJson('/admin/passkeys/register/options');

    $response->assertOk();
    $response->assertJsonStructure(['options']);
});

it('rejects unauthenticated requests for registration options', function () {
    auth()->logout();

    $this->getJson('/admin/passkeys/register/options')
        ->assertUnauthorized();
});
