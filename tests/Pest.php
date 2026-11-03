<?php

declare(strict_types=1);

use AdriaanZon\FilamentPasskeys\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/**
 * Minimal valid WebAuthn assertion credential, with every binary member
 * base64url-encoded without padding as the browser API emits it:
 * - id/rawId: the same raw bytes
 * - clientDataJSON: JSON with type, challenge, origin
 * - authenticatorData: rpIdHash(32) + flags(1) + counter(4)
 * - signature: any bytes
 *
 * @return array<string, mixed>
 */
function fakeAssertionCredential(): array
{
    return [
        'id' => 'dGVzdGNyZWRlbnRpYWxpZA',
        'rawId' => 'dGVzdGNyZWRlbnRpYWxpZA',
        'type' => 'public-key',
        'response' => [
            'clientDataJSON' => 'eyJ0eXBlIjoid2ViYXV0aG4uZ2V0IiwiY2hhbGxlbmdlIjoiZEdWemRBIiwib3JpZ2luIjoiaHR0cDpcL1wvbG9jYWxob3N0In0',
            'authenticatorData' => 'SZYN5YgOjGh0NBcPZHZgW4_krrmihjLHmVzzuoMdl2MBAAAAAA',
            'signature' => 'ZmFrZS1zaWc',
        ],
    ];
}
