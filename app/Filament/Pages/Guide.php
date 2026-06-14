<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class Guide extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|UnitEnum|null $navigationGroup = 'Hướng dẫn';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'Hướng dẫn sử dụng';

    protected static ?string $title = 'Hướng dẫn sử dụng';

    protected static ?string $slug = 'guide';

    protected string $view = 'filament.pages.guide';
}
