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
use Prajwal89\Llm\HttpClients\OpenAiClient;

/**
 * @see https://platform.openai.com/docs/guides/batch/getting-started?lang=node
 */
class OpenAI implements InteractsWithBatches
{
    private PendingRequest $httpClient;

    public function __construct(
        public Collection $requests,
    ) {
        $this->httpClient = OpenAiClient::make();
    }

    // return dto
    // https://platform.openai.com/docs/api-reference/batch/create
    public function makeRequest(): BatchResponseDto
    {
        // todo build jsonl file
        $requests = $this->requests->map(function (BatchRequestDto $request): array {
            return $request->toOpenAi();
        });

        // dd($requests);

        $jsonlFileContents = Helper::JsonToJsonl(json_encode($requests));
        $uploadJsonlFileResponse = $this->uploadJsonlFile($jsonlFileContents);
        $fileId = $uploadJsonlFileResponse->json('id');

        // $fileId = 'file-2Emt7i5Hm5nBBobWsdfigB';

        // dd($fileId);

        $response = $this->httpClient
            // ->acceptJson()
            ->post('/batches', [
                'input_file_id' => $fileId,
                'endpoint' => '/v1/chat/completions',
                'completion_window' => '24h',
            ])
            ->throw();

        return BatchResponseDto::fromOpenAi($response->json());
    }

    public function uploadJsonlFile(string $jsonlFileContents)
    {
        // $client = $this->httpClient;

        $client = clone $this->httpClient;

        return $client
            ->attach('file', $jsonlFileContents, 'data.jsonl')
            ->post('/files', [
                'purpose' => 'batch',
            ]);
    }

    /**
     * @return Collection<BatchResponseDto>
     */
    // todo add support for pagination
    public static function listBatches(): Collection
    {
        $response = OpenAiClient::make()->get('/batches');

        return collect($response->json('data'))->map(function ($data): BatchResponseDto {
            return BatchResponseDto::fromOpenAi($data);
        });
    }

    public static function checkBatchStatus(string $batchId): BatchResponseDto
    {
        $response = OpenAiClient::make()->get("/batches/{$batchId}");

        return BatchResponseDto::fromOpenAi($response->json());
    }

    /**
     * @return Collection<BatchRequestResponseDto>
     */
    public static function getBatchResults(string $batchId): Collection
    {
        // todo check success response first
        $batchResponseDto = self::checkBatchStatus($batchId);

        $response = OpenAiClient::make()
            ->retry(2, 3000)
            ->get("files/{$batchResponseDto->outputFileId}/content");

        $responses = Helper::jsonlToArray($response->body());

        return $responses->map(function ($res): BatchRequestResponseDto {
            return BatchRequestResponseDto::fromOpenAi($res);
        });
    }
}
