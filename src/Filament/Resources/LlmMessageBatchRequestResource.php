<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Prajwal89\Llm\BatchLlmRequests;
use Prajwal89\Llm\Enums\BatchRequestStatus;
use Prajwal89\Llm\Enums\LlmModelEnum;
use Prajwal89\Llm\Filament\Resources\LlmMessageBatchRequestResource\Pages;
use Prajwal89\Llm\Filament\Resources\LlmMessageBatchRequestResource\Pages\ListLlmMessageBatchRequests;
use Prajwal89\Llm\Filament\Resources\LlmMessageBatchRequestResource\Widgets\LlmMessageBatchRequestTrendChart;
use Prajwal89\Llm\Models\LlmMessageBatchRequest;

class LlmMessageBatchRequestResource extends Resource
{
    protected static ?string $model = LlmMessageBatchRequest::class;

    // protected static ?string $navigationIcon = 'heroicon-o-globe-americas';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = 'Batch Requests';

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
                ...self::commonColumns(),
            ])
            ->filters([
                ...self::commonFilters(),
            ])
            ->actions([
                ...self::commonActions(),
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

    public static function getWidgets(): array
    {
        return [
            LlmMessageBatchRequestTrendChart::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLlmMessageBatchRequests::route('/'),
            // 'create' => Pages\CreateLlmMessageBatchRequest::route('/create'),
            // 'edit' => Pages\EditLlmMessageBatchRequest::route('/{record}/edit'),
        ];
    }

    public static function commonFilters(): array
    {
        return [
            SelectFilter::make('status')
                ->options(BatchRequestStatus::class),
            // ->default(BatchRequestStatus::PROCESSING->value)
            SelectFilter::make('model_name')
                ->options(LlmModelEnum::class),

            DateRangeFilter::make('created_at'),

            // ! this is custom logic violets module principles
            Filter::make('Pending Tool Imports')
                ->modifyQueryUsing(function ($query): void {
                    $query->whereNotIn('id', function ($query): void {
                        $query->select('processable_id')
                            ->from('llm_response_process_record')
                            ->where('processable_type', LlmMessageBatchRequest::class);
                    });
                }),
        ];
    }

    public static function commonActions(): array
    {
        return [
            // Tables\Actions\EditAction::make(),
            DeleteAction::make(),
            Action::make('check_status')
                ->label('Check Status')
                ->slideOver()
                ->modalContent(function (LlmMessageBatchRequest $record): HtmlString {
                    $responseCollection = BatchLlmRequests::getBatchResults($record->batch_id);

                    $filtered = $responseCollection->where('customId', $record->custom_id)->first();

                    // dd($filtered);

                    $prettyJson = json_encode($filtered->toArray(), JSON_PRETTY_PRINT);

                    return new HtmlString(
                        sprintf(
                            '<pre class="p-4 overflow-auto bg-gray-100 rounded-lg"><code>%s</code></pre>',
                            htmlspecialchars($prettyJson)
                        )
                    );
                }),
        ];
    }

    public static function commonColumns(): array
    {
        return [
            TextColumn::make('custom_id')->searchable(),
            TextColumn::make('batch_id')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('status')->badge(),

            TextColumn::make('created_at')
                // ->date()
                ->sortable()
                ->datetime(),

            TextColumn::make('model_name')
                ->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('response')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('system_prompt')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('user_prompt')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }
}
