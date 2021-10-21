<?php

namespace League\OAuth2\Client\Test\Provider;

use InvalidArgumentException;
use Mockery as m;
use League\OAuth2\Client\Provider\Facebook;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\Exception\FacebookProviderException;
use PHPUnit\Framework\TestCase;

class FooFacebookProvider extends Facebook
{
    protected function fetchResourceOwnerDetails(AccessToken $token)
    {
        return json_decode('{"id": 12345, "name": "mock_name", "username": "mock_username", "first_name": "mock_first_name", "last_name": "mock_last_name", "email": "mock_email", "Location": "mock_home", "link": "mock_facebook_url"}', true);
    }
}

class FacebookTest extends TestCase
{
    /**
     * @const string The version of the Graph API we want to use for tests.
     */
    protected const GRAPH_API_VERSION = 'v7.0';

    /**
     * @var Facebook
     */
    protected $provider;

    protected function setUp(): void
    {
        $this->provider = new Facebook([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
            'graphApiVersion' => static::GRAPH_API_VERSION,
        ]);
    }

    public function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }

    public function testAuthorizationUrl(): void
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        self::assertArrayHasKey('client_id', $query);
        self::assertArrayHasKey('redirect_uri', $query);
        self::assertArrayHasKey('state', $query);
        self::assertArrayHasKey('scope', $query);
        self::assertArrayHasKey('response_type', $query);
        self::assertArrayHasKey('approval_prompt', $query);
        self::assertNotNull($this->provider->getState());
    }

    public function testGetBaseAccessTokenUrl(): void
    {
        $url = $this->provider->getBaseAccessTokenUrl([]);
        $uri = parse_url($url);
        $graphVersion = static::GRAPH_API_VERSION;

        self::assertEquals('/'.$graphVersion.'/oauth/access_token', $uri['path']);
    }

    public function testGraphApiVersionCanBeCustomized(): void
    {
        $graphVersion = 'v13.37';
        $provider = new Facebook([
            'graphApiVersion' => $graphVersion,
            'clientSecret' => 'mock_secret',
        ]);
        $fooToken = new AccessToken(['access_token' => 'foo_token']);

        $urlAuthorize = $provider->getBaseAuthorizationUrl();
        $urlAccessToken = $provider->getBaseAccessTokenUrl([]);
        $urlUserDetails = parse_url($provider->getResourceOwnerDetailsUrl($fooToken), PHP_URL_PATH);

        self::assertEquals('https://www.facebook.com/'.$graphVersion.'/dialog/oauth', $urlAuthorize);
        self::assertEquals('https://graph.facebook.com/'.$graphVersion.'/oauth/access_token', $urlAccessToken);
        self::assertEquals('/'.$graphVersion.'/me', $urlUserDetails);
    }

    public function testResourceOwnerDetailsUrlHasDefaultFields(): void
    {
        $fooToken = new AccessToken(['access_token' => 'foo_token']);

        $fields = [
            'id', 'name', 'first_name', 'last_name',
            'email', 'hometown', 'picture.type(large){url,is_silhouette}',
            'gender', 'age_range'
        ];
        $urlUserDetails = parse_url($this->provider->getResourceOwnerDetailsUrl($fooToken), PHP_URL_QUERY);
        $urlParts = explode('&', $urlUserDetails);
        $urlFieldsPart = $urlParts[0];

        self::assertEquals('fields=' . implode(',', $fields), $urlFieldsPart);
    }

    public function testResourceOwnerDetailsUrlCanUseCustomizedFields(): void
    {
        $graphVersion = static::GRAPH_API_VERSION;
        $fooToken = new AccessToken(['access_token' => 'foo_token']);
        $fields = ['id', 'name', 'first_name', 'last_name', 'email'];
        $provider = new Facebook([
            'graphApiVersion' => $graphVersion,
            'clientSecret' => 'mock_secret',
            'fields' => $fields
        ]);

        $urlUserDetails = parse_url($provider->getResourceOwnerDetailsUrl($fooToken), PHP_URL_QUERY);
        $urlParts = explode('&', $urlUserDetails);
        $urlFieldsPart = $urlParts[0];

        self::assertEquals('fields=' . implode(',', $fields), $urlFieldsPart);
    }

    public function testGraphApiVersionWillFallbackToDefault(): void
    {
        $graphVersion = static::GRAPH_API_VERSION;
        $fooToken = new AccessToken(['access_token' => 'foo_token']);

        $urlAuthorize = $this->provider->getBaseAuthorizationUrl();
        $urlAccessToken = $this->provider->getBaseAccessTokenUrl([]);
        $urlUserDetails = parse_url($this->provider->getResourceOwnerDetailsUrl($fooToken), PHP_URL_PATH);

        self::assertEquals('https://www.facebook.com/'.$graphVersion.'/dialog/oauth', $urlAuthorize);
        self::assertEquals('https://graph.facebook.com/'.$graphVersion.'/oauth/access_token', $urlAccessToken);
        self::assertEquals('/'.$graphVersion.'/me', $urlUserDetails);
    }

    public function testGraphApiVersionWillCheckFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $graphVersion = '2.4';
        $provider = new Facebook([
            'graphApiVersion' => $graphVersion,
            'clientSecret' => 'mock_secret',
        ]);
    }

    public function testTheBetaTierCanBeEnabled(): void
    {
        $provider = new Facebook([
            'graphApiVersion' => 'v0.0',
            'clientSecret' => 'mock_secret',
            'enableBetaTier' => true,
        ]);
        $fooToken = new AccessToken(['access_token' => 'foo_token']);

        $urlAuthorize = parse_url($provider->getBaseAuthorizationUrl(), PHP_URL_HOST);
        $urlAccessToken = parse_url($provider->getBaseAccessTokenUrl([]), PHP_URL_HOST);
        $urlUserDetails = parse_url($provider->getResourceOwnerDetailsUrl($fooToken), PHP_URL_HOST);

        self::assertEquals('www.beta.facebook.com', $urlAuthorize);
        self::assertEquals('graph.beta.facebook.com', $urlAccessToken);
        self::assertEquals('graph.beta.facebook.com', $urlUserDetails);
    }

    public function testGetAccessToken(): void
    {
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getHeader')
            ->times(1)
            ->andReturn('application/json');
        $response->shouldReceive('getBody')
            ->times(1)
            ->andReturn('{"access_token":"mock_access_token","token_type":"bearer","expires_in":3600}');

        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(1)->andReturn($response);
        $this->provider->setHttpClient($client);

        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);

        self::assertEquals('mock_access_token', $token->getToken());
        self::assertLessThanOrEqual(time() + 3600, $token->getExpires());
        self::assertGreaterThanOrEqual(time(), $token->getExpires());
        self::assertNull($token->getRefreshToken(), 'Facebook does not support refresh tokens. Expected null.');
        self::assertNull(
            $token->getResourceOwnerId(),
            'Facebook does not return user ID with access token. Expected null.'
        );
    }

    public function testCanGetALongLivedAccessTokenFromShortLivedOne(): void
    {
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getHeader')
            ->times(1)
            ->andReturn('application/json');
        $response->shouldReceive('getBody')
            ->times(1)
            ->andReturn('{"access_token":"long-lived-token","token_type":"bearer","expires_in":3600}');

        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(1)->andReturn($response);
        $this->provider->setHttpClient($client);

        $token = $this->provider->getLongLivedAccessToken('short-lived-token');

        self::assertEquals('long-lived-token', $token->getToken());
    }

    public function testTryingToRefreshAnAccessTokenWillThrow(): void
    {
        $this->expectException(FacebookProviderException::class);

        $this->provider->getAccessToken('foo', ['refresh_token' => 'foo_token']);
    }

    public function testScopes(): void
    {
        self::assertEquals(['public_profile', 'email'], $this->provider->getDefaultScopes());
    }

    public function testUserData(): void
    {
        $provider = new FooFacebookProvider([
          'graphApiVersion' => static::GRAPH_API_VERSION,
        ]);

        $token = m::mock('League\OAuth2\Client\Token\AccessToken');
        $user = $provider->getResourceOwner($token);

        self::assertEquals(12345, $user->getId());
        self::assertEquals('mock_name', $user->getName());
        self::assertEquals('mock_first_name', $user->getFirstName());
        self::assertEquals('mock_last_name', $user->getLastName());
        self::assertEquals('mock_email', $user->getEmail());
    }

    public function testNotSettingADefaultGraphApiVersionWillThrow(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Facebook([
          'clientId' => 'mock_client_id',
          'clientSecret' => 'mock_secret',
          'redirectUri' => 'none',
        ]);
    }

    public function testOldVersionsOfGraphWillParseStringResponse(): void
    {
        $provider = new Facebook([
          'clientId' => 'mock_client_id',
          'clientSecret' => 'mock_secret',
          'redirectUri' => 'none',
          'graphApiVersion' => 'v2.2',
        ]);

        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getHeader')
                 ->times(1)
                 ->andReturn('application/x-www-form-urlencoded');
        $response->shouldReceive('getBody')
                 ->times(1)
                 ->andReturn('access_token=mock_access_token&expires=3600&refresh_token=mock_refresh_token');

        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(1)->andReturn($response);
        $provider->setHttpClient($client);

        $token = $provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);

        self::assertEquals('mock_access_token', $token->getToken());
        self::assertLessThanOrEqual(time() + 3600, $token->getExpires());
        self::assertGreaterThanOrEqual(time(), $token->getExpires());
        self::assertEquals('mock_refresh_token', $token->getRefreshToken());
    }

    public function testProperlyHandlesErrorResponses(): void
    {
        $postResponse = m::mock('Psr\Http\Message\ResponseInterface');
        $postResponse->shouldReceive('getHeader')
                 ->times(1)
                 ->andReturn('application/json');
        $postResponse->shouldReceive('getBody')
                     ->times(1)
                     ->andReturn('{"error":{"message":"Foo auth error","type":"OAuthException","code":191}}');

        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(1)->andReturn($postResponse);
        $this->provider->setHttpClient($client);

        $errorMessage = '';
        $errorCode = 0;

        try {
            $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
        } catch (IdentityProviderException $e) {
            $errorMessage = $e->getMessage();
            $errorCode = $e->getCode();
        }

        self::assertEquals('OAuthException: Foo auth error', $errorMessage);
        self::assertEquals(191, $errorCode);
    }

    public function testAnAppSecretProofWillBeAppendedToRequestUrl(): void
    {
        $provider = new Facebook([
            'graphApiVersion' => 'v0.0',
            'clientSecret' => 'foo_secret',
        ]);
        $fooToken = new AccessToken(['access_token' => 'foo_token']);

        self::assertStringContainsString(
            '&appsecret_proof=df4256903ba4e23636cc142117aa632133d75c642bd2a68955be1443bd14deb9',
            $provider->getResourceOwnerDetailsUrl($fooToken)
        );
    }

    public function testGetResourceOwnerDetailsForApiVersionLessThan28(): void
    {
        $provider = new Facebook([
            'graphApiVersion' => 'v2.7',
            'clientSecret' => 'foo_secret',
        ]);
        $fooToken = new AccessToken(['access_token' => 'foo_token']);

        self::assertStringContainsString('bio', $provider->getResourceOwnerDetailsUrl($fooToken));

        $provider = new Facebook([
            'graphApiVersion' => 'v2.6',
            'clientSecret' => 'foo_secret',
        ]);
        $fooToken = new AccessToken(['access_token' => 'foo_token']);

        self::assertStringContainsString('bio', $provider->getResourceOwnerDetailsUrl($fooToken));
    }

    public function testGetResourceOwnerDetailsForApiVersion28OrHigher(): void
    {
        $provider = new Facebook([
            'graphApiVersion' => 'v2.8',
            'clientSecret' => 'foo_secret',
        ]);
        $fooToken = new AccessToken(['access_token' => 'foo_token']);

        self::assertStringNotContainsString('bio', $provider->getResourceOwnerDetailsUrl($fooToken));

        $provider = new Facebook([
            'graphApiVersion' => 'v2.9',
            'clientSecret' => 'foo_secret',
        ]);
        $fooToken = new AccessToken(['access_token' => 'foo_token']);

        self::assertStringNotContainsString('bio', $provider->getResourceOwnerDetailsUrl($fooToken));
    }

    public function testGetResourceOwnerDetailsForApiVersion210OrHigher(): void
    {
        $provider = new Facebook([
            'graphApiVersion' => 'v2.10',
            'clientSecret' => 'foo_secret',
        ]);
        $fooToken = new AccessToken(['access_token' => 'foo_token']);

        self::assertStringNotContainsString('bio', $provider->getResourceOwnerDetailsUrl($fooToken));
    }
}
