<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Client;
use App\Models\ChecklistItem;
use Illuminate\Support\Facades\URL;
use Filament\Actions\Action;

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
        $notCompleted")->description('Progress' . ' ' . $ratio . '%')->color($color),
            Stat::make('Nationalities', Client::query()->distinct()->count('nationality')),
            Stat::make('Expiring Visas', Client::query()->whereDate('expire_date', '<=', now()->addDays(90))->count())->description('Within the next 3 months')->descriptionIcon('heroicon-s-bolt')->color('danger'),
            Stat::make('Client registration', 'Open form')
                ->description('Link valid for 1 minute')
                ->descriptionIcon('heroicon-o-link')
                ->url(
                    fn(): string => URL::temporarySignedRoute(
                        'client-register',
                        now()->addMinute()
                    ),
                    shouldOpenInNewTab: true,
                ),

        ];
    }
}
