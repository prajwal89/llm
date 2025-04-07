<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Filament\Resources\LlmMessageBatchRequestResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Prajwal89\Llm\Filament\Resources\LlmMessageBatchRequestResource;
use Prajwal89\Llm\Filament\Resources\LlmMessageBatchRequestResource\Widgets\LlmMessageBatchRequestTrendChart;

class ListLlmMessageBatchRequests extends ListRecords
{
    protected static string $resource = LlmMessageBatchRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            LlmMessageBatchRequestTrendChart::class,
        ];
    }
}
