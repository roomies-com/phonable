<?php

namespace Roomies\Phonable;

trait InteractsWithRequest
{
    /**
     * Attempt to get the IP address from the request.
     */
    protected function getIpAddress(): ?string
    {
        return $this->app['request']->header('CF_CONNECTING_IP')
            ?: $this->app['request']->ip();
    }
}
