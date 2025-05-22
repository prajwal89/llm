<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Strategies\Chat;

use Illuminate\Support\Collection;
use Prajwal89\Llm\Concerns\ChatProvider;
use Prajwal89\Llm\Dtos\LlmResponseDto;
use Prajwal89\Llm\Dtos\MessageDto;
use Prajwal89\Llm\HttpClients\OpenAiClient;

class OpenAI implements ChatProvider
{
    public function __construct(
        public string $modelName,
        public ?int $maxTokens,
        public ?string $systemPrompt,
        /**
         * @var Collection<MessageDto>
         */
        public Collection $messages,
        public array $additionalParams = [],
    ) {}

    public function makeRequest(): LlmResponseDto
    {
        $openAiMessages = $this->messages->map(function (MessageDto $message): array {
            return $message->toOpenAi();
        })->toArray();

        if ($this->systemPrompt) {
            array_unshift($openAiMessages, (new MessageDto('system', $this->systemPrompt))->toOpenAi());
        }

        $response = OpenAiClient::make()
            ->post('/chat/completions', [
                'model' => $this->modelName,
                'messages' => $openAiMessages,
                ...$this->maxTokens ? ['max_tokens' => $this->maxTokens] : [],
                ...$this->additionalParams
            ])
            ->throw();

        return LlmResponseDto::fromOpenAiResponse($response);
    }
}
