<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Strategies\Chat;

use Illuminate\Support\Facades\Http;
use Prajwal89\Llm\Concerns\ChatProvider;
use Prajwal89\Llm\Dtos\LlmResponseDto;

class Meta implements ChatProvider
{
    public function __construct(
        public string $modelName,
        public int $maxTokens,
        public ?string $systemPrompt,
        public array $messages,
        public array $additionalParams = [],
    ) {}

    // todo max tokens support
    // todo different endpoint support like @CF, my local tunnel
    // todo system prompt should be optional
    public function makeRequest(): LlmResponseDto
    {
        $response = Http::timeout(300)
            ->post(config('services.ollama.endpoint') . '/chat', [
                'model' => $this->modelName,
                'stream' => false,
                'keep_alive' => 120,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->systemPrompt,
                    ],
                    ...$this->messages,
                ],
                ...$this->additionalParams,
            ]);

        // dd($response->json());
        return LlmResponseDto::fromMetaResponse($response);
    }
}
