<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Client;
use App\Models\ChecklistItem;
use phpDocumentor\Reflection\PseudoTypes\FloatValue;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $completed = ChecklistItem::query()->where('is_completed', true)->count();
        $notCompleted = ChecklistItem::query()->where('is_completed', false)->count();
        $ratio = round($completed / $notCompleted * 100, 1);
        if ($ratio < 30) {
            $color = 'danger';
        } elseif ($ratio < 50) {
            $color = 'warning';
        } else {
            $color = 'success';
        }


        return [
            Stat::make('Clients', Client::query()->count())->description('Total Clients')->color('success'),
            Stat::make('Checklist', "$completed /
        $notCompleted")->description('Process' . ' ' . $ratio . '%')->color($color),
            Stat::make('Nationalities', Client::query()->distinct()->count('nationality')),

        ];
    }
}
