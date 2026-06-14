<?php

namespace App\Filament\Support;

use Filament\Support\RawJs;

class MoneyInput
{
    public static function mask(): RawJs
    {
        return RawJs::make('$money($input, \',\', \'.\', 0)');
    }

    public static function stripCharacters(): string
    {
        return '.';
    }
}
