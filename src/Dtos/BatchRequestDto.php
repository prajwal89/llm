<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Dtos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Prajwal89\Llm\Enums\LlmModelEnum;

class BatchRequestDto
{
    public function __construct(
        public string $customId,
        public LlmModelEnum $llmModel,
        /**
         * @var Collection<MessageDto>
         */
        public Collection $messages, // user messages
        public ?string $systemPrompt = null,
        public int $maxTokens = 4000,
        public ?Model $responseable = null,
        // this will check if output has valid json and replaces text with valid json string
        public bool $strictJsonOutput = false,
    ) {
        if (!$messages->every(fn ($item): bool => $item instanceof MessageDto)) {
            throw new InvalidArgumentException('All items in the messages must be instances of MessageDto.');
        }
    }

    public function toAntropic(): array
    {
        return [
            'custom_id' => $this->customId,
            'params' => [
                'model' => $this->llmModel->value,
                'max_tokens' => $this->maxTokens,
                'system' => $this->systemPrompt,
                'messages' => $this->messages->map(function (MessageDto $message): array {
                    return $message->toAnthropic();
                })->toArray(),
            ],
        ];
    }

    public function toOpenAi(): array
    {
        $openAiMessages = $this->messages->map(function (MessageDto $message): array {
            return $message->toOpenAi();
        })->toArray();

        // Prepend system prompt if available
        if ($this->systemPrompt) {
            array_unshift($openAiMessages, (new MessageDto('system', $this->systemPrompt))->toOpenAi());
        }

        return [
            'custom_id' => $this->customId,
            'method' => 'POST',
            'url' => '/v1/chat/completions',
            'body' => [
                'model' => $this->llmModel->value,
                'messages' => $openAiMessages,
                'max_tokens' => $this->maxTokens,
            ],
        ];
    }
}
