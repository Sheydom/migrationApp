<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientStatusOverview extends StatsOverviewWidget
{

    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        return [
            Stat::make('New', Client::query()->where('status', 'New')->count())->description('New Clients')->color('info'),
            Stat::make('Pending', Client::query()->where('status', 'Pending')->count())->description('Requested Information')->color('info'),
            Stat::make('In Progress', Client::query()->where('status', 'In Progress')->count())->description('Active Applications')->color('info'),
            Stat::make('Completed', Client::query()->where('status', 'Completed')->count())->description('Completed Clients')->descriptionIcon(Heroicon::OutlinedCheckCircle)->color('success'),
        ];
    }
}
