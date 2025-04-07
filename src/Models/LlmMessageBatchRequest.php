<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Prajwal89\Llm\Enums\BatchRequestStatus;
use Prajwal89\Llm\Enums\LlmModelEnum;

class LlmMessageBatchRequest extends Model
{
    protected $table = 'llm_message_batch_requests';

    protected $fillable = [
        'custom_id',
        'batch_id',
        'system_prompt',
        'user_prompt',
        'model_name',
        'responseable_id',
        'responseable_type',

        // updated after response
        'response',
        'input_tokens',
        'output_tokens',
        'status',
        'error_message',
    ];

    public function responseable(): MorphTo
    {
        return $this->morphTo();
    }

    public function llmMessageBatch(): BelongsTo
    {
        return $this->belongsTo(LlmMessageBatch::class, 'batch_id', 'batch_id');
    }

    public function llmResponseProcessRecords(): MorphOne
    {
        return $this->morphOne(LlmResponseProcessRecord::class, 'processable');
    }

    protected function casts(): array
    {
        return [
            'status' => BatchRequestStatus::class,
            'model_name' => LlmModelEnum::class,
            'input_tokens' => 'integer',
            'output_tokens' => 'integer',
        ];
    }
}
