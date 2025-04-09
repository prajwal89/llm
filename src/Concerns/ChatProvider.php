<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Concerns;

use Prajwal89\Llm\Dtos\LlmResponseDto;

interface ChatProvider
{
    public function makeRequest(): LlmResponseDto;
}
