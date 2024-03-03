<?php

namespace App\Http\Controllers;

use App\Models\Commodity;

class DashboardController extends Controller
{
    public function get()
    {
        return view(
            'dashboard', 
            [
                'commodities' => Commodity::all()->map(function($commodity) {
                    $commodity->currentRate = (array) $commodity->getCurrentRate();
                    return $commodity->only([
                        'name',
                        'symbol',
                        'currentRate'
                    ]);
                })
            ]
        );
    }
}