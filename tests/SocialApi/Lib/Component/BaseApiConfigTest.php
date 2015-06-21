<?php

namespace Test\SocialApi\Lib\Component;

use PhpUnitPlus\Lib\Component\ConstructChecker;
use PhpUnitPlus\Lib\Util\Custom\ManualInput;
use PhpUnitPlus\Lib\Util\Custom\MergeInput;
use PhpUnitPlus\Lib\Util\Simple\AnyBool;
use PhpUnitPlus\Lib\Util\Simple\AnyInteger;
use PhpUnitPlus\Lib\Util\Simple\AnyString;
use SocialAPI\Lib\Component\BaseApiConfig;

/**
 * Class BaseApiConfigTest
 *
 * @package Test\SocialApi\Lib\Component
 */
class BaseApiConfigTest extends \PHPUnit_Framework_TestCase
{
    use ConstructChecker;

    /**
     * Test __construct method and getters
     */
    public function testConstructAndGetters()
    {
        $this->checkConstructor(
            [
                new AnyBool(),
                new MergeInput(new AnyString(false, false), new AnyInteger(false, false, false)),
                new AnyString(false, false),
                new AnyString(false, false),
                new ManualInput([['test']], [[]])
            ],
            function ($isEnabled, $appId, $appSecret, $redirectUrl, $scopeList) {
                $config = new BaseApiConfigTestClass(
                    $isEnabled,
                    $appId,
                    $appSecret,
                    $redirectUrl,
                    $scopeList
                );

                $this->assertEquals($isEnabled, $config->isEnabled());
                $this->assertEquals($appId, $config->getAppId());
                $this->assertEquals($appSecret, $config->getAppSecret());
                $this->assertEquals($redirectUrl, $config->getRedirectUrl());
                $this->assertEquals($scopeList, $config->getScopeList());
            }
        );
    }

}

/**
 * Class BaseApiConfigTestClass - tmp class for testing abstract method
 *
 * @package Test\SocialApi\Lib\Component
 */
class BaseApiConfigTestClass extends BaseApiConfig
{

}
