<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgentController;

Route::redirect('/', '/admin');


Route::livewire('/register', 'pages.client-register')->name('client-register')->middleware('signed:relative');


Route::get('/debug-request', function () {
    return [
        'url' => request()->url(),
        'scheme' => request()->getScheme(),
        'secure' => request()->isSecure(),
        'host' => request()->getHost(),

        'forwarded_proto' => request()->header('X-Forwarded-Proto'),
        'forwarded_host' => request()->header('X-Forwarded-Host'),

        'server_https' => request()->server('HTTPS'),
        'server_forwarded_proto' => request()->server('HTTP_X_FORWARDED_PROTO'),
        'server_forwarded_host' => request()->server('HTTP_X_FORWARDED_HOST'),
        'remote_addr' => request()->server('REMOTE_ADDR'),
    ];
});



//Route::middleware(['auth', 'verified'])->group(function () {
//    Route::view('dashboard', 'dashboard')->name('dashboard');
//});
//
//require __DIR__ . '/settings.php';
