<?php

namespace Roomies\Phonable\Identification;

use Illuminate\Support\MultipleInstanceManager;
use Vonage\Client;

class Manager extends MultipleInstanceManager
{
    /**
     * The name of the default instance.
     */
    protected ?string $defaultInstance;

    /**
     * Get the default driver name.
     */
    public function getDefaultInstance(): string
    {
        return $this->defaultInstance ?? $this->config['phonable.identification_service'];
    }

    /**
     * Set the default instance name.
     */
    public function setDefaultInstance(string $name): void
    {
        $this->defaultInstance = $name;
    }

    /**
     * Get the instance specific configuration.
     */
    public function getInstanceConfig(string $name): array
    {
        return config("phonable.services.{$name}");
    }

    /**
     * Create an instance of the Prelude driver.
     */
    public function createPreludeDriver(array $config): Prelude
    {
        return new Prelude(
            $config['key'], $config['customer_uuid'], $this->app->make('request')->ip(),
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
