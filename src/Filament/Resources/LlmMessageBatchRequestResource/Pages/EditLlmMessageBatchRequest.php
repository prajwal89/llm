<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Filament\Resources\LlmMessageBatchRequestResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Prajwal89\Llm\Filament\Resources\LlmMessageBatchRequestResource;

class EditLlmMessageBatchRequest extends EditRecord
{
    protected static string $resource = LlmMessageBatchRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
