<?php

namespace Tests\SocialApi\Lib\Models\ApiResponse;

use SocialAPI\Lib\Model\ApiResponse\Profile;
use SocialAPI\Lib\Util\Tests\ConstructorTester;

/**
 * Class ProfileTest
 *
 * @package Tests\SocialApi\Lib\Models\ApiResponse
 */
class ProfileTest extends \PHPUnit_Framework_TestCase
{
    use ConstructorTester;

    /**
     * Test for __construct method
     */
    public function testConstructor()
    {
        $success = [
            'id'        => [324, 'test'],
            'firstName' => ['ddd'],
            'lastName'  => ['sss', null],
            'email'     => ['ccc', null],
            'gender'    => ['male', 'female', null],
            'birthday'  => [new \DateTimeImmutable(), null],
            'avatarUrl' => ['ava', null],
        ];

        $fail = [
            'id'        => [null, []],
            'firstName' => [4234, null],
            'lastName'  => [5236],
            'email'     => [546436],
            'gender'    => ['bar', 4234],
            'birthday'  => [new \DateTime(), 'foo', 341],
            'avatarUrl' => [333],
        ];

        $this->checkConstructor(
            $success,
            $fail,
            function($id, $firstName, $lastName, $email, $gender, $birthday, $avatarUrl) {
                $profile = new Profile($id, $firstName, $lastName, $email, $gender, $birthday, $avatarUrl);

                $this->assertEquals($id, $profile->getId());
                $this->assertEquals($firstName, $profile->getFirstName());
                $this->assertEquals($lastName, $profile->getLastName());
                $this->assertEquals($email, $profile->getEmail());
                $this->assertEquals($gender, $profile->getGender());
                $this->assertEquals($birthday, $profile->getBirthday());
                $this->assertEquals($avatarUrl, $profile->getAvatarUrl());
            }
        );
    }
}
