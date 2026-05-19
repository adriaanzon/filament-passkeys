<?php

declare(strict_types=1);

use AdriaanZon\FilamentPasskeys\Tests\Fixtures\User;
use Laravel\Passkeys\Actions\VerifyPasskey;

it('returns passwordless verification options without allowCredentials', function () {
    $response = $this->getJson('/admin/passkeys/login/options');

    $response->assertOk();
    $response->assertJsonStructure(['options']);

    $options = $response->json('options');

    expect($options['allowCredentials'] ?? [])->toBe([]);
});

it('stores options in the session', function () {
    $this->getJson('/admin/passkeys/login/options')->assertOk();

    expect(session()->has('passkey.verification_options'))->toBeTrue();
});

it('rejects login with no session options', function () {
    $payload = [
        'credential' => [
            'id' => 'abc',
            'rawId' => 'abc',
            'type' => 'public-key',
            'response' => ['clientDataJSON' => 'x', 'authenticatorData' => 'x', 'signature' => 'x'],
        ],
    ];

    $this->postJson('/admin/passkeys/login', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['credential']);

    $this->assertGuest();
});

it('rejects login with malformed credential', function () {
    $this->getJson('/admin/passkeys/login/options')->assertOk();

    $this->postJson('/admin/passkeys/login', ['credential' => 'not-an-array'])
        ->assertStatus(422);
});

it('logs the user in on a successful assertion', function () {
    $user = User::create([
        'name' => 'Test',
        'email' => 'pw@example.com',
        'password' => 'password',
    ]);

    $passkey = $user->passkeys()->create([
        'name' => 'Test Key',
        'credential_id' => 'dGVzdGNyZWRlbnRpYWxpZA',
        'credential' => ['id' => 'dGVzdGNyZWRlbnRpYWxpZA'],
    ]);

    $this->getJson('/admin/passkeys/login/options')->assertOk();

    $this->mock(VerifyPasskey::class, function ($mock) use ($passkey) {
        $mock->shouldReceive('__invoke')->once()->andReturn($passkey);
    });

    // Minimal valid WebAuthn assertion credential:
    // - id/rawId: base64url/base64 of the same raw bytes
    // - clientDataJSON: base64url of JSON with type, challenge, origin
    // - authenticatorData: base64 of rpIdHash(32) + flags(1) + counter(4)
    // - signature: any base64-encoded bytes
    $payload = [
        'credential' => [
            'id' => 'dGVzdGNyZWRlbnRpYWxpZA',
            'rawId' => 'dGVzdGNyZWRlbnRpYWxpZA==',
            'type' => 'public-key',
            'response' => [
                'clientDataJSON' => 'eyJ0eXBlIjoid2ViYXV0aG4uZ2V0IiwiY2hhbGxlbmdlIjoiZEdWemRBIiwib3JpZ2luIjoiaHR0cDpcL1wvbG9jYWxob3N0In0',
                'authenticatorData' => 'SZYN5YgOjGh0NBcPZHZgW4/krrmihjLHmVzzuoMdl2MBAAAAAA==',
                'signature' => 'ZmFrZS1zaWc=',
            ],
        ],
    ];

    $response = $this->postJson('/admin/passkeys/login', $payload);

    $response->assertOk();
    $response->assertJsonStructure(['redirect']);

    $this->assertAuthenticatedAs($user);
});

it('renders the sign in with passkey button on the login page', function () {
    $response = $this->get('/admin/login');

    $response->assertOk();
    $response->assertSee('fi-passkeys-login-button', false);
    $response->assertSee('\/admin\/passkeys\/login\/options', false);
});
