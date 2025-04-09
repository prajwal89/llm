<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Filament\Resources\LlmUsageResource\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Prajwal89\Llm\Models\LlmUsage;

class LlmUsageTrend extends ChartWidget
{
    protected static ?string $heading = 'LLM Usage Trends';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '320px';

    protected static bool $isLazy = true;

    public ?string $filter = 'daily';

    protected static ?string $pollingInterval = '30s';

    protected static ?int $sort = 0;

    protected function getData(): array
    {
        $dateFormat = match ($this->filter) {
            'hourly' => '%Y-%m-%d %H:00',
            'weekly' => '%Y-%m-%d', // Will group by week later
            'monthly' => '%Y-%m',
            default => '%Y-%m-%d', // daily
        };

        $dbDateFormat = match ($this->filter) {
            'hourly' => 'Y-m-d H:00',
            'weekly' => 'Y-m-d',
            'monthly' => 'Y-m',
            default => 'Y-m-d', // daily
        };

        $timeRange = $this->getTimeRange();

        // Query to get usage count
        $usageQuery = LlmUsage::query()
            ->select([
                DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as date"),
                DB::raw('COUNT(*) as aggregate'),
            ])
            ->whereBetween('created_at', [$timeRange['start'], $timeRange['end']])
            ->groupBy('date')
            ->orderBy('date');

        // Query to get token usage
        $tokenQuery = LlmUsage::query()
            ->select([
                DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as date"),
                DB::raw('SUM(input_tokens) as input_aggregate'),
                DB::raw('SUM(output_tokens) as output_aggregate'),
                DB::raw('(SUM(input_tokens) + SUM(output_tokens)) as aggregate'),
            ])
            ->whereBetween('created_at', [$timeRange['start'], $timeRange['end']])
            ->groupBy('date')
            ->orderBy('date');

        // For weekly view, group by week
        if ($this->filter === 'weekly') {
            $usageQuery->select([
                DB::raw('YEARWEEK(created_at, 1) as week_number'),
                DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as date"),
                DB::raw('COUNT(*) as aggregate'),
            ])->groupBy('week_number', 'date');

            $tokenQuery->select([
                DB::raw('YEARWEEK(created_at, 1) as week_number'),
                DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as date"),
                DB::raw('SUM(input_tokens) as input_aggregate'),
                DB::raw('SUM(output_tokens) as output_aggregate'),
                DB::raw('(SUM(input_tokens) + SUM(output_tokens)) as aggregate'),
            ])->groupBy('week_number', 'date');
        }

        $usageData = $usageQuery->get();
        $tokenData = $tokenQuery->get();

        // Generate complete date series to fill gaps
        $labels = $this->generateDateSeries($timeRange['start'], $timeRange['end'], $this->filter, $dbDateFormat);

        // Map usage data to date series (filling in zeros for missing dates)
        $usageMap = $usageData->pluck('aggregate', 'date')->toArray();
        $usageSeries = array_map(fn ($date) => $usageMap[$date] ?? 0, $labels);

        // Map token data to date series (filling in zeros for missing dates)
        $tokenMap = $tokenData->pluck('aggregate', 'date')->toArray();
        $tokenSeries = array_map(fn ($date) => $tokenMap[$date] ?? 0, $labels);

        $inputTokenMap = $tokenData->pluck('input_aggregate', 'date')->toArray();
        $inputTokenSeries = array_map(fn ($date) => $inputTokenMap[$date] ?? 0, $labels);

        $outputTokenMap = $tokenData->pluck('output_aggregate', 'date')->toArray();
        $outputTokenSeries = array_map(fn ($date) => $outputTokenMap[$date] ?? 0, $labels);

        // Format labels for display
        $displayLabels = $this->formatLabelsForDisplay($labels, $this->filter);

        return [
            'datasets' => [
                [
                    'label' => 'Usage Count',
                    'data' => $usageSeries,
                    'borderColor' => '#FF6384',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderWidth' => 2,
                    'yAxisID' => 'y',
                    'tension' => 0.1,
                ],
                [
                    'label' => 'Total Tokens',
                    'data' => $tokenSeries,
                    'borderColor' => '#36A2EB',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderWidth' => 2,
                    'yAxisID' => 'y1',
                    'tension' => 0.1,
                ],
                [
                    'label' => 'Input Tokens',
                    'data' => $inputTokenSeries,
                    'borderColor' => '#4BC0C0',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderWidth' => 2,
                    'yAxisID' => 'y1',
                    'tension' => 0.1,
                ],
                [
                    'label' => 'Output Tokens',
                    'data' => $outputTokenSeries,
                    'borderColor' => '#FFCE56',
                    'backgroundColor' => 'rgba(255, 206, 86, 0.2)',
                    'borderWidth' => 2,
                    'yAxisID' => 'y1',
                    'tension' => 0.1,
                ],
            ],
            'labels' => $displayLabels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Usage Count',
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Token Count',
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
                'x' => [
                    'ticks' => [
                        'maxRotation' => 45,
                        'minRotation' => 45,
                    ],
                ],
            ],
            'elements' => [
                'line' => [
                    'fill' => 'start',
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                    'align' => 'end',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'hourly' => 'Hourly',
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
        ];
    }

    /**
     * Get time range based on selected filter
     */
    protected function getTimeRange(): array
    {
        return match ($this->filter) {
            'hourly' => [
                'start' => now()->subDay(),
                'end' => now(),
            ],
            'weekly' => [
                'start' => now()->subWeeks(12),
                'end' => now(),
            ],
            'monthly' => [
                'start' => now()->subMonths(12),
                'end' => now(),
            ],
            default => [ // daily
                'start' => now()->subDays(30),
                'end' => now(),
            ],
        };
    }

    /**
     * Generate complete date series to ensure all dates are represented
     */
    protected function generateDateSeries(Carbon $start, Carbon $end, string $filter, string $format): array
    {
        $result = [];
        $current = $start->copy();

        while ($current <= $end) {
            $result[] = $current->format($format);

            // Increment based on filter
            $current = match ($filter) {
                'hourly' => $current->addHour(),
                'weekly' => $current->addWeek(),
                'monthly' => $current->addMonth(),
                default => $current->addDay(), // daily
            };
        }

        return $result;
    }

    /**
     * Format labels for better display
     */
    protected function formatLabelsForDisplay(array $labels, string $filter): array
    {
        return match ($filter) {
            // 'hourly' => array_map(fn($date) => Carbon::createFromFormat('Y-m-d H:i', $date . ':00')->format('H:i'), $labels),
            'weekly' => array_map(fn ($date) => 'Week of ' . $date, $labels),
            'monthly' => array_map(fn ($date) => Carbon::createFromFormat('Y-m', $date)->format('M Y'), $labels),
            default => $labels, // daily
        };
    }
}
