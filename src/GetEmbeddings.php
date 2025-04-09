<?php

declare(strict_types=1);

namespace Prajwal89\Llm;

use Illuminate\Database\Eloquent\Model;
use Prajwal89\Llm\Dtos\EmbeddingResponseDto;
use Prajwal89\Llm\Models\Embedding;
use Prajwal89\Llm\Strategies\Embedding\OpenAI;
use Prajwal89\Llm\Strategies\Embedding\VoyageAI;

class GetEmbeddings
{
    public EmbeddingResponseDto $embeddingResponse;

    public function __construct(
        public string $modelName,
        public string $provider,
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
                ->where('model_name', $this->modelName)
                ->where('input_text_md5', md5($this->input))
                ->first();

            if ($dbRecord) {
                $this->embeddingResponse = EmbeddingResponseDto::fromDb($dbRecord);

                return $this;
            }
        }

        $strategy = $this->getStrategy();

        $this->embeddingResponse = $strategy->makeRequest();

        if ($this->saveDbRecord) {
            $this->saveUsageData();
        }

        return $this;
    }

    public function getStrategy()
    {
        return match ($this->provider) {
            VoyageAI::class => new VoyageAI(
                modelName: $this->modelName,
                input: $this->input,
                additionalParams: $this->additionalParams
            ),
            OpenAI::class => new OpenAI(
                modelName: $this->modelName,
                input: $this->input,
                additionalParams: $this->additionalParams
            ),
        };
    }

    public function saveUsageData(): void
    {
        Embedding::query()->create([
            'input_text' => $this->input,
            'use_case' => $this->useCase,
            'model_name' => $this->modelName,
            'vectors' => $this->embeddingResponse->embeddings,
            'total_tokens' => $this->embeddingResponse->totalTokens,
            'embedable_id' => $this->embedable instanceof Model ? $this->embedable->getKey() : null,
            'embedable_type' => $this->embedable instanceof Model ? get_class($this->embedable) : null,
        ]);
    }
}
