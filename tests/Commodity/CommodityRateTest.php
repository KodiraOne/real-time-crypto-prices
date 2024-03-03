<?php

use App\AlphaVantage\ExchangeRate;
use Tests\TestCase;
use App\Models\Commodity;
use Illuminate\Support\Facades\Cache;

class CommodityRateTest extends TestCase
{
    public function testAddNewRate()
    {
        $commodity = Commodity::create(['name' => 'Test', 'symbol' => 'TST']);

        $exhangeRate = new ExchangeRate($commodity->id, 1.23, now()->toDateTimeString());
        $oldRate = $commodity->getCurrentRate();
        $commodity->addNewRate($exhangeRate);
        $newRate = $commodity->getCurrentRate();
        $cacheRate = Cache::get($commodity->getRateCacheKey());

        // old rate should not be equal to new one
        $this->assertNotEquals($oldRate->rate, $newRate->rate);
        // new rate should be cached
        $this->assertEquals($cacheRate->rate, $newRate->rate);
        // there should be rate in db
        $this->assertEquals(1, $commodity->rates()->get()->count());
    }

    public function testCurrentRate()
    {
        $commodity = Commodity::create(['name' => 'Test', 'symbol' => 'TST']);
        $newRate = $commodity->getCurrentRate();

        // no rates yet for commodity
        $this->assertEquals(0, $newRate->rate);
        $this->assertEquals(0, $newRate->change);

        $exhangeRate = new ExchangeRate($commodity->id, 1.23, now()->toDateTimeString());
        $commodity->addNewRate($exhangeRate);
        $newRate = $commodity->getCurrentRate();

        // new rate should become current
        $this->assertEquals(1.23, $newRate->rate);
        $this->assertEquals(0, $newRate->change);
    }

    public function testRateChange()
    {
        $commodity = Commodity::create(['name' => 'Test', 'symbol' => 'TST']);

        $exhangeRate = new ExchangeRate($commodity->id, 1, now()->toDateTimeString());
        $commodity->addNewRate($exhangeRate);

        $exhangeRate = new ExchangeRate($commodity->id, 1.1, now()->toDateTimeString());
        $commodity->addNewRate($exhangeRate);

        $currentRate = $commodity->getCurrentRate();
        // we expect 10% increase
        $this->assertEquals(10, $currentRate->change);
    }
}