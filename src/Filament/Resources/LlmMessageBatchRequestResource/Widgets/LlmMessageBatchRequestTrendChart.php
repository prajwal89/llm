<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Filament\Resources\LlmMessageBatchRequestResource\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Prajwal89\Llm\Models\LlmMessageBatchRequest;

class LlmMessageBatchRequestTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Requests';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '320px';

    protected static bool $isLazy = true;

    public ?string $filter = 'Daily';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $query = Trend::model(LlmMessageBatchRequest::class);

        $query = match ($this->filter) {
            'Daily' => $query->between(
                start: now()->subDays(60),
                end: now(),
            )->perDay(),
            'Monthly' => $query->between(
                start: now()->subYear(),
                end: now(),
            )->perMonth()
        };

        $data = $query->count();

        $data = [
            'datasets' => [
                [
                    'label' => 'Requests',
                    'data' => $data->map(fn (TrendValue $value): mixed => $value->aggregate),
                ],
            ],
            'labels' => $data->map(function (TrendValue $value): string {
                return $value->date;
            }),
        ];

        return $data;
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getFilters(): ?array
    {
        return [
            'Daily' => 'Daily',
            'Monthly' => 'Monthly',
        ];
    }
}
