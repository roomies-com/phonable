<?php

namespace Roomies\Phonable\Verification;

use Illuminate\Support\Manager as BaseManager;

class Manager extends BaseManager
{
    /**
     * Get the default driver name.
     */
    public function getDefaultDriver(): string
    {
        return $this->config['phonable.verification.default'];
    }

    /**
     * Create an instance of the Ding driver.
     */
    public function createDingDriver(): Ding
    {
        return new Ding(
            $this->config['phonable.services.ding.key'],
            $this->config['phonable.services.ding.customer_uuid'],
            $this->getContainer()->make('request')->ip(),
        );
    }

    /**
     * Create an instance of the Twilio driver.
     */
    public function createTwilioDriver(): Twilio
    {
        return new Twilio(
            $this->config['phonable.services.twilio.account_sid'],
            $this->config['phonable.services.twilio.auth_token'],
            $this->config['phonable.services.twilio.service_sid']
        );
    }

    /**
     * Create an instance of the Vonage driver.
     */
    public function createVonageDriver(): Vonage
    {
        return new Vonage(
            $this->config['phonable.services.vonage.key'],
            $this->config['phonable.services.vonage.secret']
        );
    }
}
