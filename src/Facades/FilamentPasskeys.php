<?php

namespace AdriaanZon\FilamentPasskeys\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \AdriaanZon\FilamentPasskeys\FilamentPasskeys
 */
class FilamentPasskeys extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \AdriaanZon\FilamentPasskeys\FilamentPasskeys::class;
    }
}
