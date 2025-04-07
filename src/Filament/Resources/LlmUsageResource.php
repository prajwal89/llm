<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Filament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Prajwal89\Llm\Enums\LlmModelEnum;
use Prajwal89\Llm\Filament\Resources\LlmUsageResource\Pages;
use Prajwal89\Llm\Filament\Resources\LlmUsageResource\Pages\ListLlmUsages;
use Prajwal89\Llm\Filament\Resources\LlmUsageResource\Widgets\LlmUsageCostTable;
use Prajwal89\Llm\Filament\Resources\LlmUsageResource\Widgets\LlmUsageTrend;
use Prajwal89\Llm\Models\LlmUsage;

class LlmUsageResource extends Resource
{
    protected static ?string $model = LlmUsage::class;

    // protected static ?string $navigationIcon = 'heroicon-o-cube-transparent';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'Usage';

    protected static ?string $navigationGroup = 'LLM';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // TextInput::make('system_prompt')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('system_prompt')
                    ->words(4)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('user_prompt')
                    ->words(2)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('response')
                    ->words(2)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('responseable_type')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('responseable_id')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('model_name')->sortable(),
                TextColumn::make('input_tokens')->sortable(),
                TextColumn::make('output_tokens')->sortable(),
                TextColumn::make('time_taken_ms')->sortable(),
            ])
            ->filters([
                SelectFilter::make('model_name')
                    ->options(LlmModelEnum::class),
                DateRangeFilter::make('created_at'),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Preview Record')
                    ->modalContent(function ($record): HtmlString {
                        return new HtmlString(
                            sprintf(
                                '<div class="space-y-4">
                                    <div>
                                        <h4 class="text-gray-700">System Prompt</h4>
                                        <textarea class="w-full p-4 whitespace-pre-wrap bg-gray-100 rounded-lg">%s</textarea>
                                    </div>
                                    <div>
                                        <h4 class="text-gray-700">User Prompt</h4>
                                        <textarea class="w-full p-4 whitespace-pre-wrap bg-gray-100 rounded-lg">%s</textarea>
                                    </div>
                                    <div>
                                        <h4 class="text-gray-700">Response</h4>
                                        <textarea class="w-full p-4 whitespace-pre-wrap bg-gray-100 rounded-lg">%s</textarea>
                                    </div>
                                </div>',
                                htmlspecialchars($record->system_prompt ?? 'N/A'),
                                htmlspecialchars(json_encode(json_decode($record->user_prompt ?? '{}'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?? 'N/A'),
                                htmlspecialchars(json_encode(json_decode($record->response ?? '{}'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?? 'N/A')
                            )
                        );
                    })

                    ->color('primary'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->recordAction('preview')
            ->defaultSort('created_at', 'desc');
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
            LlmUsageTrend::class,
            LlmUsageCostTable::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLlmUsages::route('/'),
            // 'create' => Pages\CreateLlmUsage::route('/create'),
            // 'edit' => Pages\EditLlmUsage::route('/{record}/edit'),
        ];
    }
}
