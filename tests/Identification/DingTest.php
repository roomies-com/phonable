<?php

namespace Roomies\Phonable\Tests\Identification;

use Illuminate\Support\Facades\Http;
use Roomies\Phonable\Identification\Ding;
use Roomies\Phonable\Tests\TestCase;

class DingTest extends TestCase
{
    public function test_it_handles_string()
    {
        Http::fake([
            'api.ding.live/v1/lookup/+12125550000' => Http::response([
                'carrier' => 'carrier_name',
                'country_code' => 'carrier_country',
                'line_type' => 'network_type',
            ], 200, ['content-type' => 'application/json']),
        ]);

        $result = app(Ding::class)->get('+12125550000');

        $this->assertEquals('carrier_name', $result->carrierName);
        $this->assertEquals('carrier_country', $result->carrierCountry);
        $this->assertEquals('network_type', $result->networkType);
    }

    public function test_it_handle_us_number()
    {
        $identifiable = new Identifiable(
            phoneNumber: '+12125550000',
        );

        Http::fake([
            'api.ding.live/v1/lookup/+12125550000' => Http::response([
                'carrier' => 'carrier_name',
                'country_code' => 'carrier_country',
                'line_type' => 'network_type',
            ], 200, ['content-type' => 'application/json']),
        ]);

        $result = app(Ding::class)->get($identifiable);

        $this->assertEquals('carrier_name', $result->carrierName);
        $this->assertEquals('carrier_country', $result->carrierCountry);
        $this->assertEquals('network_type', $result->networkType);
    }

    public function test_it_handles_failure()
    {
        $identifiable = new Identifiable;

        Http::fake([
            'api.ding.live/v1/lookup/+12125550000' => Http::response(null, 404),
        ]);

        $result = app(Ding::class)->get($identifiable);

        $this->assertNull($result);
    }
}
