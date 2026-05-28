<?php

declare(strict_types=1);

namespace AdriaanZon\FilamentPasskeys\Http\Controllers;

use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Passkeys\Actions\GenerateVerificationOptions;
use Laravel\Passkeys\Actions\VerifyPasskey;
use Laravel\Passkeys\Exceptions\InvalidPasskeyException;
use Laravel\Passkeys\Http\Requests\PasskeyVerificationRequest;
use Laravel\Passkeys\Passkeys;
use Laravel\Passkeys\Support\WebAuthn;

class PasskeyLoginController extends Controller
{
    public function index(Request $request, GenerateVerificationOptions $generate): JsonResponse
    {
        $options = $generate();

        $serialized = WebAuthn::toJson($options);

        $request->session()->put('passkey.verification_options', $serialized);

        return response()->json([
            'options' => WebAuthn::toBrowserArray($options),
        ]);
    }

    public function store(
        PasskeyVerificationRequest $request,
        VerifyPasskey $verify,
    ): JsonResponse {
        $passkey = $verify(
            $request->credential(),
            $request->verificationOptions(),
        );

        if (! Passkeys::allowsLogin($request, $passkey)) {
            throw InvalidPasskeyException::make('filament-passkeys::passkeys.login.failed');
        }

        $user = $passkey->user;

        $panel = Filament::getCurrentOrDefaultPanel();

        if ($user instanceof FilamentUser && ($panel === null || ! $user->canAccessPanel($panel))) {
            throw InvalidPasskeyException::make('filament-passkeys::passkeys.login.failed');
        }

        Filament::auth()->login($user);

        $request->session()->regenerate();

        return response()->json([
            'redirect' => redirect()->intended(Filament::getUrl())->getTargetUrl(),
        ]);
    }
}
