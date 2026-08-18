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
                        ->body($url)->actions([
                            Action::make('Copy Link')->button()->icon('heroicon-o-document')->color('primary')->alpineClickHandler(
                            // Copy to clipboard and show tooltip.
                                "window.navigator.clipboard.writeText( '$url' ); \$tooltip('Copied to clipboard', { timeout: 1500 });"
                            ),
                        ])
                        ->success()
                        ->persistent()
                        ->send();
                }),
        ];
    }
}
