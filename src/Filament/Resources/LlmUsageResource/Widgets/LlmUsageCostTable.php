<?php

declare(strict_types=1);

namespace Prajwal89\Llm\Filament\Resources\LlmUsageResource\Widgets;

use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Prajwal89\Llm\Models\LlmUsage;

// todo consider is_from_message_batch for calculating llm cost
class LlmUsageCostTable extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                function () {
                    return LlmUsage::query()
                        ->select(
                            'id',
                            'model_name',
                            DB::raw('SUM(input_tokens) as total_input_tokens'),
                            DB::raw('SUM(output_tokens) as total_output_tokens')
                        )
                        ->groupBy('model_name');
                }
            )
            ->columns([
                TextColumn::make('model_name'),
                TextColumn::make('total_input_tokens')
                    ->getStateUsing(function ($record) {
                        return Number::abbreviate((int) $record->total_input_tokens);
                    })->summarize(Sum::make()),
                TextColumn::make('total_output_tokens')
                    ->getStateUsing(function ($record) {
                        return Number::abbreviate((int) $record->total_output_tokens);
                    })->summarize(Sum::make()),
                TextColumn::make('input_cost')
                    ->label('Input cost')
                    ->getStateUsing(function ($record) {
                        $costPerToken = config('llm.pricing' . $record->model_name . '.input');
                        // dd($costPerToken);

                        return Number::currency((int) $record->total_input_tokens * $costPerToken);
                    })
                    ->summarize(Summarizer::make('SUM')
                        ->label('Sum')->using(function ($table) {
                            // Access the records already shown in the table
                            $records = $table->getRecords();

                            // Summarize dynamic columns
                            $totalInputCost = $records->sum(function ($record): int|float {
                                $costPerToken = config('llm.pricing' . $record->model_name . '.input');

                                return $record->total_input_tokens * $costPerToken;
                            });

                            return Number::currency($totalInputCost);
                        })),
                TextColumn::make('output_cost')
                    ->label('Output cost')
                    ->getStateUsing(function ($record) {
                        $costPerToken = config('llm.pricing' . $record->model_name . '.input');

                        return Number::currency((int) $record->total_output_tokens * $costPerToken);
                    })
                    ->summarize(Summarizer::make('SUM')
                        ->label('Sum')->using(function ($table) {
                            // Access the records already shown in the table
                            $records = $table->getRecords();

                            $totalOutputCost = $records->sum(function ($record): int|float {
                                $costPerToken = config('llm.pricing' . $record->model_name . '.input');

                                return $record->total_output_tokens * $costPerToken;
                            });

                            return Number::currency((int) $totalOutputCost);
                        })),
            ])
            ->filters([
                SelectFilter::make('model_name')
                    ->options(function () {
                        return LlmUsage::query()
                            ->distinct('model_name')
                            ->pluck('model_name')
                            ->mapWithKeys(function ($model_name) {
                                return [$model_name => $model_name];
                            });
                    }),
                DateRangeFilter::make('created_at'),
            ])
            ->defaultSort('total_input_tokens', 'desc');
    }

    public function getTableRecordKey($record): string
    {
        return $record->model_name;
    }
}
