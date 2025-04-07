<?php

declare(strict_types=1);

namespace Prajwal89\Llm;

use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

class Logger
{
    public static function chat(): LoggerInterface
    {
        return Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/llm/chat.log'),
        ]);
    }

    public static function embeddings(): LoggerInterface
    {
        return Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/llm/embeddings.log'),
        ]);
    }

    public static function batchRequests(): LoggerInterface
    {
        return Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/llm/batch-requests.log'),
        ]);
    }
}
