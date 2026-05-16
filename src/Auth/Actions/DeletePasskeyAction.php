<?php

declare(strict_types=1);

namespace AdriaanZon\FilamentPasskeys\Auth\Actions;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Laravel\Passkeys\Actions\DeletePasskey;
use Laravel\Passkeys\Contracts\PasskeyUser;
use Laravel\Passkeys\Passkey;
use LogicException;

class DeletePasskeyAction
{
    public static function make(): Action
    {
        return Action::make('delete')
            ->label(__('filament-passkeys::passkeys.management.delete'))
            ->icon(Heroicon::Trash)
            ->iconButton()
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading(__('filament-passkeys::passkeys.management.delete'))
            ->modalDescription(fn (Passkey $record): string => __('filament-passkeys::passkeys.management.delete_confirmation', ['name' => $record->name]))
            ->action(function (Passkey $record): void {
                $user = Filament::auth()->user();

                throw_unless($user instanceof PasskeyUser, new LogicException('The authenticated user must implement the [' . PasskeyUser::class . '] interface.'));

                app(DeletePasskey::class)($user, $record);

                Notification::make()
                    ->title(__('filament-passkeys::passkeys.management.deleted'))
                    ->success()
                    ->send();
            });
    }
}
