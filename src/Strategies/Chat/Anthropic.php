<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Strategies\Chat;

use Illuminate\Support\Collection;
use Prajwal89\Llm\Concerns\ChatProvider;
use Prajwal89\Llm\Dtos\LlmResponseDto;
use Prajwal89\Llm\Dtos\MessageDto;
use Prajwal89\Llm\HttpClients\AnthropicClient;

class Anthropic implements ChatProvider
{
    public function __construct(
        public string $modelName,
        /**
         * @var Collection<MessageDto>
         */
        public Collection $messages,
        public ?string $systemPrompt = null,
        public int $maxTokens = 4000,
        // todo support for extra options
    ) {}

    public function makeRequest(): LlmResponseDto
    {
        $systemPrompt = [];

        if ($this->systemPrompt !== null && $this->systemPrompt !== '' && $this->systemPrompt !== '0') {
            $systemPrompt['system'] = $this->systemPrompt;
        }

        $response = AnthropicClient::make()
            ->post('/messages', [
                'model' => $this->modelName,
                'stream' => false,
                'max_tokens' => $this->maxTokens,
                ...$systemPrompt,
                'messages' => $this->messages->map(function (MessageDto $message): array {
                    return $message->toAnthropic();
                })->toArray(),
            ])
            ->throw();

        return LlmResponseDto::fromAnthropicResponse($response);
    }
}
