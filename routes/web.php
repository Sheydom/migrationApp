<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PythonController;

Route::view('/', 'welcome')->name('home');


Route::livewire('/register', 'pages.client-register')->name('client-register')->middleware('signed');


Route::get('/python', [PythonController::class, "create"])->name('python');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__ . '/settings.php';
