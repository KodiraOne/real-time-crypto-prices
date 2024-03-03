<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use App\AlphaVantage\ExchangeRate;
use App\Commodity\Rate;

class Commodity extends Model
{
    use HasFactory;

    protected $table = 'commodities';

    protected $fillable = [
        'symbol',
        'name',
    ];

    /**
     * @return HasMany<CommodityPrice>
     */
    public function rates(): HasMany
    {
        return $this->hasMany(CommodityRates::class);
    }

    public function getRateChange(float $newRate, float $oldRate): float
    {
        if ($oldRate == 0){
            return 0;
        }

        return round(($newRate - $oldRate) / $oldRate * 100, 2);
    }

    public function getRateAttribute($rate)
    {
        return (float) $rate;
    }

    public function getRateCacheKey()
    {
        return $this->id . '_latest_rate_with_change';
    }

    public function cacheRate(Rate $rate)
    {
        Cache::put(
            $this->getRateCacheKey(), 
            $rate, 
            config('market.commodity.cache_ttl')
        );
    }

    public function getCurrentRate(): Rate
    {
        // get latest price from cache or DB
        $currentRate = Cache::get($this->getRateCacheKey());
        if (!$currentRate) {

            // cache miss take from db
            $lastTwoPrices = $this->rates()->orderBy('datetime', 'DESC')->take(2)->get();

            // 3 cases are possible
            if ($lastTwoPrices->count() < 2) {
                $currentRate = match ($lastTwoPrices->count()) {
                    // #1 there is single record for this commodity
                    1 => new Rate ($lastTwoPrices->first()->rate, 0),
                    // #2 there are no records for this commodity
                    default => new Rate (0, 0),
                };
            } else {
                // #3 there are 2 records for this commodity
                $oldRate = $lastTwoPrices->pop();
                $newRate = $lastTwoPrices->pop();
                $currentRate = new Rate ($newRate->rate, $this->getRateChange($newRate->rate, $oldRate->rate));
            }

            // cache db records
            $this->cacheRate($currentRate);
        }

        return $currentRate;
    }

    public function addNewRate(ExchangeRate $newRate): void
    {
        // get latest price from cache or DB
        $oldRate = $this->getCurrentRate();

        // calculate new change in %
        $newChange = $this->getRateChange($newRate->rate, $oldRate->rate);

        // save price and change in cache
        $cacheRate = new Rate ($newRate->rate, $newChange);
        $this->cacheRate($cacheRate);

        // store new price in db
        $this->rates()->create($newRate->toArray());
    }
}