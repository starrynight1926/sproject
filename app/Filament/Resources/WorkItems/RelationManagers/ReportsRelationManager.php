<?php

namespace App\Filament\Resources\WorkItems\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ReportsRelationManager extends RelationManager
{
    protected static string $relationship = 'reports';

    protected static ?string $title = 'Báo cáo tiến độ';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')
                    ->default(fn () => Auth::id()),
                DatePicker::make('report_date')
                    ->label('Ngày báo cáo')
                    ->required()
                    ->default(now()),
                TextInput::make('progress_value')
                    ->label('Giá trị tiến độ')
                    ->numeric(),
                Textarea::make('note')
                    ->label('Ghi chú / Minh chứng')
                    ->rows(3)
                    ->columnSpanFull(),
                FileUpload::make('attachments')
                    ->label('File đính kèm')
                    ->multiple()
                    ->directory('work-item-reports')
                    ->dehydrated(false)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('report_date')
            ->columns([
                TextColumn::make('report_date')
                    ->label('Ngày')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('progress_value')
                    ->label('Giá trị')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Người báo cáo'),
                TextColumn::make('note')
                    ->label('Ghi chú')
                    ->limit(50),
                TextColumn::make('media_count')
                    ->label('File')
                    ->getStateUsing(fn ($record) => $record->getMedia('attachments')->count()),
            ])
            ->defaultSort('report_date', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->after(function ($record, array $data) {
                        self::attachFiles($record, $data);
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->after(function ($record, array $data) {
                        self::attachFiles($record, $data);
                    }),
                DeleteAction::make(),
            ]);
    }

    protected static function attachFiles(Model $record, array $data): void
    {
        foreach (($data['attachments'] ?? []) as $path) {
            if (! is_string($path)) {
                continue;
            }

            $fullPath = storage_path('app/public/'.$path);

            if (file_exists($fullPath)) {
                $record->addMedia($fullPath)
                    ->preservingOriginal()
                    ->toMediaCollection('attachments');
            }
        }
    }
}
