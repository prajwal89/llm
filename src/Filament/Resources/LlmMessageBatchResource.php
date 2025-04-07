<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Filament\Resources;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Prajwal89\Llm\BatchLlmRequests;
use Prajwal89\Llm\Enums\BatchProcessingStatusEnum;
use Prajwal89\Llm\Enums\LlmProvider;
use Prajwal89\Llm\Filament\Resources\LlmMessageBatchResource\Pages;
use Prajwal89\Llm\Filament\Resources\LlmMessageBatchResource\Pages\EditLlmMessageBatch;
use Prajwal89\Llm\Filament\Resources\LlmMessageBatchResource\Pages\ListLlmMessageBatches;
use Prajwal89\Llm\Filament\Resources\LlmMessageBatchResource\RelationManagers\LlmMessageBatchRequestsRelationManager;
use Prajwal89\Llm\Filament\Resources\LlmMessageBatchResource\Widgets\LlmMessageBatchTrendChart;
use Prajwal89\Llm\Models\LlmMessageBatch;

class LlmMessageBatchResource extends Resource
{
    protected static ?string $model = LlmMessageBatch::class;

    // protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'Batches';

    protected static ?string $navigationGroup = 'LLM';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('batch_id'),
                TextInput::make('processing_status'),
                DateTimePicker::make('created_at'),
            ])
            ->disabled();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('batch_id')->searchable(),
                TextColumn::make('processing_status')->badge(),
                TextColumn::make('llm_message_batch_requests_count')
                    ->label('Requests')
                    ->counts('llmMessageBatchRequests')
                    ->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('processing_status')
                    ->options(BatchProcessingStatusEnum::class),
                // ->default(BatchProcessingStatusEnum::IN_PROGRESS->value)
                SelectFilter::make('llm_provider')
                    ->options(LlmProvider::class),
                DateRangeFilter::make('created_at'),

            ])
            ->actions([
                // todo show request results also
                EditAction::make('edit'),
                Action::make('check_status')
                    ->label('Check Status')
                    ->slideOver()
                    ->modalContent(function (LlmMessageBatch $record): HtmlString {
                        $batchResponseDto = BatchLlmRequests::checkBatchStatus($record->batch_id);

                        // Convert response to pretty JSON
                        $prettyJson = json_encode($batchResponseDto->toArray(), JSON_PRETTY_PRINT);

                        // Return the content wrapped in a pre tag for proper formatting
                        return new HtmlString(
                            sprintf(
                                '<pre class="p-4 overflow-auto bg-gray-100 rounded-lg"><code>%s</code></pre>',
                                htmlspecialchars($prettyJson)
                            )
                        );
                    }),
                // ->modalSubmitAction(function (LlmMessageBatch $record) {
                //     //
                //     $response = BatchLlmRequests::getBatchResults($record->batch_id);

                //     // Convert response to pretty JSON
                //     $prettyJson = json_encode($response->json(), JSON_PRETTY_PRINT);

                //     // Return the content wrapped in a pre tag for proper formatting
                //     return new HtmlString(
                //         sprintf(
                //             '<pre class="p-4 overflow-auto bg-gray-100 rounded-lg"><code>%s</code></pre>',
                //             htmlspecialchars($prettyJson)
                //         )
                //     );
                // }),

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
            LlmMessageBatchRequestsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLlmMessageBatches::route('/'),
            // 'create' => Pages\CreateLlmMessageBatch::route('/create'),
            'edit' => EditLlmMessageBatch::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            LlmMessageBatchTrendChart::class,
        ];
    }
}
