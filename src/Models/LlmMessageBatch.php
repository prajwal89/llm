<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Prajwal89\Llm\Enums\BatchProcessingStatusEnum;
use Prajwal89\Llm\Enums\LlmProvider;

class LlmMessageBatch extends Model
{
    protected $primaryKey = 'batch_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'batch_id',
        'processing_status',
        'llm_provider',
        'ended_at',
        'expires_at',
        'cancel_initiated_at',
    ];

    public function responseable(): MorphTo
    {
        return $this->morphTo();
    }

    public function llmMessageBatchRequests(): HasMany
    {
        return $this->hasMany(LlmMessageBatchRequest::class, 'batch_id', 'batch_id');
    }

    protected function casts(): array
    {
        return [
            'llm_provider' => LlmProvider::class,
            'processing_status' => BatchProcessingStatusEnum::class,
            'ended_at' => 'datetime',
            'expires_at' => 'datetime',
            'cancel_initiated_at' => 'datetime',
        ];
    }
}
