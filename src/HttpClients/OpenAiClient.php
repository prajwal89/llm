<?php

declare(strict_types=1);

namespace Prajwal89\Llm\HttpClients;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class OpenAiClient
{
    public static function make(): PendingRequest
    {
        return Http::timeout(300)
            ->withToken(config('services.open_ai.api_key'))
            ->baseUrl('https://api.openai.com/v1');
    }
}
