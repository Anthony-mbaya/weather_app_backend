<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\OpenWeatherMapException;

class OpenWeatherMapService
{
    protected string $key;
    protected string $baseUrl;

    public function __construct()
    {
        $this->key     = config('services.openweathermap.key');
        $this->baseUrl = config('services.openweathermap.base_url');

        if (! $this->key) {
            throw new OpenWeatherMapException('OpenWeatherMap API key not configured.');
        }
    }

    /**
     * Get current weather for a city, cached for 5 minutes.
     */
    public function getCurrentWeather(string $city): array
    {
        return Cache::remember("weather.current.{$city}", 300, fn() => $this->callApi('weather', ['q' => $city]));
    }

    /**
     * Get 5-day/3-hour forecast for a city, cached for 10 minutes.
     */
    public function getForecast(string $city): array
    {
        return Cache::remember("weather.forecast.{$city}", 600, fn() => $this->callApi('forecast', ['q' => $city]));
    }

    /**
     * Internal helper to perform the HTTP call with retries.
     */
    protected function callApi(string $endpoint, array $params): array
    {
        $response = retry(3, fn() => Http::timeout(5)
            ->get($this->baseUrl . $endpoint, array_merge($params, [
                'appid' => $this->key,
                'units' => 'metric',
            ])),
            100
        );

        if (! $response->successful()) {
            $this->handleError($response->status());
        }

        return $response->json();
    }

    /**
     * Map HTTP status codes to our custom exception.
     */
    protected function handleError(int $status): void
    {
        match ($status) {
            401 => throw new OpenWeatherMapException('Invalid API key.'),
            429 => throw new OpenWeatherMapException('Rate limit exceeded.'),
            default => throw new OpenWeatherMapException("OpenWeatherMap API error: HTTP {$status}."),
        };
    }
}
