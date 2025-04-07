<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Enums;

use Prajwal89\Llm\Strategies\Batches\Anthropic;
use Prajwal89\Llm\Strategies\Batches\OpenAI;

enum LlmProvider: string
{
    case OPEN_AI = 'open_ai';
    case ANTHROPIC = 'anthropic';
    case META = 'meta';
    case GOOGLE = 'google';
    case DEEPSEEK = 'deepseek';

    public function checkBatchStatus(string $batchId)
    {
        return match ($this) {
            self::ANTHROPIC => Anthropic::checkBatchStatus($batchId),
            // self::OPEN_AI => OpenAI::checkBatchStatus($batchId)
        };
    }

    public function getBatchResults(string $batchId)
    {
        return match ($this) {
            self::ANTHROPIC => Anthropic::getBatchResults($batchId),
            // self::OPEN_AI => OpenAI::checkBatchStatus($batchId)
        };
    }
}
