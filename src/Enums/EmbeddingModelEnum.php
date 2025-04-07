<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Enums;

enum EmbeddingModelEnum: string
{
    case ADA_002 = 'text-embedding-ada-002';
    case VOYAGE_AI_LARGE_2_INSTRUCT = 'voyage-large-2-instruct';
    case VOYAGE_AI_VOYAGE_3 = 'voyage-3';

    public function llmFamilyName(): EmbeddingModelFamilyEnum
    {
        return match ($this) {
            self::ADA_002 => EmbeddingModelFamilyEnum::OPEN_AI,

            self::VOYAGE_AI_VOYAGE_3,
            self::VOYAGE_AI_LARGE_2_INSTRUCT, => EmbeddingModelFamilyEnum::VOYAGE_AI,
        };
    }

    public function getMinSemanticScore(): float
    {
        return match ($this) {
            self::ADA_002 => 0.85,
            self::VOYAGE_AI_VOYAGE_3 => 0.50,
            self::VOYAGE_AI_LARGE_2_INSTRUCT, => 0.50,
        };
    }

    public function dimensions(): float
    {
        return match ($this) {
            self::ADA_002 => 1536,
            self::VOYAGE_AI_VOYAGE_3 => 1024,
            self::VOYAGE_AI_LARGE_2_INSTRUCT, => 1024,
        };
    }

    /**
     * @deprecated This function is deprecated.
     */
    public function minimumScores()
    {
        return match ($this) {
            self::VOYAGE_AI_VOYAGE_3 => [
                'popular_search' => 0.50,
            ],
        };
    }
}
