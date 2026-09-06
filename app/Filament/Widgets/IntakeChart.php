<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Client;
use Carbon\Carbon;

class IntakeChart extends ChartWidget
{
    protected int | string | array $columnSpan = 1;
    protected static ?int $sort = 3;


    public function getHeading(): string
    {
        $year = now()->year;
        return 'Client Intake' . " " . $year;
    }

    protected function getData(): array
    {
        $clientsPerMonth = Client::query()->selectRaw('MONTH(created_at) as month, count(*) as total')->groupBy('month')->pluck('total', 'month');

        $data = [];
        for ($month = 1; $month <= 12; $month++) {
            $data[] = $clientsPerMonth[$month] ?? 0;
        }
        return [
            'datasets' => [
                [
                    'label' => 'Client Intakes',
                    'data' => $data,
                ],
            ],

            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],];

    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ], 'title' => [
                        'display' => 'true',
                        'text' => 'Monthly Client Intake',
                    ]
                ],

            ],

            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
