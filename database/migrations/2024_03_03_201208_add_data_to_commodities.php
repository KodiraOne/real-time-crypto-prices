<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (config('app.env') == 'testing') {
            return;
        }
        
        $commodities = [
            [
                'name' => 'Bitcoin',
                'symbol' => 'BTC',
            ],
            [
                'name' => 'Ethereum',
                'symbol' => 'ETH',
            ],
            [
                'name' => 'Solana',
                'symbol' => 'SOL',
            ],
            [
                'name' => 'Ripple',
                'symbol' => 'XRP',
            ],
            [
                'name' => 'Cardano',
                'symbol' => 'ADA',
            ],
            [
                'name' => 'Avalanche',
                'symbol' => 'AVAX',
            ],
            [
                'name' => 'Chainlink',
                'symbol' => 'LINK',
            ],
            [
                'name' => 'Polkadot',
                'symbol' => 'DOT',
            ],
            [
                'name' => 'Polygon',
                'symbol' => 'MATIC',
            ],
            [
                'name' => 'Litecoin',
                'symbol' => 'LTC',
            ]
        ];

        // add timestamps
        $commodities = collect($commodities)->map(function($item){
            $item['created_at'] = now()->toDateTimeString();
            $item['updated_at'] = now()->toDateTimeString();
            return $item;
        })->toArray();
        DB::table('commodities')->insert($commodities);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
