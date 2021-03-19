<?php

namespace League\OAuth2\Client\Grant;

class FbExchangeToken extends AbstractGrant
{
    public function __toString(): string
    {
        return 'fb_exchange_token';
    }

    protected function getRequiredRequestParameters(): array
    {
        return [
            'fb_exchange_token',
        ];
    }

    protected function getName(): string
    {
        return 'fb_exchange_token';
    }
}
