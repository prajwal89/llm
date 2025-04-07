<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Filament\Resources\LlmMessageBatchResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Prajwal89\Llm\Filament\Resources\LlmMessageBatchResource;
use Prajwal89\Llm\Filament\Resources\LlmMessageBatchResource\Widgets\LlmMessageBatchTrendChart;

class ListLlmMessageBatches extends ListRecords
{
    protected static string $resource = LlmMessageBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            LlmMessageBatchTrendChart::class,
        ];
    }
}
