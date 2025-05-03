<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;

// These two routes will be accessible at /api/weather and /api/forecast
Route::get('weather',  [WeatherController::class, 'current']);
Route::get('forecast', [WeatherController::class, 'forecast']);