<?php

namespace App\Http\Controllers;

use App\Models\Commodity;

class CommodityController extends Controller
{
    public function get()
    {
        return response()->json([
            'commodities' => Commodity::all()->map(function($commodity) {
                $commodity->currentRate = $commodity->getCurrentRate();
                return collect($commodity->only([
                    'name',
                    'symbol',
                    'currentRate'
                ]));
            })
        ]);
    }
}