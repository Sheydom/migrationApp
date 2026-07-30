<?php

use App\Models\Client;
use Livewire\Component;

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


    public function save(): void
    {
        $validated = $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
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
        <label for="first_name">First Name<input id="first_name" type="text" wire:model="first_name"></label>
        <label for="last_name">Last Name<input id="last_name" type="text" wire:model="last_name"></label>
        <label for="nationality">Nationality<input id="nationality" type="text" wire:model="nationality"></label>
        <label for="email">Email<input id="email" type="email" wire:model="email"></label>
        <label for="phone">Phone<input id="phone" type="tel" wire:model="phone"></label>
        <button type="submit">Submit</button>
    </form>
</div>
