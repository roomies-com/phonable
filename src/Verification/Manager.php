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
     * Get the default instance name.
     *
     * @return string
     */
    public function getDefaultInstance()
    {
        return $this->defaultInstance
            ?? $this->app['config']->get('phonable.verification_service');
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
            $config['key'], $config['customer_uuid'], $this->app['request']->ip(),
        );
    }

    /**
     * Create an instance of the Prelude driver.
     */
    public function createPreludeDriver(array $config): Prelude
    {
        return new Prelude(
            $config['key'], $this->app['request']->ip(),
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
