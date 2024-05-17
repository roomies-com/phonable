<?php

namespace Roomies\Phonable\Identification;

use Illuminate\Support\Manager as BaseManager;
use Vonage\Client;

class Manager extends BaseManager
{
    /**
     * Get the default driver name.
     */
    public function getDefaultDriver(): string
    {
        return $this->config['phonable.identification.default'];
    }

    /**
     * Create an instance of the Prelude driver.
     */
    public function createPreludeDriver(): Prelude
    {
        return new Prelude(
            $this->config['phonable.services.prelude.key'],
            $this->config['phonable.services.prelude.customer_uuid'],
            $this->getContainer()->make('request')->ip(),
        );
    }

    /**
     * Create an instance of the Vonage driver.
     */
    public function createVonageDriver(): Vonage
    {
        return new Vonage($this->getContainer()->make(Client::class));
    }
}
