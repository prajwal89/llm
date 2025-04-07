<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Filament\Resources\LlmUsageResource\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Prajwal89\Llm\Models\LlmUsage;

class LlmUsageTrend extends ChartWidget
{
    protected static ?string $heading = 'Llm Usage';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '320px';

    protected static bool $isLazy = true;

    public ?string $filter = 'Daily';

    protected static ?string $pollingInterval = '30s';

    protected static ?int $sort = 0;

    protected function getData(): array
    {
        if ($this->filter === 'Daily') {
            $data = LlmUsage::query()->select([
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as aggregate'),
            ])
                ->whereBetween('created_at', [now()->subDays(30), now()])
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $tokenData = LlmUsage::query()->select([
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(input_tokens) as input_aggregate'),
                DB::raw('SUM(output_tokens) as output_aggregate'),
                DB::raw('(SUM(input_tokens) + SUM(output_tokens)) as aggregate'),
            ])
                ->whereBetween('created_at', [now()->subDays(30), now()])
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        }

        // if ($this->filter == 'Hourly') {
        //     $data = LlmUsage::select([
        //         DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00") as date'),
        //         DB::raw('COUNT(*) as aggregate'),
        //     ])
        //         ->whereBetween(
        //             'created_at',
        //             [now()->subMinutes(2000), now()]
        //         )

        //         ->groupBy('date')
        //         ->orderBy('date')
        //         ->get();
        //     // dd($data);

        //     $tokenData = LlmUsage::select([
        //         DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00") as date'),
        //         DB::raw('COUNT(*) as aggregate'),
        //     ])
        //         ->whereBetween(
        //             'created_at',
        //             [now()->subMinutes(100), now()]
        //         )
        //         ->groupBy('date')
        //         ->orderBy('date')
        //         ->get();
        //     dd($data->map(function ($value) {
        //         return $value->date;
        //     }));
        // }

        $data = [
            'datasets' => [
                [
                    'label' => 'Usage Count',
                    'data' => $data->map(fn ($value) => $value->aggregate),
                    'borderColor' => 'red',
                    'backgroundColor' => 'lightred',
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Token Count',
                    'data' => $tokenData->map(fn ($value) => $value->aggregate),
                    'borderColor' => 'blue',
                    'yAxisID' => 'y1',
                ],
                // [
                //     'label' => 'input_aggregate',
                //     'data' => $tokenData->map(fn($value) => $value->input_aggregate),
                //     'borderColor' => 'violet',
                //     'yAxisID' => 'y1',
                // ],
                // [
                //     'label' => 'output_aggregate',
                //     'data' => $tokenData->map(fn($value) => $value->output_aggregate),
                //     'borderColor' => 'black',
                //     'yAxisID' => 'y1',
                // ],
            ],
            'labels' => $data->map(function ($value) {
                return $value->date;
            }),
        ];

        return $data;
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            'Daily' => 'Daily',
            // 'Hourly' => 'Hourly',
        ];
    }
}
