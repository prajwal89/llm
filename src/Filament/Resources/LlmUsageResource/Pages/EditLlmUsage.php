<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Filament\Resources\LlmUsageResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Prajwal89\Llm\Filament\Resources\LlmUsageResource;

class EditLlmUsage extends EditRecord
{
    protected static string $resource = LlmUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
