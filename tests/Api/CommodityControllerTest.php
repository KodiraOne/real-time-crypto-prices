<?php

use Tests\TestCase;
use App\Models\Commodity;

class CommodityControllerTest extends TestCase
{
    public function getCommodities()
    {
        return $this->get('/commodities');
    }

    public function testItGetsCommodities(): void
    {
        Commodity::create(['name' => 'Test', 'symbol' => 'TST']);
        $this->getCommodities()->assertStatus(200)->assertJsonStructure([
            'commodities' => [
                '*' => [
                    'name',
                    'symbol'
                ]
            ]
        ]);
    }
}