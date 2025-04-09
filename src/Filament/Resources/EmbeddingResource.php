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
use Prajwal89\Llm\Filament\Resources\EmbeddingResource\Pages;
use Prajwal89\Llm\Filament\Resources\EmbeddingResource\Pages\ListEmbeddings;
use Prajwal89\Llm\Filament\Resources\EmbeddingResource\Widgets\EmbeddingTrendChart;
use Prajwal89\Llm\Models\Embedding;

class EmbeddingResource extends Resource
{
    protected static ?string $model = Embedding::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Embeddings';

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
                TextColumn::make('input_text')->words(4)->searchable(),
                TextColumn::make('use_case'),
                TextColumn::make('model_name'),
                TextColumn::make('embedable_type'),
                TextColumn::make('embedable_id'),
                TextColumn::make('total_tokens')->sortable(),
            ])
            ->filters([
                SelectFilter::make('use_case')
                    ->options(function () {
                        return Embedding::query()
                            ->distinct('use_case')
                            ->get()
                            ->mapWithKeys(function ($use_case) {
                                return [$use_case => $use_case];
                            });
                    }),
                SelectFilter::make('model_name')
                    ->options(function () {
                        return Embedding::query()
                            ->distinct('model_name')
                            ->pluck('model_name')
                            ->mapWithKeys(function ($model_name) {
                                return [$model_name => $model_name];
                            });
                    }),
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
            ->defaultSort('created_at', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            EmbeddingTrendChart::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmbeddings::route('/'),
            // 'create' => Pages\CreateEmbedding::route('/create'),
            // 'edit' => Pages\EditEmbedding::route('/{record}/edit'),
        ];
    }
}
