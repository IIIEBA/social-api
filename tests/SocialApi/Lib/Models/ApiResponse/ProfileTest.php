<?php

namespace Tests\SocialApi\Lib\Models\ApiResponse;

use PhpUnitPlus\Lib\Component\ConstructChecker;
use PhpUnitPlus\Lib\Util\Custom\ManualInput;
use PhpUnitPlus\Lib\Util\Custom\MergeInput;
use PhpUnitPlus\Lib\Util\Simple\AnyInteger;
use PhpUnitPlus\Lib\Util\Simple\AnyString;
use SocialAPI\Lib\Model\ApiResponse\Profile;

/**
 * Class ProfileTest
 *
 * @package Tests\SocialApi\Lib\Models\ApiResponse
 */
class ProfileTest extends \PHPUnit_Framework_TestCase
{
    use ConstructChecker;

    /**
     * Test for __construct method and getters
     */
    public function testConstructorAndGetters()
    {
        $this->checkConstructor(
            [
                new MergeInput(new AnyInteger(false, false, false), new AnyString(false, false)),
                new AnyString(false, false),
                new AnyString(false, true),
                new AnyString(false, true),
                new ManualInput(['male', 'female', null]),
                new ManualInput([new \DateTimeImmutable(), null]),
                new AnyString(false, true),
            ],
            function ($id, $firstName, $lastName, $email, $gender, $birthday, $avatarUrl) {
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
