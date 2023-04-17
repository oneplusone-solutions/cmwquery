<?php

namespace OnePlusOne\CMWQuery\Widgets;

use Filament\Forms\Components\DatePicker;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CMWWidget extends ApexChartWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static string $chartId = 'CMWChart';

    protected static ?string $heading = 'CMWChart';

    protected function getStartDate(): \Illuminate\Support\Carbon
    {
        switch (config('cmwquery.period')) {
            case 'week':
                return now()->subWeek();
                break;
            case 'year':
                return now()->subYear();
                break;
            case 'month':
            default:
                return now()->subMonth();
                break;

        }
    }

    protected function getOptions(): array
    {
        $start = $this->getStartDate();
        $model = config('cmwquery.model');
        $period = config('cmwquery.period');
        $data = Trend::model($model)
            ->between(
                start: $start,
                end: now(),
            );
        if ($period == 'year') {
            $data = $data
                ->perMonth()
                ->count();
        } else {
            $data = $data
                ->perDay()
                ->count();
        }

        return [
            'chart' => [
                'type' => 'line',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'TasksChart',
                    //                    'data' => [7, 4, 6, 10, 14, 7, 5, 9, 10, 15, 13, 18],
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'xaxis' => [
                //                'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                'categories' => $data->map(fn (TrendValue $value) => $value->date),
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'colors' => ['#6366f1'],
            'stroke' => [
                'curve' => 'smooth',
            ],
        ];
    }

    protected function getFormSchema(): array
    {
        $start = $this->getStartDate();

        return [
            DatePicker::make('date_start')
                 ->default($start),
            DatePicker::make('date_end')
                ->default(now()),
        ];
    }
}
