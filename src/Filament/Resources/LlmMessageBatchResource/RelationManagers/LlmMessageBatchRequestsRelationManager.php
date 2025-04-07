<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Filament\Resources\LlmMessageBatchResource\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Prajwal89\Llm\BatchLlmRequests;
use Prajwal89\Llm\Filament\Resources\LlmMessageBatchRequestResource;
use Prajwal89\Llm\Models\LlmMessageBatchRequest;

class LlmMessageBatchRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'llmMessageBatchRequests';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('custom_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('custom_id')
            ->columns([
                ...LlmMessageBatchRequestResource::commonColumns(),
            ])
            ->filters([
                ...LlmMessageBatchRequestResource::commonFilters(),
            ])
            ->actions([
                ...LlmMessageBatchRequestResource::commonActions(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            // ->actions([
            //     // Tables\Actions\EditAction::make(),
            //     Tables\Actions\DeleteAction::make(),
            //     Tables\Actions\Action::make('check_status')
            //         ->label('Check Status')
            //         ->slideOver()
            //         ->modalContent(function (LlmMessageBatchRequest $record) {
            //             $responseCollection = BatchLlmRequests::getBatchResults($record->batch_id);

            //             $prettyJson = json_encode($responseCollection->toArray(), JSON_PRETTY_PRINT);

            //             return new HtmlString(
            //                 sprintf(
            //                     '<pre class="p-4 overflow-auto bg-gray-100 rounded-lg"><code>%s</code></pre>',
            //                     htmlspecialchars($prettyJson)
            //                 )
            //             );
            //         }),
            // ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
