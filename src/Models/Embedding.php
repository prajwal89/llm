<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Qdrant\Enums\EmbeddingUseCase;

class Embedding extends Model
{
    use Prunable;

    protected $table = 'llm_embeddings';

    protected $fillable = [
        'input_text',
        'use_case',
        'model_name',
        'embedable_id',
        'embedable_type',
        'vectors',
        'total_tokens',
    ];

    protected function casts(): array
    {
        return [
            'use_case' => EmbeddingUseCase::class,
            'vectors' => 'array',
            'input_text_md5' => 'string',
        ];
    }

    public function embedable(): MorphTo
    {
        return $this->morphTo();
    }

    public function prunable(): Builder
    {
        return static::query()
            ->where('created_at', '<=', now()->subMonths(1))
            ->where('use_case', EmbeddingUseCase::SEARCH);
    }
}
