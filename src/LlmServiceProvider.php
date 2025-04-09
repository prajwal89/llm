<?php

declare(strict_types=1);

namespace Prajwal89\Llm;

use Illuminate\Support\ServiceProvider;

class LlmServiceProvider extends ServiceProvider
{
    public function register() {}

    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/llm.php', 'llm');

        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }
}
