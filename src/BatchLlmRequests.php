<?php

declare(strict_types=1);

namespace Prajwal89\Llm;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Prajwal89\Llm\Dtos\BatchRequestDto;
use Prajwal89\Llm\Dtos\BatchRequestResponseDto;
use Prajwal89\Llm\Dtos\BatchResponseDto;
use Prajwal89\Llm\Dtos\LlmResponseDto;
use Prajwal89\Llm\Enums\BatchRequestStatus;
use Prajwal89\Llm\Enums\LlmProvider;
use Prajwal89\Llm\Models\LlmMessageBatch;
use Prajwal89\Llm\Strategies\Batches\Anthropic;
use Prajwal89\Llm\Strategies\Batches\OpenAI;

// ! this is a beta feature
class BatchLlmRequests
{
    // LlmResponseDto
    // todo convert this to DTO
    public BatchResponseDto $llmBatchResponse;

    public LlmMessageBatch $llmMessageBatch;

    public int $timeTakenInMs = 0;

    public function __construct(
        /**
         * @var Collection<BatchRequestDto>
         */
        public Collection $requests,
        // we require this as we cannot batch request of multiple families
        public LlmProvider $modelFamily,
    ) {
        if (!$requests->every(fn ($item): bool => $item instanceof BatchRequestDto)) {
            throw new InvalidArgumentException('All items in the collection must be instances of BatchRequestDto.');
        }
    }

    public function makeRequest(): static
    {
        $strategyClass = self::getStrategy($this->modelFamily);

        $strategy = new $strategyClass($this->requests);

        $this->llmBatchResponse = $strategy->makeRequest();

        $this->record();

        return $this;
    }

    private static function getStrategy(LlmProvider $modelFamily): string
    {
        return match ($modelFamily) {
            LlmProvider::ANTHROPIC => Anthropic::class,
            LlmProvider::OPEN_AI => OpenAI::class,
            default => throw new Exception('We Do not have Batch Request strategy for: ' . $modelFamily->value)
        };
    }

    /**
     * store Message batch with its requests
     */
    private function record(): void
    {
        DB::transaction(function (): void {
            $llmMessageBatch = LlmMessageBatch::query()->create([
                'batch_id' => $this->llmBatchResponse->batchId,
                'processing_status' => $this->llmBatchResponse->status->value,
                'llm_provider' => $this->modelFamily->value,
                'ended_at' => $this->llmBatchResponse->endedAt,
                'expires_at' => $this->llmBatchResponse->expiresAt,
                'cancel_initiated_at' => $this->llmBatchResponse->cancelInitiatedAt,
            ]);

            $this->requests->map(function (BatchRequestDto $request) use ($llmMessageBatch): void {
                $llmMessageBatch->llmMessageBatchRequests()->create([
                    'custom_id' => $request->customId,
                    'model_name' => $request->llmModel,
                    'system_prompt' => $request->systemPrompt,
                    'user_prompt' => json_encode($request->messages),
                    'status' => BatchRequestStatus::PROCESSING,
                    'responseable_id' => $request->responseable instanceof Model ? $request->responseable->getKey() : null,
                    'responseable_type' => $request->responseable instanceof Model ? get_class($request->responseable) : null,
                ]);
            });
        });
    }

    public static function listBatches(LlmProvider $llmFamilyEnum)
    {
        $strategyClass = self::getStrategy($llmFamilyEnum);

        return $strategyClass::listBatches();
    }

    public static function checkBatchStatus(string $batchId): BatchResponseDto
    {
        $batch = LlmMessageBatch::query()
            ->where('batch_id', $batchId)
            ->firstOr(function () use ($batchId): void {
                throw new Exception("Batch ID $batchId not found");
            });

        $strategyClass = self::getStrategy($batch->llm_provider);

        return $strategyClass::checkBatchStatus($batchId);
    }

    /**
     * @return Collection<BatchRequestResponseDto>
     */
    public static function getBatchResults(string $batchId): Collection
    {
        // todo batch need to be completed too fetching result (fail or complete doesnt matter)
        //
        $batch = LlmMessageBatch::query()
            ->where('batch_id', $batchId)
            ->firstOr(function () use ($batchId): void {
                throw new Exception("Batch ID $batchId not found");
            });

        $strategyClass = self::getStrategy($batch->llm_provider);

        // dd($httpResponse);

        return $strategyClass::getBatchResults($batchId);
    }
}
