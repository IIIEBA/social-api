<?php

namespace Test\SocialApi\Lib\Component;

use SocialAPI\Lib\Component\BaseApiConfig;
use SocialAPI\Lib\Util\Tests\ConstructorTester;

/**
 * Class BaseApiConfigTest
 *
 * @package Test\SocialApi\Lib\Component
 */
class BaseApiConfigTest extends \PHPUnit_Framework_TestCase
{
    use ConstructorTester;

    /**
     * Test __construct method and getters
     */
    public function testConstructAndGetters()
    {
        $valid = [
            'isEnabled'     => [false],
            'appId'         => [43, 'test'],
            'appSecret'     => ['test'],
            'redirectUrl'   => ['test'],
            'scopeList'     => [['test']],
        ];

        $invalid = [
            'isEnabled'     => [244, 3.5, 'em', [], new \stdClass(), null],
            'appId'         => [0, -1, 4.8, [], new \stdClass(), null],
            'appSecret'     => [5, 4.8, [], new \stdClass(), null],
            'redirectUrl'   => [8, 4.8, [], new \stdClass(), null],
            'scopeList'     => [[]],
        ];

        $this->checkConstructor(
            $valid,
            $invalid,
            function($isEnabled, $appId, $appSecret, $redirectUrl, $scopeList) {
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