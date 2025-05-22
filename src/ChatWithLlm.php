<?php

declare(strict_types=1);

namespace Prajwal89\Llm;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Prajwal89\Llm\Concerns\ChatProvider;
use Prajwal89\Llm\Dtos\LlmResponseDto;
use Prajwal89\Llm\Dtos\MessageDto;
use Prajwal89\Llm\Helpers\Helper;
use Prajwal89\Llm\Models\LlmUsage;
use Prajwal89\Llm\Strategies\Chat\Anthropic;
use Prajwal89\Llm\Strategies\Chat\Deepseek;
use Prajwal89\Llm\Strategies\Chat\Google;
use Prajwal89\Llm\Strategies\Chat\OpenAI;
use Psr\Log\LoggerInterface;

// todo add support for local llm through tunnel
// todo mockable
// todo logging turn on and off
// todo handle same model on different service provider like llama model is available on @cf and other providers
class ChatWithLlm
{
    /**
     * As it is response from AI endpoint
     * this may contain invalid json or failed response
     */
    public LlmResponseDto $llmResponse;

    public int $timeTakenInMs = 0;

    public LoggerInterface $logger;

    public ?LlmUsage $llmUsage = null;

    public function __construct(
        public string $modelName,
        /**
         * @var Collection<MessageDto>
         */
        public string $provider,
        public Collection $messages,
        public ?string $systemPrompt = null,
        public ?int $maxTokens = null,
        public ?Model $responseable = null,
        // this will check if output has valid json and replaces text with valid json string
        public bool $strictJsonOutput = false,
        public bool $saveUsageData = true,
        public bool $checkCache = true,
        public array $additionalParams = []
    ) {
        $this->logger = Logger::chat();

        if (!$messages->every(fn($item): bool => $item instanceof MessageDto)) {
            throw new InvalidArgumentException('All items in the messages must be instances of MessageDto.');
        }
    }

    public function makeRequest(): self
    {
        $strategy = $this->getStrategy();

        if ($this->checkCache) {
            $md5HashOfPrompt = Helper::llmUsageHash(
                $this->systemPrompt,
                $this->messages->toArray(),
                $this->modelName
            );

            $llmUsage = LlmUsage::query()->where('prompt_md5', $md5HashOfPrompt)->first();

            // we found the cache
            if ($llmUsage) {
                $this->llmUsage = $llmUsage;
                $this->llmResponse = LlmResponseDto::fromJson($llmUsage->response);
                // we require this as we save raw response in database
                if ($this->strictJsonOutput) {
                    $validJson = Helper::extractValidJson($this->llmResponse->content[0]->text);
                    $this->llmResponse->content[0]->text = $validJson;
                }

                return $this;
            }
        }

        $startTime = microtime(true);

        $this->llmResponse = $strategy->makeRequest();

        $this->timeTakenInMs = (int) round((microtime(true) - $startTime) * 1000);

        if ($this->saveUsageData) {
            $this->saveUsageData();
        }

        // * we need to do this bc of anthropic sends response like
        //  ```json {} ```
        if ($this->strictJsonOutput) {
            $validJson = Helper::extractValidJson($this->llmResponse->content[0]->text);
            $this->llmResponse->content[0]->text = $validJson;
        }

        return $this;
    }

    public function getStrategy(): ChatProvider
    {
        // dd($this->provider, OpenAI::class);
        // dd(get_class($this->provider));
        return match ($this->provider) {
            Anthropic::class => new Anthropic(
                modelName: $this->modelName,
                messages: $this->messages,
                systemPrompt: $this->systemPrompt,
                maxTokens: $this->maxTokens,
                additionalParams: $this->additionalParams
            ),
            OpenAI::class => new OpenAI(
                modelName: $this->modelName,
                messages: $this->messages,
                systemPrompt: $this->systemPrompt,
                maxTokens: $this->maxTokens,
                additionalParams: $this->additionalParams
            ),
            Deepseek::class => new Deepseek(
                modelName: $this->modelName,
                messages: $this->messages,
                systemPrompt: $this->systemPrompt,
                maxTokens: $this->maxTokens,
                additionalParams: $this->additionalParams
            ),
            Google::class => new Google(
                modelName: $this->modelName,
                messages: $this->messages,
                systemPrompt: $this->systemPrompt,
                maxTokens: $this->maxTokens,
                additionalParams: $this->additionalParams
            )
        };
    }

    public function saveUsageData(): void
    {
        try {
            $this->llmUsage = LlmUsage::query()->create([
                'system_prompt' => $this->systemPrompt,
                'user_prompt' => json_encode($this->messages),
                'response' => json_encode($this->llmResponse),
                'input_tokens' => $this->llmResponse->tokenUsage['inputTokens'],
                'output_tokens' => $this->llmResponse->tokenUsage['outputTokens'],
                'time_taken_ms' => $this->timeTakenInMs,
                'model_name' => $this->modelName,
                'blade_template_name' => null,
                'responseable_id' => $this->responseable instanceof Model ? $this->responseable->getKey() : null,
                'responseable_type' => $this->responseable instanceof Model ? get_class($this->responseable) : null,
            ]);
        } catch (Exception $e) {
            if (!app()->isProduction()) {
                throw $e;
            }
        }
    }
}
