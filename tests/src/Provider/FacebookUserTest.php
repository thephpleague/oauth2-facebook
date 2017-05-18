<?php

namespace League\OAuth2\Client\Test\Provider;

use League\OAuth2\Client\Provider\FacebookUser;

class FacebookUserTest extends \PHPUnit_Framework_TestCase
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
            'cover' => ['id' => '123', 'source' => 'foo.com/cover.jpg'],
            'first_name' => 'Mark',
            'last_name' => 'Zuck',
            'foo' => 'bar',
            'timezone' => '-8',
            'age_range' => ['min' => 21],
            'birthday' => '10/10/1992'
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
        $this->assertEquals('foo.com/cover.jpg', $this->user->getCoverPhotoUrl());
    }

    public function testCanGetAllDataBackAsAnArray()
    {
        $data = $this->user->toArray();

        $expectedData = [
          'id' => '4',
          'picture' => ['data' => ['is_silhouette' => true, 'url' => 'foo.com/pic.jpg']],
          'cover' => ['id' => '123', 'source' => 'foo.com/cover.jpg'],
          'first_name' => 'Mark',
          'last_name' => 'Zuck',
          'foo' => 'bar',
          'picture_url' => 'foo.com/pic.jpg',
          'is_silhouette' => true,
          'cover_photo_url' => 'foo.com/cover.jpg',
          'timezone' => '-8',
          'age_range' => ['min' => 21],
          'birthday' => '10/10/1992'
        ];

        $this->assertEquals($expectedData, $data);
    }
}
