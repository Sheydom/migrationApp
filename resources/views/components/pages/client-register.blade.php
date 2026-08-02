<?php

use App\Models\Client;
use Livewire\Component;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

new class extends Component {
    public function render()
    {
        return $this->view()->layout('layouts::client');
    }


    public string $first_name;
    public string $last_name;
    public string $email;
    public string $phone;
    public string $nationality;
    public string $email_confirmation;
    public string $signedUrl;

    public function mount(): void
    {
        $this->signedUrl = request()->fullUrl();
    }

    public function save(): void
    {
        $signedRequest = Request::create($this->signedUrl);
        if (!URL::hasValidSignature($signedRequest)) {
            abort(403, 'This registration link has expired or is invalid.');
        }


        $validated = $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email|confirmed',
            'phone' => 'required|string|max:30',
            'nationality' => 'required|string|max:50',
        ]);

        Client::create($validated);
//        $this->redirect('pages/thank-you');
    }

};
?>

<div>
    <h1>Client Intake Form</h1>
    <form wire:submit="save">
        <label for="first_name">First Name<input id="first_name" type="text" wire:model="first_name" required></label>
        @error('first_name')
        {{ $message }}
        @enderror
        <label for="last_name">Last Name<input id="last_name" type="text" wire:model="last_name" required></label>
        @error('last_name')
        {{ $message }}
        @enderror
        <label for="nationality">Nationality<input id="nationality" type="text" wire:model="nationality"
                                                   required></label>
        @error('nationality')
        {{ $message }}
        @enderror
        <label for="email">Email<input id="email" type="email" wire:model="email" required></label>
        @error('email')
        {{ $message }}
        @enderror
        <label for="email_confirmation">Confirm Email Address<input id="email_confirmation" type="email"
                                                                    wire:model="email_confirmation" required></label>
        @error('email_confirmation')
        {{ $message }}
        @enderror
        <label for="phone">Phone<input id="phone" type="tel" wire:model="phone" required></label>
        @error('phone')
        {{ $message }}
        @enderror
        <button type="submit">Submit</button>
    </form>
</div>
