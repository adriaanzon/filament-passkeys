<?php

namespace AdriaanZon\FilamentPasskeys\Commands;

use Illuminate\Console\Command;

class FilamentPasskeysCommand extends Command
{
    public $signature = 'filament-passkeys';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
