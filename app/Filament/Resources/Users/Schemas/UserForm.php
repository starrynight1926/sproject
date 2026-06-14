<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Họ tên')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('Mật khẩu')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn (?string $state) => Hash::make($state))
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->maxLength(255),
                Select::make('org_unit_id')
                    ->relationship('orgUnit', 'name')
                    ->label('Phòng ban')
                    ->searchable()
                    ->preload(),
                Select::make('position_id')
                    ->relationship('position', 'name')
                    ->label('Vị trí')
                    ->searchable()
                    ->preload(),
            ]);
    }
}
