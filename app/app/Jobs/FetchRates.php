<?php

namespace App\Jobs;

use App\Models\Commodity;
use App\Services\AlphaVantage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class FetchRates implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $alphaVantageApi = new AlphaVantage();
        foreach (Commodity::all() as $commodity) {
            $exhangeRate = $alphaVantageApi->fetchRate($commodity);
            $commodity->addNewRate($exhangeRate);
        }
    }
}
