<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Jobs;

use Exception;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Prajwal89\Llm\BatchLlmRequests;
use Prajwal89\Llm\Dtos\BatchRequestResponseDto;
use Prajwal89\Llm\Enums\BatchProcessingStatusEnum;
use Prajwal89\Llm\Models\LlmMessageBatch;
use Prajwal89\Llm\Models\LlmMessageBatchRequest;
use Prajwal89\Llm\Models\LlmUsage;

class UpdateBatchAndRequestStatusesJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public function __construct(public ?LlmMessageBatch $batch = null)
    {
        $this->queue = 'high';
    }

    public function handle(): void
    {
        if ($this->batch instanceof LlmMessageBatch) {
            $this->processBatch($this->batch);

            return;
        }

        $allPendingBatches = LlmMessageBatch::query()
            ->where('processing_status', BatchProcessingStatusEnum::IN_PROGRESS)
            ->limit(10)
            ->get();

        // dd($allPendingBatches);

        // ! this can be time consuming as we are making network call
        // ? should we use job for each message batch
        $allPendingBatches->map(function (LlmMessageBatch $llmMessageBatch): void {
            $this->processBatch($llmMessageBatch);
        });
    }

    public function processBatch(LlmMessageBatch $llmMessageBatch): void
    {
        // working on each batch
        // * check status of entire batch
        $batchResponse = BatchLlmRequests::checkBatchStatus($llmMessageBatch->batch_id);

        // in these case we need to update status of requests
        // b.c response from each individual request is only available in these cases
        if (
            $batchResponse->status === BatchProcessingStatusEnum::ENDED
            ||
            $batchResponse->status === BatchProcessingStatusEnum::FAILED
        ) {
            try {
                DB::beginTransaction();

                $llmMessageBatch->update(['processing_status' => $batchResponse->status]);

                // todo record llm usage also
                $allRequestResponse = BatchLlmRequests::getBatchResults($llmMessageBatch->batch_id);
                // dd($allRequestResponse);

                $allRequestResponse->map(function (BatchRequestResponseDto $response) use ($llmMessageBatch): void {
                    // resolve request from database
                    // todo handle if we didn't find
                    $requestDbRecord = LlmMessageBatchRequest::query()
                        ->where('batch_id', $llmMessageBatch->batch_id)
                        ->where('custom_id', $response->customId)
                        ->first();

                    // todo handle if the request is failed
                    if ($requestDbRecord === null) {
                        return;
                    }

                    // todo log error
                    // hint send error dto
                    $requestDbRecord->update([
                        'status' => $response->status,
                        'response' => $response->responseText,
                        'input_tokens' => $response->inputTokens,
                        'output_tokens' => $response->outputTokens,
                    ]);

                    $this->recordLlmUsage($response, $requestDbRecord);
                });

                DB::commit();
            } catch (Exception $e) {
                // todo handle exception
                DB::rollBack();
                throw $e;
            }
        } else {
            $llmMessageBatch->update(['processing_status' => $batchResponse->status]);
        }
    }

    public function recordLlmUsage(BatchRequestResponseDto $response, LlmMessageBatchRequest $dbRecord): LlmUsage
    {
        return LlmUsage::query()->create([
            'system_prompt' => $dbRecord->system_prompt ?? null,
            'user_prompt' => json_encode($dbRecord->user_prompt) ?? null,
            'response' => $response->responseText,
            'input_tokens' => $response->inputTokens,
            'output_tokens' => $response->outputTokens,
            'model_name' => $dbRecord->model_name,
            'blade_template_name' => null,
            'responseable_id' => $dbRecord ? $dbRecord->getKey() : null,
            'responseable_type' => $dbRecord ? get_class($dbRecord) : null,
            'is_from_message_batch' => 1,
        ]);
    }
}
