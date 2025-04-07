<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum LlmResponseProcessStatus: string implements HasColor, HasIcon, HasLabel
{
    case FAILED = 'failed';
    case SUCCEEDED = 'succeeded';

    public function getColor(): string
    {
        return match ($this) {
            self::FAILED => 'danger',
            self::SUCCEEDED => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::FAILED => 'heroicon-o-x-circle',
            self::SUCCEEDED => 'heroicon-o-check-circle',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::FAILED => 'Failed',
            self::SUCCEEDED => 'Succeeded',
        };
    }
}
