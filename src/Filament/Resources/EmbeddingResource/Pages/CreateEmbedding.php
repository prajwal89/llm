<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Filament\Resources\EmbeddingResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Prajwal89\Llm\Filament\Resources\EmbeddingResource;

class CreateEmbedding extends CreateRecord
{
    protected static string $resource = EmbeddingResource::class;
}
