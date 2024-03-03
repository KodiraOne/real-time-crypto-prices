<?php

use Tests\TestCase;
use App\Models\Commodity;
use Illuminate\Support\Facades\Http;
use App\Services\AlphaVantage;

class AlphaVantageApiTest extends TestCase
{
    public function testSuccessResponse(): void
    {
        $commodity = Commodity::create([
            'name' => 'Bitcoin',
            'symbol' => 'BTC'
        ]);
        $api = new AlphaVantage();

        $response = '{
            "Realtime Currency Exchange Rate": {
                "1. From_Currency Code": "BTC",
                "2. From_Currency Name": "Bitcoin",
                "3. To_Currency Code": "EUR",
                "4. To_Currency Name": "United States Dollar",
                "5. Exchange Rate": "62763.78000000",
                "6. Last Refreshed": "2024-03-03 18:17:04",
                "7. Time Zone": "UTC",
                "8. Bid Price": "62763.78000000",
                "9. Ask Price": "62763.79000000"
            }
        }';

        Http::fake([
            $api->getFetchRateUrl($commodity->symbol, 'EUR') => Http::response($response, 200)
        ]);


        $rate = $api->fetchRate($commodity, 'EUR');

        $this->assertEquals(62763.78, $rate->rate);
    }

    public function testErrorResponse(): void
    {
        $commodity = Commodity::create([
            'name' => 'Bitcoin',
            'symbol' => 'BTC'
        ]);
        $api = new AlphaVantage();

        $response = '{
            "Error Message": "Invalid API call"
        }';

        Http::fake([
            $api->getFetchRateUrl($commodity->symbol, 'EUR') => Http::response($response, 200)
        ]);

        $this->expectException(Exception::class);
        $api->fetchRate($commodity, 'EUR');
    }
}