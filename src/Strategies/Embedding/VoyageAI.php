<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Strategies\Embedding;

use Illuminate\Support\Facades\Http;
use Prajwal89\Llm\Dtos\EmbeddingResponseDto;

class VoyageAI
{
    public function __construct(
        public string $modelName,
        public string $input,
        public array $additionalParams = []
    ) {}

    public function makeRequest(): EmbeddingResponseDto
    {
        $response = Http::timeout(30)
            ->retry(5, 2000)
            ->withToken(config('services.voyage_ai.api_key'))
            ->withHeader('content-type', 'application/json')
            ->post(config('services.voyage_ai.endpoint'), [
                'input' => $this->input,
                'model' => $this->modelName,
                ...$this->additionalParams,
                // https://docs.voyageai.com/docs/embeddings#python-api
                // 'input_type' => "document"
            ])
            ->throw();

        return EmbeddingResponseDto::fromVoyageAiResponse($response);
    }
}
