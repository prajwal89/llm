<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Filament\Resources\EmbeddingResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Prajwal89\Llm\Filament\Resources\EmbeddingResource;

class EditEmbedding extends EditRecord
{
    protected static string $resource = EmbeddingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
