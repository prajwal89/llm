<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Concerns;

use Illuminate\Support\Collection;
use Prajwal89\Llm\Dtos\BatchResponseDto;

interface InteractsWithBatches
{
    public function makeRequest(): BatchResponseDto;

    /**
     * @return Collection<BatchResponseDto>
     */
    public static function listBatches(): Collection;

    public static function checkBatchStatus(string $batchId): BatchResponseDto;

    /**
     * @return Collection<BatchRequestResponseDto>
     */
    public static function getBatchResults(string $batchId): Collection;
}
