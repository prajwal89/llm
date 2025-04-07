<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Filament\Resources\LlmResponseProcessRecordResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Prajwal89\Llm\Filament\Resources\LlmResponseProcessRecordResource;

class EditLlmResponseProcessRecord extends EditRecord
{
    protected static string $resource = LlmResponseProcessRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
