<?php

declare(strict_types=1);

namespace Prajwal89\Llm;

use Filament\Contracts\Plugin;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Prajwal89\Llm\Filament\Resources\EmbeddingResource;
use Prajwal89\Llm\Filament\Resources\LlmMessageBatchRequestResource;
use Prajwal89\Llm\Filament\Resources\LlmMessageBatchResource;
use Prajwal89\Llm\Filament\Resources\LlmResponseProcessRecordResource;
use Prajwal89\Llm\Filament\Resources\LlmUsageResource;

class LlmFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'llm';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                LlmUsageResource::class,
                EmbeddingResource::class,
                LlmMessageBatchResource::class,
                LlmMessageBatchRequestResource::class,
                LlmResponseProcessRecordResource::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('LLM')
                    ->icon('heroicon-o-cube')
                    ->collapsed(),
            ])
            ->pages([
                // Settings::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
