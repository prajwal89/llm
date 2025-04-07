<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

/**
 * https://docs.anthropic.com/en/api/creating-message-batches
 */
enum BatchProcessingStatusEnum: string implements HasColor, HasIcon, HasLabel
{
    case IN_PROGRESS = 'in_progress';
    case CANCELING = 'canceling';
    case ENDED = 'ended'; // ?should i rename it to completed
    case FAILED = 'failed';

    public static function fromOpenAi(string $status): self
    {
        return match ($status) {
            // assuming that validating is in progress
            'validating' => self::IN_PROGRESS,
            'completed' => self::ENDED,
            'ended' => self::ENDED,
            'failed' => self::FAILED,
        };
    }

    public static function fromAnthropic(string $status): self
    {
        return match ($status) {
            'in_progress' => self::IN_PROGRESS,
            'canceling' => self::CANCELING,
            'ended' => self::ENDED,
            // ? how to know request has failed
            // 'ended' => self::FAILED,
        };
    }

    // Returns the color associated with each status
    public function getColor(): string
    {
        return match ($this) {
            self::IN_PROGRESS => 'info',
            self::CANCELING => 'danger',
            self::FAILED => 'danger',
            self::ENDED => 'success',
        };
    }

    // Returns the icon associated with each status
    public function getIcon(): ?string
    {
        return match ($this) {
            self::IN_PROGRESS => 'heroicon-o-clock',
            self::CANCELING => 'heroicon-o-refresh',
            self::ENDED => 'heroicon-o-check-circle',
            self::FAILED => 'heroicon-o-x-mark',
        };
    }

    // Returns the label associated with each status
    public function getLabel(): ?string
    {
        return match ($this) {
            self::IN_PROGRESS => 'In Progress',
            self::CANCELING => 'Canceling',
            self::ENDED => 'Ended',
            self::FAILED => 'Failed',
        };
    }
}
