<?php

namespace App\Filament\Support;

use App\Models\OrgUnit;
use App\Models\Position;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class OrgOptionForms
{
    /**
     * Builds a flat id => label map of org units in tree order, with each
     * label prefixed by a hierarchical number (1, 1.1, 1.2, ...) and
     * indented according to its depth, e.g. "— 1.1. Ban Giám sát".
     *
     * @return array<int, string>
     */
    public static function orgUnitOptions(): array
    {
        $unitsByParent = OrgUnit::orderBy('sort_order')->orderBy('id')->get()->groupBy('parent_id');

        $options = [];

        $walk = function (int|string $parentId, string $prefix, int $depth) use (&$walk, &$options, $unitsByParent) {
            foreach ($unitsByParent->get($parentId, collect()) as $index => $unit) {
                $number = $prefix === '' ? (string) ($index + 1) : "{$prefix}.".($index + 1);
                $options[$unit->id] = str_repeat('— ', $depth)."{$number}. {$unit->name}";
                $walk($unit->id, $number, $depth + 1);
            }
        };

        $walk('', '', 0);

        return $options;
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    public static function orgUnit(): array
    {
        return [
            TextInput::make('name')
                ->label('Tên phòng ban')
                ->required()
                ->maxLength(255),
            Select::make('parent_id')
                ->label('Thuộc đơn vị')
                ->options(fn () => self::orgUnitOptions())
                ->searchable()
                ->preload()
                ->native(false),
        ];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    public static function position(): array
    {
        return [
            TextInput::make('name')
                ->label('Tên vị trí')
                ->required()
                ->maxLength(255),
            Select::make('org_unit_id')
                ->label('Phòng ban')
                ->options(fn () => self::orgUnitOptions())
                ->searchable()
                ->preload()
                ->native(false),
            Select::make('parent_id')
                ->label('Thuộc vị trí')
                ->options(fn () => Position::pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->native(false),
        ];
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    public static function user(): array
    {
        return [
            TextInput::make('name')
                ->label('Họ tên')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->unique('users', 'email')
                ->maxLength(255),
            TextInput::make('password')
                ->label('Mật khẩu')
                ->password()
                ->revealable()
                ->required()
                ->minLength(6),
            Select::make('org_unit_id')
                ->label('Phòng ban')
                ->options(fn () => self::orgUnitOptions())
                ->searchable()
                ->preload()
                ->native(false),
            Select::make('position_id')
                ->label('Vị trí')
                ->options(fn () => Position::pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->native(false),
        ];
    }
}
