<?php

declare(strict_types=1);

namespace Prajwal89\Llm;

use Illuminate\Database\Eloquent\Model;
use Prajwal89\Llm\Dtos\EmbeddingResponseDto;
use Prajwal89\Llm\Enums\EmbeddingModelEnum;
use Prajwal89\Llm\Enums\EmbeddingModelFamilyEnum;
use Prajwal89\Llm\Models\Embedding;
use Prajwal89\Llm\Strategies\Embedding\OpenAI;
use Prajwal89\Llm\Strategies\Embedding\VoyageAI;

// todo support for embedding caching
// record embeddings here to the database
class GetEmbeddings
{
    public EmbeddingResponseDto $embeddingResponse;

    public function __construct(
        public EmbeddingModelEnum $embeddingModel,
        public string $input,
        public string $useCase = 'default',
        public ?Model $embedable = null,
        public array $additionalParams = [],
        public $tryFromDb = true,
        public $saveDbRecord = true,
    ) {}

    public function makeRequest(): self
    {
        if ($this->tryFromDb) {
            $dbRecord = Embedding::query()
                ->where('model_name', $this->embeddingModel->value)
                ->where('input_text_md5', md5($this->input))
                ->first();

            if ($dbRecord) {
                $this->embeddingResponse = EmbeddingResponseDto::fromDb($dbRecord);

                return $this;
            }
        }

        $strategy = match ($this->embeddingModel->llmFamilyName()) {
            EmbeddingModelFamilyEnum::VOYAGE_AI => new VoyageAI(
                embeddingModel: $this->embeddingModel,
                input: $this->input,
                additionalParams: $this->additionalParams
            ),
            EmbeddingModelFamilyEnum::OPEN_AI => new OpenAI(
                embeddingModel: $this->embeddingModel,
                input: $this->input,
                additionalParams: $this->additionalParams
            ),
        };

        $this->embeddingResponse = $strategy->makeRequest();

        if ($this->saveDbRecord) {
            $this->saveUsageData();
        }

        return $this;
    }

    public function saveUsageData(): void
    {
        Embedding::query()->create([
            'input_text' => $this->input,
            'use_case' => $this->useCase,
            'model_name' => $this->embeddingModel->value,
            'vectors' => $this->embeddingResponse->embeddings,
            'total_tokens' => $this->embeddingResponse->totalTokens,
            'embedable_id' => $this->embedable instanceof Model ? $this->embedable->getKey() : null,
            'embedable_type' => $this->embedable instanceof Model ? get_class($this->embedable) : null,
        ]);
    }
}
