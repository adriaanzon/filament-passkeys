<?php

declare(strict_types=1);

use AdriaanZon\FilamentPasskeys\Forms\Components\PasskeyChallenge;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;
use Livewire\Component;

function renderPasskeyChallenge(): string
{
    View::share('errors', new ViewErrorBag);

    $livewire = new class extends Component implements HasSchemas
    {
        use InteractsWithSchemas;

        public array $data = [];

        public function render(): string
        {
            return '<div></div>';
        }
    };

    $schema = Schema::make($livewire)
        ->statePath('data')
        ->components([PasskeyChallenge::make('credential')]);

    return $schema->getComponents()[0]->toHtml();
}

it('interpolates the panel authenticate label into the callout description', function (string $locale, string $expected) {
    app()->setLocale($locale);

    expect(renderPasskeyChallenge())->toContain(e($expected));
})->with([
    ['en', 'Press "Confirm sign in" to choose a passkey from your device or password manager.'],
    ['nl', 'Druk op "Verifiëren" om een passkey te kiezen op je apparaat of wachtwoordmanager.'],
    ['vi', 'Nhấn "Xác nhận đăng nhập" để chọn passkey từ thiết bị hoặc trình quản lý mật khẩu của bạn.'],
]);
