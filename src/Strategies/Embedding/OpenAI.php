<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Strategies\Embedding;

use Illuminate\Support\Facades\Http;
use Prajwal89\Llm\Dtos\EmbeddingResponseDto;

class OpenAI
{
    public function __construct(
        public string $modelName,
        public string $input,
        public array $additionalParams = []
    ) {}

    public function makeRequest(): EmbeddingResponseDto
    {
        $response = Http::timeout(30)
            ->withToken(config('services.open_ai.api_key'))
            ->post('https://api.openai.com/v1/embeddings', [
                'input' => $this->input,
                'model' => $this->modelName,
                ...$this->additionalParams,
            ])
            ->throw();

        return EmbeddingResponseDto::fromOpenAiResponse($response);
    }
}
