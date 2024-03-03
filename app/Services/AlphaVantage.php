<?php

namespace App\Services;

use App\AlphaVantage\ExchangeRate;
use App\Models\Commodity;
use Illuminate\Support\Facades\Http;

class AlphaVantage
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.alpha_vantage.base_url');
    }
    private function addApiKeyToUrl(string $url): string
    {
        return $url . "&apikey=" . config("services.alpha_vantage.api_key");
    }

    public function getFetchRateUrl(string $symbol, string $currency)
    {
        $url = "{$this->baseUrl}/query?function=CURRENCY_EXCHANGE_RATE&from_currency={$symbol}&to_currency={$currency}";

        return $this->addApiKeyToUrl($url);
    }

    public function fetchRate(Commodity $commodity, string $currency = 'EUR'): ExchangeRate
    {
        $url = $this->getFetchRateUrl($commodity->symbol, $currency);
        $response = Http::get($url);
        $error = $response->json("Error Message");
        if ($response->ok() && !$error) {
            $responseData = $response->json("Realtime Currency Exchange Rate");
            if (!$responseData) {
                throw new \Exception("Response format has changed! It doesnt contain 'Realtime Currency Exchange Rate' key anymore. New format:" . $response->body());
            }

            foreach ($responseData as $key => $value) {
                if (str_contains($key, 'Exchange Rate')) {
                    $rate = round($value, 4);
                }
                if (str_contains($key, 'Last Refreshed')) {
                    $datetime = $value;
                }
            }

            return new ExchangeRate($commodity->id, $rate, $datetime);

            throw new \Exception("Response format has changed! It doesnt contain 'Exchange Rate' key anymore. New format:" . $response->body());
        } else {
            throw new \Exception("Alpha vantage api issue! Response status: " . $response->status() . " Body: " . $response->body());
        }
    }
}