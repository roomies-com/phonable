<?php

namespace Roomies\Phonable\Tests\Identification;

use Illuminate\Support\Facades\Http;
use Roomies\Phonable\Identification\Prelude;
use Roomies\Phonable\Tests\TestCase;

class PreludeTest extends TestCase
{
    public function test_it_handles_string(): void
    {
        Http::fake([
            'api.prelude.dev/v2/lookup/+12125550000' => Http::response([
                'country_code' => 'carrier_country',
                'network_info' => [
                    'carrier_name' => 'carrier_name',
                ],
                'line_type' => 'network_type',
            ], 200, ['content-type' => 'application/json']),
        ]);

        $result = app(Prelude::class)->get('+12125550000');

        $this->assertEquals('carrier_name', $result->carrierName);
        $this->assertEquals('carrier_country', $result->carrierCountry);
        $this->assertEquals('network_type', $result->networkType);
    }

    public function test_it_handle_us_number(): void
    {
        $identifiable = new Identifiable(
            phoneNumber: '+12125550000',
        );

        Http::fake([
            'api.prelude.dev/v2/lookup/+12125550000' => Http::response([
                'country_code' => 'carrier_country',
                'network_info' => [
                    'carrier_name' => 'carrier_name',
                ],
                'line_type' => 'network_type',
            ], 200, ['content-type' => 'application/json']),
        ]);

        $result = app(Prelude::class)->get($identifiable);

        $this->assertEquals('carrier_name', $result->carrierName);
        $this->assertEquals('carrier_country', $result->carrierCountry);
        $this->assertEquals('network_type', $result->networkType);
    }

    public function test_it_handles_failure(): void
    {
        $identifiable = new Identifiable;

        Http::fake([
            'api.prelude.dev/v2/lookup/+12125550000' => Http::response(null, 404),
        ]);

        $result = app(Prelude::class)->get($identifiable);

        $this->assertNull($result);
    }
}
