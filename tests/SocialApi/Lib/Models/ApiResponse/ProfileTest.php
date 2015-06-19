<?php

namespace Tests\SocialApi\Lib\Models\ApiResponse;

/**
 * Class ProfileTest
 *
 * @package Tests\SocialApi\Lib\Models\ApiResponse
 */
class ProfileTest extends \PHPUnit_Framework_TestCase
{
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
            'id'        => [0, -3, null],
            'firstName' => [4234, null],
            'lastName'  => [5236],
            'email'     => [546436],
            'gender'    => ['bar', 4234],
            'birthday'  => [new \DateTime(), 'foo', 341],
            'avatarUrl' => [333],
        ];

        
    }
}
