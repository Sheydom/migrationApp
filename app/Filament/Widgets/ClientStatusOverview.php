<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Clients\ClientResource;
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
            Stat::make('New', Client::query()->where('status', 'New')->count())->description('New Clients')->color('info')->extraAttributes(['class' => 'hover:scale-105 border border-transparent hover:border-amber-500 transition ease-in-out duration-200'])->url(ClientResource::getUrl('index', ['filters' => ['new' => ['isActive' => true,]]])),
            Stat::make('Pending', Client::query()->where('status', 'Pending')->count())->description('Requested Information')->color('info')->extraAttributes(['class' => 'hover:scale-105 border border-transparent hover:border-amber-500 transition ease-in-out duration-200'])->url(ClientResource::getUrl('index', ['filters' => ['pending' => ['isActive' => true]]])),
            Stat::make('In Progress', Client::query()->where('status', 'In Progress')->count())->description('Active Applications')->color('info')->extraAttributes(['class' => 'hover:scale-105 border border-transparent hover:border-amber-500 transition ease-in-out duration-200'])->url(ClientResource::getUrl('index', ['filters' => ['In Progress' => ['isActive' => true]]])),
            Stat::make('Closed', Client::query()->where('status', 'Closed')->count())->description('Completed Clients')->descriptionIcon(Heroicon::OutlinedCheckCircle)->color('success')->extraAttributes(['class' => 'hover:scale-105 border border-transparent hover:border-amber-500 transition ease-in-out duration-200'])->url(ClientResource::getUrl('index', ['filters' => ['closed' => ['isActive' => true,]]])),
        ];
    }
}
