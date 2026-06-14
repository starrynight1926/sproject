<?php

namespace App\Filament\Resources\ExcelFormulas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExcelFormulasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyableState(fn ($record) => $record->syntax)
                    ->copyMessage('Đã copy công thức!')
                    ->tooltip('Click để copy công thức'),
                TextColumn::make('syntax')
                    ->searchable()
                    ->limit(50)
                    ->copyable()
                    ->copyMessage('Đã copy!')
                    ->tooltip(fn ($record) => $record->syntax),
                TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Tìm kiếm' => 'info',
                        'Thống kê' => 'success',
                        'Logic' => 'warning',
                        'Văn bản' => 'primary',
                        'Ngày giờ' => 'danger',
                        'Toán học' => 'gray',
                        'Google Sheets' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('example')
                    ->searchable()
                    ->limit(40)
                    ->copyable()
                    ->copyMessage('Đã copy example!')
                    ->toggleable(),
                TextColumn::make('tags')
                    ->badge()
                    ->separator(',')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'Tìm kiếm' => 'Tìm kiếm',
                        'Thống kê' => 'Thống kê',
                        'Logic' => 'Logic',
                        'Văn bản' => 'Văn bản',
                        'Ngày giờ' => 'Ngày giờ',
                        'Toán học' => 'Toán học',
                        'Google Sheets' => 'Google Sheets',
                    ]),
            ])
            ->defaultSort('name')
            ->recordActions([
                ViewAction::make()
                    ->modalHeading(fn ($record) => $record->name)
                    ->form([
                        TextInput::make('syntax')
                            ->disabled()
                            ->label('Cú pháp')
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->disabled()
                            ->label('Mô tả')
                            ->columnSpanFull()
                            ->rows(2),
                        TextInput::make('example')
                            ->disabled()
                            ->label('Ví dụ mẫu')
                            ->columnSpanFull(),
                        Textarea::make('note')
                            ->disabled()
                            ->label('Ghi chú')
                            ->columnSpanFull()
                            ->rows(2),
                        Textarea::make('test_formula')
                            ->label('Kiểm tra công thức')
                            ->placeholder('Dán công thức của bạn vào đây để kiểm tra...')
                            ->columnSpanFull()
                            ->rows(2)
                            ->live()
                            ->dehydrated(false)
                            ->helperText(fn ($state, $record) => self::validateFormula($state, $record)),
                    ]),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function validateFormula(?string $input, $record): string
    {
        if (blank($input)) {
            return 'Nhập công thức để kiểm tra xem có khớp với cú pháp không.';
        }

        $formulaName = strtoupper($record->name);
        $inputUpper = strtoupper(trim($input));

        if (! str_starts_with($inputUpper, '=')) {
            return '❌ Công thức phải bắt đầu bằng dấu =';
        }

        $inputWithoutEquals = substr($inputUpper, 1);

        $names = array_map('trim', explode('/', $formulaName));
        $matched = false;
        $matchedName = $names[0];
        foreach ($names as $name) {
            $nameUpper = strtoupper(trim($name));
            if (str_starts_with($inputWithoutEquals, $nameUpper . '(') || $inputWithoutEquals === $nameUpper) {
                $matched = true;
                $matchedName = $nameUpper;
                break;
            }
        }

        if (! $matched) {
            return '❌ Không khớp. Công thức này nên bắt đầu bằng =' . $names[0] . '(...)';
        }

        $openParens = substr_count($input, '(');
        $closeParens = substr_count($input, ')');
        if ($openParens !== $closeParens) {
            return '⚠️ Đúng hàm ' . $matchedName . ' nhưng số dấu ngoặc không khớp (' . $openParens . ' mở, ' . $closeParens . ' đóng)';
        }

        return '✅ Đúng! Công thức khớp với hàm ' . $matchedName;
    }
}
