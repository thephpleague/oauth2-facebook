<?php

namespace League\OAuth2\Client\Grant;

class FbExchangeToken extends AbstractGrant
{
    public function __toString()
    {
        return 'fb_exchange_token';
    }

    protected function getRequiredRequestParams()
    {
        return [
            'fb_exchange_token',
        ];
    }
}
