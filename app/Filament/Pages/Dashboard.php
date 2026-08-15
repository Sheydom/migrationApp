<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\URL;

class Dashboard extends BaseDashboard
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateRegisterLink')
                ->label('Generate Register Link')
                ->icon('heroicon-o-link')
                ->color('primary')
                ->action(function (): void {
                    $relativeUrl = URL::temporarySignedRoute(
                        'client-register',
                        now()->addWeek(),
                        absolute: false,
                    );
                    $url = rtrim(config('app.url'),'/') . $relativeUrl;

                    Notification::make()
                        ->title('Registration link generated')
                        ->body($url)
                        ->success()
                        ->persistent()
                        ->send();
                }),
        ];
    }
}
