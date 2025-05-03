<?php

use App\Http\Controllers\WeatherController;

Route::get('weather', [WeatherController::class, 'current']);
Route::get('forecast', [WeatherController::class, 'forecast']);

?>