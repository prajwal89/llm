<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Filament\Resources\EmbeddingResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Prajwal89\Llm\Filament\Resources\EmbeddingResource;
use Prajwal89\Llm\Filament\Resources\EmbeddingResource\Widgets\EmbeddingTrendChart;

class ListEmbeddings extends ListRecords
{
    protected static string $resource = EmbeddingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            EmbeddingTrendChart::class,
        ];
    }
}
