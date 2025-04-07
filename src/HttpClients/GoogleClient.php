<?php

declare(strict_types=1);

namespace Prajwal89\Llm\HttpClients;

use Illuminate\Support\Facades\Http;

class GoogleClient
{
    public static function make()
    {
        return Http::timeout(300)
            ->acceptJson()
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->withQueryParameters([
                'key' => config('services.google.ai.api_key'),
            ])
            ->baseUrl('https://generativelanguage.googleapis.com/v1beta');
    }
}
