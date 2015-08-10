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
            'picture' => ['data' => ['url' => 'foo.com/pic.jpg']],
            'first_name' => 'Mark',
            'last_name' => 'Zuck',
            'foo' => 'bar',
        ]);
    }

    public function testGettersReturnNullWhenNoKeyExists()
    {
        $this->assertEquals('4', $this->user->getId());
        $this->assertNull($this->user->getGender());
    }

    public function testProperlyMutatesPictureUrl()
    {
        $this->assertEquals('foo.com/pic.jpg', $this->user->getPictureUrl());
    }

    public function testCanGetAllDataBackAsAnArray()
    {
        $data = $this->user->toArray();

        $expectedData = [
          'id' => '4',
          'picture' => ['data' => ['url' => 'foo.com/pic.jpg']],
          'first_name' => 'Mark',
          'last_name' => 'Zuck',
          'foo' => 'bar',
          'picture_url' => 'foo.com/pic.jpg',
        ];

        $this->assertEquals($expectedData, $data);
    }
}
