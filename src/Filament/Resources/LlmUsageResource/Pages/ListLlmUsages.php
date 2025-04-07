<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Filament\Resources\LlmUsageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Prajwal89\Llm\Filament\Resources\LlmUsageResource;
use Prajwal89\Llm\Filament\Resources\LlmUsageResource\Widgets\LlmUsageCostTable;
use Prajwal89\Llm\Filament\Resources\LlmUsageResource\Widgets\LlmUsageTrend;

class ListLlmUsages extends ListRecords
{
    protected static string $resource = LlmUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LlmUsageCostTable::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            LlmUsageTrend::class,
        ];
    }
}
