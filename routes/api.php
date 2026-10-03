<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgentController;

Route::post('/agent/chat', [AgentController::class, 'chat']);