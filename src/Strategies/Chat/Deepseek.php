<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Strategies\Chat;

use Illuminate\Support\Collection;
use Prajwal89\Llm\Concerns\ChatProvider;
use Prajwal89\Llm\Dtos\LlmResponseDto;
use Prajwal89\Llm\Dtos\MessageDto;
use Prajwal89\Llm\HttpClients\DeepseekClient;

class Deepseek implements ChatProvider
{
    public function __construct(
        public string $modelName,
        public ?int $maxTokens,
        public ?string $systemPrompt,
        /**
         * @var Collection<MessageDto>
         */
        public Collection $messages,
    ) {}

    public function makeRequest(): LlmResponseDto
    {
        $systemPrompt = [];

        // todo use message dto
        if ($this->systemPrompt !== null && $this->systemPrompt !== '' && $this->systemPrompt !== '0') {
            $systemPrompt[0]['role'] = 'system';
            $systemPrompt[0]['content'] = $this->systemPrompt;
        }

        $response = DeepseekClient::make()
            ->post('/chat/completions', [
                'model' => $this->modelName,
                ...$this->maxTokens ? ['max_tokens' => $this->maxTokens] : [],
                'messages' => [
                    ...$systemPrompt,
                    ...$this->messages->map(function (MessageDto $message): array {
                        return $message->toAnthropic();
                    })->toArray(),
                ],
            ])
            ->throw();

        return LlmResponseDto::fromOpenAiResponse($response);
    }
}
