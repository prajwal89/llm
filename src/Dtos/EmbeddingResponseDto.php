<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Dtos;

use Illuminate\Http\Client\Response;
use Prajwal89\Llm\Models\Embedding;

class EmbeddingResponseDto
{
    public function __construct(
        public array $embeddings,
        public int $totalTokens
    ) {}

    public static function fromOpenAiResponse(Response $response): self
    {
        $data = $response->json();

        return new self(
            embeddings: $data['data'][0]['embedding'],
            totalTokens: $data['usage']['total_tokens']
        );
    }

    public static function fromVoyageAiResponse(Response $response): self
    {
        $data = $response->json();

        return new self(
            embeddings: $data['data'][0]['embedding'],
            totalTokens: $data['usage']['total_tokens']
        );
    }

    public static function fromDb(Embedding $embedding): self
    {
        return new self(
            embeddings: $embedding->vectors,
            totalTokens: $embedding->total_tokens
        );
    }
}
