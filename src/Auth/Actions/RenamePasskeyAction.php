<?php

namespace AdriaanZon\FilamentPasskeys\Auth\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Laravel\Passkeys\Passkey;

class RenamePasskeyAction
{
    public static function make(): Action
    {
        return Action::make('rename')
            ->label(__('filament-passkeys::passkeys.management.rename'))
            ->icon(Heroicon::PencilSquare)
            ->iconButton()
            ->color('gray')
            ->modalIcon(Heroicon::OutlinedPencilSquare)
            ->modalHeading(__('filament-passkeys::passkeys.management.rename'))
            ->modalWidth(Width::Medium)
            ->fillForm(fn (Passkey $record): array => ['name' => $record->name])
            ->schema([
                TextInput::make('name')
                    ->label(__('filament-passkeys::passkeys.register.name_label'))
                    ->required()
                    ->maxLength(255),
            ])
            ->action(function (Passkey $record, array $data): void {
                $record->update(['name' => $data['name']]);

                Notification::make()
                    ->title(__('filament-passkeys::passkeys.management.renamed'))
                    ->success()
                    ->send();
            })
            ->rateLimit(5);
    }
}
