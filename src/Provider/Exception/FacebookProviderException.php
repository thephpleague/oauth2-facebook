<?php

namespace League\OAuth2\Client\Provider\Exception;

class FacebookProviderException extends IdentityProviderException
{
    /**
     * @param string $message
     * @param int $code
     * @param mixed $response The response body
     */
    public function __construct($message, $code = 0, $response = null)
    {
        parent::__construct($message, $code, $response);
    }
}
