<?php

namespace AdriaanZon\FilamentPasskeys\Http\Controllers;

use Filament\Facades\Filament;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Passkeys\Actions\GenerateRegistrationOptions;
use Laravel\Passkeys\Actions\StorePasskey;
use Laravel\Passkeys\Http\Requests\PasskeyRegistrationRequest;
use Laravel\Passkeys\Support\WebAuthn;

class PasskeyRegistrationController extends Controller
{
    public function index(Request $request, GenerateRegistrationOptions $generate): JsonResponse
    {
        $user = Filament::auth()->user();

        $options = $generate($user);

        $serialized = WebAuthn::toJson($options);

        $request->session()->put('passkey.registration_options', $serialized);

        return response()->json([
            'options' => json_decode($serialized, true),
        ]);
    }

    public function store(
        PasskeyRegistrationRequest $request,
        StorePasskey $storePasskey,
    ): JsonResponse {
        $user = Filament::auth()->user();

        $passkey = $storePasskey(
            $user,
            $request->string('name')->toString(),
            $request->credential(),
            $request->registrationOptions()
        );

        $passkey->update([
            'name' => $passkey->authenticator
                ?? __('filament-passkeys::passkeys.management.default_name'),
        ]);

        return response()->json([
            'id' => $passkey->getKey(),
            'name' => $passkey->name,
        ]);
    }
}
