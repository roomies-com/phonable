<?php

namespace Roomies\Phonable\Verification;

use Illuminate\Support\MultipleInstanceManager;

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
        return $this->defaultInstance ?? $this->config['phonable.verification_service'];
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
     * Create an instance of the Twilio driver.
     */
    public function createTwilioDriver(array $config): Twilio
    {
        return new Twilio(
            $config['account_sid'], $config['auth_token'], $config['service_sid']
        );
    }

    /**
     * Create an instance of the Vonage driver.
     */
    public function createVonageDriver(array $config): Vonage
    {
        return new Vonage($config['key'], $config['secret']);
    }
}
