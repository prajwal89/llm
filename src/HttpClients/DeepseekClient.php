<?php

declare(strict_types=1);

namespace Prajwal89\Llm\HttpClients;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class DeepseekClient
{
    public static function make(): PendingRequest
    {
        return Http::timeout(300)
            ->withToken(config('services.deepseek.api_key'))
            ->baseUrl('https://api.deepseek.com/v1');
    }
}
