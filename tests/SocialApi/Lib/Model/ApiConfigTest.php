<?php

namespace Tests\SocialApi\Lib\Model;

use PhpUnitPlus\Lib\Component\InputDataChecker;
use PhpUnitPlus\Lib\Util\Custom\ManualInput;
use PhpUnitPlus\Lib\Util\Simple\AnyBool;
use PhpUnitPlus\Lib\Util\Simple\AnyString;
use PhpUnitPlus\Lib\Util\Simple\TypeHintingInput;
use SocialApi\Lib\Model\ApiConfig;
use SocialApi\Lib\Model\Enum\ApiName;

/**
 * Class ApiConfigTest
 * @package Tests\SocialApi\Lib\Model
 */
class ApiConfigTest extends \PHPUnit_Framework_TestCase
{
    use InputDataChecker;

    public function testModel()
    {
        $this->checkInputData(
            [
                new TypeHintingInput(new ApiName(ApiName::GITHUB)),
                new AnyString(false, false),
                new AnyString(false, false),
                new AnyString(false, false),
                new ManualInput([["test", "1"], ["scope"]]),
                new AnyBool(false),
            ],
            function (
                $name,
                $appId,
                $appSecret,
                $redirectUrl,
                $scopeList,
                $enabled
            ) {
                $foo = new ApiConfig(
                    $name,
                    $appId,
                    $appSecret,
                    $redirectUrl,
                    $scopeList,
                    $enabled
                );

                $this->assertEquals($name, $foo->getName());
                $this->assertEquals($appId, $foo->getAppId());
                $this->assertEquals($appSecret, $foo->getAppSecret());
                $this->assertEquals($redirectUrl, $foo->getRedirectUrl());
                $this->assertEquals($scopeList, $foo->getScopeList());
                $this->assertEquals($enabled, $foo->isEnabled());
            }
        );
    }
}
