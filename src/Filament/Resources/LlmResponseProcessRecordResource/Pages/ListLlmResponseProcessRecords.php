<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Filament\Resources\LlmResponseProcessRecordResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Prajwal89\Llm\Filament\Resources\LlmResponseProcessRecordResource;

class ListLlmResponseProcessRecords extends ListRecords
{
    protected static string $resource = LlmResponseProcessRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
