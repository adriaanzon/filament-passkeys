<?php

declare(strict_types=1);

namespace AdriaanZon\FilamentPasskeys\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Passkeys\Actions\GenerateVerificationOptions;
use Laravel\Passkeys\Actions\VerifyPasskey;
use Laravel\Passkeys\Contracts\PasskeyUser;
use Laravel\Passkeys\Http\Requests\PasskeyVerificationRequest;
use Laravel\Passkeys\Passkeys;
use Laravel\Passkeys\Support\WebAuthn;
use Throwable;

class PasskeyVerificationController extends Controller
{
    public function index(Request $request, GenerateVerificationOptions $generate): JsonResponse
    {
        $user = $this->resolveUser($request);

        abort_unless($user instanceof PasskeyUser, 403);

        $options = $generate($user);

        $serialized = WebAuthn::toJson($options);

        $request->session()->put('passkey.verification_options', $serialized);
        $request->session()->put('passkey.mfa_user', $request->query('user'));

        return response()->json([
            'options' => json_decode($serialized, true),
        ]);
    }

    public function store(
        PasskeyVerificationRequest $request,
        VerifyPasskey $verify,
    ): JsonResponse {
        $user = $this->resolveUserFromSession($request);

        abort_unless($user instanceof PasskeyUser, 403);

        $verify(
            $request->credential(),
            $request->verificationOptions(),
            $user
        );

        $id = $user->getAuthIdentifier();
        assert(is_string($id) || is_int($id));

        $request->session()->put(
            'filament-passkeys.mfa-verified.' . $id,
            true
        );

        $request->session()->forget('passkey.mfa_user');

        return response()->json();
    }

    protected function resolveUser(Request $request): ?Authenticatable
    {
        $encrypted = $request->query('user');

        if (! is_string($encrypted) || blank($encrypted)) {
            return null;
        }

        try {
            $userId = decrypt($encrypted);
        } catch (Throwable) {
            return null;
        }

        if (! is_string($userId) && ! is_int($userId)) {
            return null;
        }

        /** @var class-string<Authenticatable> $userModel */
        $userModel = Passkeys::userModel();

        return $userModel::find($userId);
    }

    protected function resolveUserFromSession(Request $request): ?Authenticatable
    {
        $encrypted = $request->session()->get('passkey.mfa_user');

        if (! is_string($encrypted) || blank($encrypted)) {
            return null;
        }

        try {
            $userId = decrypt($encrypted);
        } catch (Throwable) {
            return null;
        }

        if (! is_string($userId) && ! is_int($userId)) {
            return null;
        }

        /** @var class-string<Authenticatable> $userModel */
        $userModel = Passkeys::userModel();

        return $userModel::find($userId);
    }
}
