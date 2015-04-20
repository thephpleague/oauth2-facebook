<?php

namespace League\OAuth2\Client\Provider;

use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Entity\User;
use League\OAuth2\Client\Exception\FacebookProviderException;

class Facebook extends AbstractProvider
{
    /**
     * @var string The Graph API version to use for requests.
     */
    protected $graphApiVersion;

    public $scopes = ['public_profile', 'email'];

    public $responseType = 'string';

    /**
     * @param array $options
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($options)
    {
        parent::__construct($options);

        if (!isset($options['graphApiVersion'])) {
            throw new \InvalidArgumentException('No "graphApiVersion" found in configuration values. Please set a default Graph API version.');
        }

        $this->graphApiVersion = $options['graphApiVersion'];
    }

    public function urlAuthorize()
    {
        return 'https://www.facebook.com/'.$this->graphApiVersion.'/dialog/oauth';
    }

    public function urlAccessToken()
    {
        return 'https://graph.facebook.com/'.$this->graphApiVersion.'/oauth/access_token';
    }

    public function urlUserDetails(AccessToken $token)
    {
        $fields = implode(',', [
            'id',
            'name',
            'first_name',
            'last_name',
            'email',
            'hometown',
            'bio',
            'picture.type(large){url}',
            'gender',
            'locale',
            'link',
        ]);

        return 'https://graph.facebook.com/'.$this->graphApiVersion.'/me?fields='.$fields.'&access_token='.$token;
    }

    public function userDetails($response, AccessToken $token)
    {
        $user = new User();

        $email = (isset($response->email)) ? $response->email : null;
        // The "hometown" field will only be returned if you ask for the `user_hometown` permission.
        $location = (isset($response->hometown->name)) ? $response->hometown->name : null;
        $description = (isset($response->bio)) ? $response->bio : null;
        $imageUrl = (isset($response->picture->data->url)) ? $response->picture->data->url : null;
        $gender = (isset($response->gender)) ? $response->gender : null;
        $locale = (isset($response->locale)) ? $response->locale : null;

        $user->exchangeArray([
            'uid' => $response->id,
            'name' => $response->name,
            'firstname' => $response->first_name,
            'lastname' => $response->last_name,
            'email' => $email,
            'location' => $location,
            'description' => $description,
            'imageurl' => $imageUrl,
            'gender' => $gender,
            'locale' => $locale,
            'urls' => [ 'Facebook' => $response->link ],
        ]);

        return $user;
    }

    public function userUid($response, AccessToken $token)
    {
        return $response->id;
    }

    public function userEmail($response, AccessToken $token)
    {
        return isset($response->email) && $response->email ? $response->email : null;
    }

    public function userScreenName($response, AccessToken $token)
    {
        return [$response->first_name, $response->last_name];
    }

    public function getAccessToken($grant = 'authorization_code', $params = [])
    {
        if (isset($params['refresh_token'])) {
            throw new FacebookProviderException('Facebook does not support token refreshing.');
        }

        return parent::getAccessToken($grant, $params);
    }
}
