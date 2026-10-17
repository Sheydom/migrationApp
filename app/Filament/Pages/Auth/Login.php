<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Actions\Action;

class Login extends BaseLogin
{
    public function demoLogin()
    {
        $user = User::where('email', 'demo@example.com')->firstOrFail();

        Filament::auth()->login($user);

        session()->regenerate();

        return redirect()->intended(Filament::getUrl());
    }

    protected function getFormActions(): array
    {
        return [
            parent::getAuthenticateFormAction(),

            Action::make('demoLogin')
                ->label('Explore Demo')
                ->color('gray')
                ->action('demoLogin'),
        ];
    }
}