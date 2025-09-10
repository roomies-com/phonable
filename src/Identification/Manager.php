<?php

namespace Roomies\Phonable\Identification;

use Illuminate\Support\MultipleInstanceManager;
use Roomies\Phonable\InteractsWithRequest;
use Vonage\Client;

class Manager extends MultipleInstanceManager
{
    use InteractsWithRequest;

    /**
     * The name of the default instance.
     */
    protected ?string $defaultInstance;

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultInstance()
    {
        return $this->defaultInstance
            ?? $this->app['config']->get('phonable.identification_service');
    }

    /**
     * Set the default instance name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultInstance($name)
    {
        $this->defaultInstance = $name;
    }

    /**
     * Get the instance specific configuration.
     *
     * @param  string  $name
     * @return array
     */
    public function getInstanceConfig($name)
    {
        return $this->app['config']->get("phonable.services.{$name}");
    }

    /**
     * Create an instance of the Prelude driver.
     */
    public function createDingDriver(array $config): Ding
    {
        return new Ding(
            $config['key'], $config['customer_uuid'], $this->getIpAddress(),
        );
    }

    /**
     * Create an instance of the Prelude driver.
     */
    public function createPreludeDriver(array $config): Prelude
    {
        return new Prelude(
            $config['key'], $this->getIpAddress(),
        );
    }

    /**
     * Create an instance of the Vonage driver.
     */
    public function createVonageDriver(array $config): Vonage
    {
        return new Vonage($this->app->make(Client::class));
    }
}
