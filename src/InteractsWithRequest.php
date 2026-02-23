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

    /**
     * Get the User-Agent from the request.
     */
    protected function getUserAgent(): ?string
    {
        return $this->app['request']->userAgent();
    }
}
