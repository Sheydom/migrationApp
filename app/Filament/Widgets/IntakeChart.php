<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Client;

class IntakeChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected ?string $heading = 'Intake Chart';
    protected ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => false,
            ]
        ]
    ];

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
}
