<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServerController;

Route::get('/metrics', [ServerController::class, 'api'])
    ->middleware('throttle:60,1');
    