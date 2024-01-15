<?php

namespace Roomies\Phonable\Tests\Identification;

use Mockery;
use Roomies\Phonable\Identification\Vonage;
use Roomies\Phonable\Tests\TestCase;
use Vonage\Client;
use Vonage\Client\Exception\Request as RequestException;
use Vonage\Insights\Standard;
use Vonage\Insights\StandardCnam;

class VonageTest extends TestCase
{
    public function test_it_handle_us_number()
    {
        $identifiable = new Identifiable(
            phoneNumber: '+12125550000',
        );

        $standardCnam = new StandardCnam($identifiable->getIdentifiablePhoneNumber());
        $standardCnam->fromArray([
            'current_carrier' => [
                'name' => 'Verizon Wireless',
                'country' => 'US',
                'network_type' => 'mobile',
            ],
            'caller_name' => 'Thomas Clement',
        ]);

        $this->mockResponse(function ($insights) use ($identifiable, $standardCnam) {
            $insights->shouldReceive('standardCnam')
                ->with($identifiable->getIdentifiablePhoneNumber())
                ->andReturn($standardCnam);
        });

        $result = app(Vonage::class)->handle($identifiable);

        $this->assertEquals('Verizon Wireless', $result->carrierName);
        $this->assertEquals('Thomas Clement', $result->callerName);
    }

    public function test_it_handles_non_us_number()
    {
        $identifiable = new Identifiable(
            phoneNumber: '+61409181900',
        );

        $standard = new Standard($identifiable->getIdentifiablePhoneNumber());
        $standard->fromArray(['current_carrier' => [
            'name' => 'Verizon Wireless',
            'country' => 'US',
            'network_type' => 'mobile',
        ]]);

        $this->mockResponse(function ($insights) use ($identifiable, $standard) {
            $insights->shouldReceive('standard')
                ->with($identifiable->getIdentifiablePhoneNumber())
                ->andReturn($standard);
        });

        $result = app(Vonage::class)->handle($identifiable);

        $this->assertEquals('Verizon Wireless', $result->carrierName);
        $this->assertNull($result->callerName);
    }

    public function test_it_handles_failure()
    {
        $identifiable = new Identifiable;

        $this->mockResponse(function ($insights) use ($identifiable) {
            $insights->shouldReceive('standardCnam')
                ->with($identifiable->getIdentifiablePhoneNumber())
                ->andThrow(new RequestException);
        });

        $result = app(Vonage::class)->handle($identifiable);

        $this->assertNull($result);
    }

    protected function mockResponse(callable $callback)
    {
        $insights = Mockery::mock('stdClass', $callback);

        $this->mock(Client::class, function ($mock) use ($insights) {
            $mock->shouldReceive('insights')->andReturn($insights);
        });
    }
}
