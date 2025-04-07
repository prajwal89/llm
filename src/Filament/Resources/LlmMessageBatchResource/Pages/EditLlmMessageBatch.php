<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Filament\Resources\LlmMessageBatchResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Prajwal89\Llm\Filament\Resources\LlmMessageBatchResource;

class EditLlmMessageBatch extends EditRecord
{
    protected static string $resource = LlmMessageBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
