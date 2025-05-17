<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Strategies\Chat;

use Illuminate\Support\Collection;
use Prajwal89\Llm\Concerns\ChatProvider;
use Prajwal89\Llm\Dtos\LlmResponseDto;
use Prajwal89\Llm\Dtos\MessageDto;
use Prajwal89\Llm\HttpClients\GoogleClient;

class Google implements ChatProvider
{
    public function __construct(
        public string $modelName,
        public ?string $systemPrompt,
        /**
         * @var Collection<MessageDto>
         */
        public Collection $messages,
        public ?int $maxTokens,
        public array $additionalParams,
    ) {}

    public function makeRequest(): LlmResponseDto
    {
        $formattedMessages = $this->messages
            ->map(function (MessageDto $message): array {
                return [
                    'role' => $message->role,
                    'parts' => [
                        'text' => $message->text,
                    ],
                ];
            });

        $systemInstruction = [];
        if ($this->systemPrompt !== null && $this->systemPrompt !== '' && $this->systemPrompt !== '0') {
            $systemInstruction = [
                'systemInstruction' => [
                    'role' => 'user',
                    'parts' => [
                        [
                            'text' => $this->systemPrompt,
                        ],
                    ],
                ],
            ];
        }

        $response = GoogleClient::make()
            ->post("/models/{$this->modelName}:generateContent", [
                'contents' => [
                    ...$formattedMessages,
                ],
                ...$systemInstruction,
                ...$this->additionalParams,
            ])
            ->throw();

        return LlmResponseDto::fromGoogleResponse($response);
    }
}
