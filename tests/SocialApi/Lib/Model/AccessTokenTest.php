<?php

namespace Tests\SocialApi\Lib\Model;

use PhpUnitPlus\Lib\Component\InputDataChecker;
use PhpUnitPlus\Lib\Util\Simple\AnyString;
use PhpUnitPlus\Lib\Util\Simple\TypeHintingInput;
use SocialApi\Lib\Model\AccessToken;

/**
 * Class AccessTokenTest
 * @package SocialApi\Lib\Model
 */
class AccessTokenTest extends \PHPUnit_Framework_TestCase
{
    use InputDataChecker;

    public function testModel()
    {
        $this->checkInputData(
            [
                new AnyString(false, false),
                new TypeHintingInput(new \DateTimeImmutable(), true),
                new AnyString(false, true)
            ],
            function (
                $token,
                $expireAt,
                $renewToken
            ) {
                $foo = new AccessToken(
                    $token,
                    $expireAt,
                    $renewToken
                );

                $this->assertEquals($token, $foo->getToken());
                $this->assertEquals($expireAt, $foo->getExpireAt());
                $this->assertEquals($renewToken, $foo->getRenewToken());
            }
        );
    }
}
