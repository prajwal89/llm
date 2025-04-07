<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Strategies\Batches;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Prajwal89\Llm\Concerns\InteractsWithBatches;
use Prajwal89\Llm\Dtos\BatchRequestDto;
use Prajwal89\Llm\Dtos\BatchRequestResponseDto;
use Prajwal89\Llm\Dtos\BatchResponseDto;
use Prajwal89\Llm\Helpers\Helper;
use Prajwal89\Llm\HttpClients\AnthropicClient;

class Anthropic implements InteractsWithBatches
{
    private PendingRequest $httpClient;

    public function __construct(
        public Collection $requests,
    ) {
        $this->httpClient = AnthropicClient::make();
    }

    // return dto
    public function makeRequest(): BatchResponseDto
    {
        $requests = $this->requests->map(function (BatchRequestDto $request): array {
            return $request->toAntropic();
        });

        $response = $this->httpClient
            ->withHeaders([
                'anthropic-version' => '2023-06-01',
                'anthropic-beta' => 'message-batches-2024-09-24',
            ])
            ->post('/messages/batches', [
                'requests' => $requests,
            ])
            ->throw();

        return BatchResponseDto::fromAnthropic($response->json());
    }

    /**
     * @return Collection<BatchResponseDto>
     */
    // todo add support for pagination
    public static function listBatches(int $limit = 100): Collection
    {
        $response = AnthropicClient::make()
            ->withHeaders([
                'anthropic-version' => '2023-06-01',
                'anthropic-beta' => 'message-batches-2024-09-24',
            ])
            ->withQueryParameters([
                'limit' => $limit,
            ])
            ->get('/messages/batches');

        // dd($response->json());

        $dtos = collect($response->json('data'))->map(function ($data): BatchResponseDto {
            return BatchResponseDto::fromAnthropic($data);
        });

        return collect([
            'data' => $dtos,
            'pagination' => [
                'has_more' => $response->json('has_more'),
            ],
        ]);
    }

    public static function checkBatchStatus(string $batchId): BatchResponseDto
    {
        $response = AnthropicClient::make()
            ->retry(2, 3000)
            ->withHeaders([
                'anthropic-version' => '2023-06-01',
                'anthropic-beta' => 'message-batches-2024-09-24',
            ])
            ->get("/messages/batches/{$batchId}");

        return BatchResponseDto::fromAnthropic($response->json());
    }

    /**
     * @return Collection<BatchRequestResponseDto>
     */
    public static function getBatchResults(string $batchId): Collection
    {
        $response = AnthropicClient::make()
            ->retry(2, 3000)
            ->withHeaders([
                'anthropic-version' => '2023-06-01',
                'anthropic-beta' => 'message-batches-2024-09-24',
            ])
            ->get("/messages/batches/{$batchId}/results");

        $responses = Helper::jsonlToArray($response->body());

        return $responses->map(function ($res): BatchRequestResponseDto {
            return BatchRequestResponseDto::fromAntropic($res);
        });
    }
}
