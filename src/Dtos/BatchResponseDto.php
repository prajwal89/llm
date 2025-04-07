<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Dtos;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Prajwal89\Llm\Enums\BatchProcessingStatusEnum;

class BatchResponseDto implements Arrayable
{
    public function __construct(
        public string $batchId,
        public BatchProcessingStatusEnum $status,
        public Carbon $createdAt,
        public Carbon $endedAt,
        public Carbon $cancelInitiatedAt,
        public Carbon $expiresAt,
        // for openAi
        public ?string $outputFileId = null,
    ) {
        //
    }

    public static function fromOpenAi(array $response): self
    {
        return new self(
            batchId: $response['id'],
            status: BatchProcessingStatusEnum::fromOpenAi($response['status']),
            createdAt: Carbon::parse($response['created_at']),
            endedAt: Carbon::parse($response['completed_at']),
            cancelInitiatedAt: Carbon::parse($response['cancelling_at']),
            expiresAt: Carbon::parse($response['expired_at']),
            outputFileId: $response['output_file_id']
        );
    }

    public static function fromAnthropic(array $response): self
    {
        return new self(
            batchId: $response['id'],
            status: BatchProcessingStatusEnum::fromAnthropic($response['processing_status']),
            createdAt: Carbon::parse($response['created_at']),
            endedAt: Carbon::parse($response['ended_at']),
            cancelInitiatedAt: Carbon::parse($response['cancel_initiated_at']),
            expiresAt: Carbon::parse($response['expires_at']),
        );
    }

    public function toArray(): array
    {
        return [
            'batchId' => $this->batchId,
            'status' => $this->status->value,
            'createdAt' => $this->createdAt->toIso8601String(),
            'endedAt' => $this->endedAt->toIso8601String(),
            'cancelInitiatedAt' => $this->cancelInitiatedAt->toIso8601String(),
            'expiresAt' => $this->expiresAt->toIso8601String(),
            'outputFileId' => $this->outputFileId,
        ];
    }
}
