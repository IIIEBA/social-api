<?php

namespace Tests\SocialApi\Lib\Model;

use PhpUnitPlus\Lib\Component\InputDataChecker;
use PhpUnitPlus\Lib\Util\Custom\MergeInput;
use PhpUnitPlus\Lib\Util\Simple\AnyInteger;
use PhpUnitPlus\Lib\Util\Simple\AnyString;
use PhpUnitPlus\Lib\Util\Simple\TypeHintingInput;
use SocialApi\Lib\Model\Enum\Gender;
use SocialApi\Lib\Model\Profile;

/**
 * Class ProfileTest
 * @package Tests\SocialApi\Lib\Model
 */
class ProfileTest extends \PHPUnit_Framework_TestCase
{
    use InputDataChecker;

    public function testModel()
    {
        $this->checkInputData(
            [
                new MergeInput(new AnyString(false, false), new AnyInteger(false, false, false)),
                new AnyString(false, false),
                new AnyString(false, true),
                new AnyString(false, true),
                new TypeHintingInput(new Gender(Gender::MALE), true),
                new TypeHintingInput(new \DateTimeImmutable(), true),
                new AnyString(false, true),
            ],
            function (
                $id,
                $firstName,
                $lastName,
                $email,
                $gender,
                $birthday,
                $avatarUrl
            ) {
                $foo = new Profile(
                    $id,
                    $firstName,
                    $lastName,
                    $email,
                    $gender,
                    $birthday,
                    $avatarUrl
                );

                $this->assertEquals($id, $foo->getId());
                $this->assertEquals($firstName, $foo->getFirstName());
                $this->assertEquals($lastName, $foo->getLastName());
                $this->assertEquals($email, $foo->getEmail());
                $this->assertEquals($birthday, $foo->getBirthday());
                $this->assertEquals($avatarUrl, $foo->getAvatarUrl());

                if (!is_null($gender)) {
                    $this->assertEquals($gender, $foo->getGender());
                } else {
                    $this->assertEquals(new Gender(Gender::UNKNOWN), $foo->getGender());
                }
            }
        );
    }
}
