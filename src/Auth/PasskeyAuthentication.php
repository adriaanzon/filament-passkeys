<?php

namespace AdriaanZon\FilamentPasskeys\Auth;

use AdriaanZon\FilamentPasskeys\Auth\Actions\DeletePasskeyAction;
use AdriaanZon\FilamentPasskeys\Auth\Actions\RenamePasskeyAction;
use AdriaanZon\FilamentPasskeys\FilamentPasskeysPlugin;
use AdriaanZon\FilamentPasskeys\Forms\Components\PasskeyChallenge;
use Closure;
use Filament\Auth\MultiFactor\Contracts\MultiFactorAuthenticationProvider;
use Filament\Facades\Filament;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\View;
use Filament\Support\Enums\Alignment;
use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Passkeys\Contracts\PasskeyUser;
use LogicException;

class PasskeyAuthentication implements MultiFactorAuthenticationProvider
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'passkey';
    }

    public function getLoginFormLabel(): string
    {
        try {
            $label = FilamentPasskeysPlugin::get()->getLoginFormLabel();
        } catch (\Throwable) {
            $label = null;
        }

        return $label ?? __('filament-passkeys::passkeys.provider.label');
    }

    public function isEnabled(Authenticatable $user): bool
    {
        if (! $user instanceof PasskeyUser) {
            throw new LogicException('The user model must implement the [' . PasskeyUser::class . '] interface to use passkey authentication.');
        }

        return $user->passkeys()->exists();
    }

    public function getManagementSchemaComponents(): array
    {
        $getPasskeys = function () {
            $user = Filament::auth()->user();

            return $user instanceof PasskeyUser
                ? $user->passkeys()->orderBy('created_at', 'desc')->get()
                : collect();
        };

        return [
            RepeatableEntry::make('passkeys')
                ->extraAttributes(['class' => 'fi-passkeys-table'])
                ->label(__('filament-passkeys::passkeys.management.heading'))
                ->afterLabel(function () use ($getPasskeys): Text {
                    $isEnabled = $getPasskeys()->isNotEmpty();

                    return Text::make($isEnabled
                        ? __('filament-passkeys::passkeys.management.enabled')
                        : __('filament-passkeys::passkeys.management.disabled'))
                        ->badge()
                        ->color($isEnabled ? 'success' : 'gray');
                })
                ->aboveContent(__('filament-passkeys::passkeys.management.description'))
                ->belowContent(View::make('filament-passkeys::add-passkey-button'))
                ->constantState($getPasskeys)
                ->placeholder(__('filament-passkeys::passkeys.management.empty'))
                ->table([
                    TableColumn::make(__('filament-passkeys::passkeys.management.columns.name')),
                    TableColumn::make(__('filament-passkeys::passkeys.management.columns.last_used')),
                    TableColumn::make(__('filament-passkeys::passkeys.management.columns.actions'))
                        ->hiddenHeaderLabel()
                        ->alignment(Alignment::End),
                ])
                ->schema([
                    TextEntry::make('name')->hiddenLabel(),
                    TextEntry::make('last_used_at')
                        ->hiddenLabel()
                        ->since()
                        ->placeholder(__('filament-passkeys::passkeys.management.never_used')),
                    Actions::make([
                        DeletePasskeyAction::make(),
                        RenamePasskeyAction::make(),
                    ])->alignEnd(),
                ]),
        ];
    }

    public function getChallengeFormComponents(Authenticatable $user): array
    {
        return [
            PasskeyChallenge::make('credential')
                ->hiddenLabel()
                ->required()
                ->rule(function () use ($user): Closure {
                    return function (string $attribute, $value, Closure $fail) use ($user): void {
                        $key = 'filament-passkeys.mfa-verified.' . $user->getAuthIdentifier();

                        if (session()->pull($key) !== true) {
                            $fail(__('filament-passkeys::passkeys.challenge.failed'));
                        }
                    };
                }),
        ];
    }
}
