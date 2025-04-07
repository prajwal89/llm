<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Prajwal89\Llm\Enums\LlmResponseProcessStatus;
use Prajwal89\Llm\Filament\Resources\LlmResponseProcessRecordResource\Pages;
use Prajwal89\Llm\Filament\Resources\LlmResponseProcessRecordResource\Pages\ListLlmResponseProcessRecords;
use Prajwal89\Llm\Models\LlmResponseProcessRecord;

class LlmResponseProcessRecordResource extends Resource
{
    protected static ?string $model = LlmResponseProcessRecord::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationLabel = 'Process Records';

    protected static ?string $navigationGroup = 'LLM';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('processable_id')->searchable()->sortable(),
                TextColumn::make('processable_type'),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')->datetime()->sortable(),
                TextColumn::make('error'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(LlmResponseProcessStatus::class),
                DateRangeFilter::make('created_at'),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLlmResponseProcessRecords::route('/'),
            // 'create' => Pages\CreateLlmResponseProcessRecord::route('/create'),
            // 'edit' => Pages\EditLlmResponseProcessRecord::route('/{record}/edit'),
        ];
    }
}
