<?php

use Livewire\Component;
use App\Models\User;
use Filament\Facades\Filament;

new class extends Component
{
    public function login(){
        $user= User::where('email','demo@example.com')->firstOrFail();
        Filament::auth()->login($user);
        session()->regenerate();
        return redirect()->to('/admin');
    }
   
};
?>

<div>
    {{-- It is not the man who has too little, but the man who craves more, that is poor. - Seneca --}}
</div>