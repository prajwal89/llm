<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Strategies\Chat;

use Illuminate\Support\Facades\Http;
use Prajwal89\Llm\Concerns\ChatStrategyInterface;
use Prajwal89\Llm\Dtos\LlmResponseDto;
use Prajwal89\Llm\Enums\LlmModelEnum;

class Meta implements ChatStrategyInterface
{
    public function __construct(
        public LlmModelEnum $llmModel,
        public int $maxTokens,
        public ?string $systemPrompt,
        public array $messages
    ) {}

    // todo max tokens support
    // todo different endpoint support like @CF, my local tunnel
    // todo system prompt should be optional
    public function makeRequest(): LlmResponseDto
    {
        $response = Http::timeout(300)
            ->post(config('services.ollama.endpoint') . '/chat', [
                'model' => $this->llmModel->value,
                'stream' => false,
                'keep_alive' => 120,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->systemPrompt,
                    ],
                    ...$this->messages,
                ],
            ]);

        // dd($response->json());
        return LlmResponseDto::fromMetaResponse($response);
    }
}
