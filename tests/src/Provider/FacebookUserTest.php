<?php

namespace League\OAuth2\Client\Test\Provider;

use League\OAuth2\Client\Provider\FacebookUser;
use PHPUnit\Framework\TestCase;

class FacebookUserTest extends TestCase
{
    /**
     * @var FacebookUser
     */
    protected $user;

    protected function setUp()
    {
        $this->user = new FacebookUser([
            'id' => '4',
            'picture' => ['data' => ['is_silhouette' => true, 'url' => 'foo.com/pic.jpg']],
            'first_name' => 'Mark',
            'last_name' => 'Zuck',
            'foo' => 'bar',
            'age_range' => ['min' => 21],
        ]);
    }

    public function testMinAndMaxAgeReturnAgeOrNull()
    {
        $this->assertEquals(21, $this->user->getMinAge());
        $this->assertNull($this->user->getMaxAge());
    }

    public function testGettersReturnNullWhenNoKeyExists()
    {
        $this->assertEquals('4', $this->user->getId());
        $this->assertNull($this->user->getGender());
    }

    public function testProperlyMutatesPhotoUrls()
    {
        $this->assertEquals('foo.com/pic.jpg', $this->user->getPictureUrl());
    }

    public function testCanGetAllDataBackAsAnArray()
    {
        $data = $this->user->toArray();

        $expectedData = [
          'id' => '4',
          'picture' => ['data' => ['is_silhouette' => true, 'url' => 'foo.com/pic.jpg']],
          'first_name' => 'Mark',
          'last_name' => 'Zuck',
          'foo' => 'bar',
          'picture_url' => 'foo.com/pic.jpg',
          'is_silhouette' => true,
          'age_range' => ['min' => 21],
        ];

        $this->assertEquals($expectedData, $data);
    }
}
