<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Prajwal89\Llm\Enums\LlmModelEnum;
use Prajwal89\Llm\Helpers\Helper;

class LlmUsage extends Model
{
    protected $table = 'llm_usages';

    protected $fillable = [
        'system_prompt',
        'user_prompt',
        'response',
        'prompt_md5',
        'input_tokens',
        'output_tokens',
        'model_name',
        'time_taken_ms',
        'blade_template_name',
        'responseable_id',
        'responseable_type',
        'is_from_message_batch',
    ];

    public function casts(): array
    {
        return [
            'input_tokens' => 'integer',
            'output_tokens' => 'integer',
            'time_taken_ms' => 'integer',
            'is_from_message_batch' => 'boolean',
            // ! we are not casting this b.c of this will fail usage table if deleted models have
            // usage record
            // 'model_name' => LlmModelEnum::class,
        ];
    }

    public function responseable(): MorphTo
    {
        return $this->morphTo();
    }

    public function llmResponseProcessRecords(): MorphMany
    {
        return $this->morphMany(LlmResponseProcessRecord::class, 'processable');
    }

    public function calculatedHash(): string
    {
        // dd($this->model_name);
        return Helper::llmUsageHash(
            $this->system_prompt,
            json_decode($this->user_prompt, true),
            // $this->model_name,
            LlmModelEnum::from($this->model_name),
        );
    }
}
