<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Dtos;

use Illuminate\Contracts\Support\Arrayable;
use Prajwal89\Llm\Enums\BatchRequestStatus;

class BatchRequestResponseDto implements Arrayable
{
    public function __construct(
        public string $customId,
        public string $responseText,
        public BatchRequestStatus $status,
        public int $inputTokens,
        public int $outputTokens,
    ) {}

    public static function fromAntropic(array $response): self
    {
        return new self(
            customId: $response['custom_id'],
            responseText: $response['result']['message']['content'][0]['text'],
            inputTokens: $response['result']['message']['usage']['input_tokens'],
            outputTokens: $response['result']['message']['usage']['output_tokens'],
            status: BatchRequestStatus::fromAnthropic($response['result']['type'])
        );
    }

    public static function fromOpenAi(array $response): self
    {
        return new self(
            customId: $response['custom_id'],
            responseText: $response['response']['body']['choices'][0]['message']['content'],
            inputTokens: $response['response']['body']['usage']['prompt_tokens'],
            outputTokens: $response['response']['body']['usage']['completion_tokens'],
            status: BatchRequestStatus::fromOpenAi($response['response']['status_code'])
        );
    }

    public function toArray(): array
    {
        return [
            'customId' => $this->customId,
            'responseText' => $this->responseText,
            'status' => $this->status->value, // Assuming BatchRequestStatus has a `value` property or method
            'inputTokens' => $this->inputTokens,
            'outputTokens' => $this->outputTokens,
        ];
    }
}
