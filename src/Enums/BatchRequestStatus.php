<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum BatchRequestStatus: string implements HasColor, HasIcon, HasLabel
{
    case PROCESSING = 'processing';
    case SUCCEEDED = 'succeeded';
    case ERRORED = 'errored';
    case CANCELED = 'canceled';
    case EXPIRED = 'expired';

    public static function fromAnthropic($status): self
    {
        return self::from($status);
    }

    public static function fromOpenAi($status): self
    {
        // is this good way to handle this
        // handel other statuses
        return match ($status) {
            200 => self::SUCCEEDED
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PROCESSING => 'Processing',
            self::SUCCEEDED => 'Succeeded',
            self::ERRORED => 'Errored',
            self::CANCELED => 'Canceled',
            self::EXPIRED => 'Expired',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::PROCESSING => 'info',
            self::SUCCEEDED => 'success',
            self::ERRORED => 'danger',
            self::CANCELED => 'gray',
            self::EXPIRED => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::PROCESSING => 'heroicon-o-clock',
            self::SUCCEEDED => 'heroicon-o-check-circle',
            self::ERRORED => 'heroicon-o-x-circle',
            self::CANCELED => 'heroicon-o-ban',
            self::EXPIRED => 'heroicon-o-calendar',
        };
    }
}
