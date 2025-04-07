<?php

declare(strict_types=1);

namespace Prajwal89\Llm\HttpClients;

use Illuminate\Support\Facades\Http;

class AnthropicClient
{
    public static function make()
    {
        return Http::timeout(600)
            ->acceptJson()
            ->contentType('application/json')
            ->withHeaders([
                'x-api-key' => config('services.anthtopic.api_key'),
                'anthropic-version' => '2023-06-01',
            ])
            ->baseUrl('https://api.anthropic.com/v1');
    }
}
