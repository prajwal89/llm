<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Dtos;

use Illuminate\Http\Client\Response;

class LlmResponseDto
{
    public function __construct(
        public array $content = [],
        public array $tokenUsage = [
            'inputTokens' => 0,
            'outputTokens' => 0,
        ]
    ) {
        //
    }

    public static function fromAnthropicResponse(Response $response): self
    {
        $data = $response->json();

        return new self(
            content: collect($data['content'])
                ->map(function ($choice): MessageDto {
                    return MessageDto::fromAnthropic($choice);
                })->toArray(),
            tokenUsage: [
                'inputTokens' => $data['usage']['input_tokens'] ?? 0,
                'outputTokens' => $data['usage']['output_tokens'] ?? 0,
            ]
        );
    }

    public static function fromOpenAiResponse(Response $response): self
    {
        $data = $response->json();

        // dd($data);
        return new self(
            content: collect($data['choices'])
                ->map(function ($choice): MessageDto {
                    return MessageDto::fromOpenAi($choice);
                })->toArray(),
            tokenUsage: [
                'inputTokens' => $data['usage']['prompt_tokens'],
                'outputTokens' => $data['usage']['completion_tokens'],
            ]
        );
    }

    public static function fromJson(string $jsonString): self
    {
        $decodedJson = json_decode($jsonString, true);

        // dd($decodedJson);

        return new self(
            content: collect($decodedJson['content'])
                ->map(function ($array): MessageDto {
                    return MessageDto::fromArray($array);
                })->toArray(),
            tokenUsage: $decodedJson['tokenUsage']
        );
    }

    public static function fromMetaResponse(Response $response): self
    {
        $data = $response->json();

        return new self(
            content: [
                MessageDto::fromMeta($data['message']),
            ],
            tokenUsage: [
                'inputTokens' => $data['prompt_eval_count'],
                'outputTokens' => $data['eval_count'],
            ]
        );
    }

    public static function fromGoogleResponse(Response $response): self
    {
        $data = $response->json();

        return new self(
            content: collect($data['candidates'])
                ->map(function ($array): MessageDto {
                    return MessageDto::fromGoogle($array);
                })->toArray(),
            tokenUsage: [
                'inputTokens' => $data['usageMetadata']['promptTokenCount'],
                'outputTokens' => $data['usageMetadata']['candidatesTokenCount'],
            ]
        );
    }
}
