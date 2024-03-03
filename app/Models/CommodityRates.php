<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommodityRates extends Model
{
    use HasFactory;

    protected $table = 'commodity_rates';

    public $timestamps = false;

    protected $fillable = [
        'commodity_id',
        'rate',
        'datetime',
    ];

    /**
     * @return BelongsTo<Commodity, Price>
     */
    public function commodity(): BelongsTo
    {
        return $this->belongsTo(Commodity::class);
    }

    public function getRateAttribute($rate): float
    {
        return (float) $rate;
    }
}