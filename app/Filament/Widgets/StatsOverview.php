<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
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
            Stat::make('Clients', Client::query()->count())->description('Total Clients')->color('success')->extraAttributes(['class' => 'hover:scale-105 border border-transparent hover:border-amber-500 transition ease-in-out duration-200'])->url(ClientResource::getUrl('index')),
            Stat::make('Checklist', "$completed /
        $notCompleted")->description('Progress' . ' ' . $ratio . '%')->color($color)->extraAttributes(['class' => 'hover:scale-105 border border-transparent hover:border-amber-500 transition ease-in-out duration-200'])->url(ClientResource::getUrl('index', ['filters' => ['checklist' => ['isActive' => true]]])),


            Stat::make('AI Reviews', Client::query()->where('status', 'Review AI')->count())->extraAttributes(['class' => 'hover:scale-105 border border-transparent hover:border-amber-500 transition ease-in-out duration-200'])->url(ClientResource::getUrl('index', ['filters' => ['Review AI' => ['isActive' => true]]])),


            Stat::make('Expiring Visas', Client::query()->whereDate('expire_date', '<=', now()->addDays(90))->count())->description('Within the next 3 months')->descriptionIcon('heroicon-s-bolt')->color('danger')->Url(ClientResource::getUrl('index', ['filters' => ['expiring_visas' => ['isActive' => true,]]]))->extraAttributes(['class' => 'hover:scale-105 border border-transparent hover:border-amber-500 transition ease-in-out duration-200']),

        ];
    }
}
