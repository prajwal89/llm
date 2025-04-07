<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Prajwal89\Llm\Enums\LlmResponseProcessStatus;

/**
 * Generally we need to process data from llm responses
 * so we can track process status or error here
 */
class LlmResponseProcessRecord extends Model
{
    protected $table = 'llm_response_process_record';

    protected $fillable = [
        'status',
        'processable_id',
        'processable_type',
        'error',
    ];

    public function casts()
    {
        return [
            'status' => LlmResponseProcessStatus::class,
        ];
    }

    public function processable(): MorphTo
    {
        return $this->morphTo();
    }
}
