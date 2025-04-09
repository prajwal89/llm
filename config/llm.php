<?php

declare(strict_types=1);

return [
    /**
     * cost per million token
     * used for getting price estimation
     */
    'pricing' => [
        'gpt-3.5-turbo' => [
            'input' => 3 / 1_000_000,
            'output' => 6 / 1_000_000,
        ],
        'gpt-4' => [
            'input' => 30 / 1_000_000,
            'output' => 60 / 1_000_000,
        ],
        'gpt-4-turbo' => [
            'input' => 10 / 1_000_000,
            'output' => 30 / 1_000_000,
        ],
        'gpt-4o' => [
            'input' => 2.50 / 1_000_000,
            'output' => 10 / 1_000_000,
        ],
        'claude-3-5-haiku-20241022' => [
            'input' => 1 / 1_000_000,
            'output' => 5 / 1_000_000,
        ],
        'claude-3-5-sonnet-20241022' => [
            'input' => 3 / 1_000_000,
            'output' => 15 / 1_000_000,
        ],
        'claude-3-7-sonnet-20250219' => [
            'input' => 3 / 1_000_000,
            'output' => 15 / 1_000_000,
        ],
        'gemini-1.5-pro' => [
            'input' => 1.25 / 1_000_000,
            'output' => 5 / 1_000_000,
        ],
        'gemini-1.5-flash-latest' => [
            'input' => 0.075 / 1_000_000,
            'output' => 0.30 / 1_000_000,
        ],
        'deepseek-chat' => [
            'input' => 0.14 / 1_000_000,
            'output' => 0.28 / 1_000_000,
        ],
        'deepseek-reasoner' => [
            'input' => 0.55 / 1_000_000,
            'output' => 2.19 / 1_000_000,
        ],
        // Fallback/default pricing
        'llama3.1' => [
            'input' => 1 / 1_000_000,
            'output' => 1 / 1_000_000,
        ],
        'gemini-2.0-flash-thinking-exp-01-21' => [
            'input' => 1 / 1_000_000,
            'output' => 1 / 1_000_000,
        ],
        'gemini-2.0-pro-exp-02-05' => [
            'input' => 1 / 1_000_000,
            'output' => 1 / 1_000_000,
        ],
        'gemini-2.5-pro-exp-03-25' => [
            'input' => 1 / 1_000_000,
            'output' => 1 / 1_000_000,
        ],
    ],
];
