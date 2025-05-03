<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenWeatherMapService;

class WeatherController extends Controller
{
    public function current(Request $request, OpenWeatherMapService $weather)
    {
        $data = $request->validate([
            'city' => 'required|string',
        ]);

        return response()->json(
            $weather->getCurrentWeather($data['city'])
        );
    }

    public function forecast(Request $request, OpenWeatherMapService $weather)
    {
        $data = $request->validate([
            'city' => 'required|string',
        ]);

        return response()->json(
            $weather->getForecast($data['city'])
        );
    }
}
