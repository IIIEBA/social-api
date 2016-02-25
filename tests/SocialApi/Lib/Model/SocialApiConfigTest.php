<?php

namespace Tests\SocialApi\Lib\Model;

use PhpUnitPlus\Lib\Component\InputDataChecker;
use PhpUnitPlus\Lib\Util\Custom\ManualInput;
use SocialApi\Lib\Model\ApiConfig;
use SocialApi\Lib\Model\Enum\ApiName;
use SocialApi\Lib\Model\SocialApiConfig;

/**
 * Class SocialApiConfigTest
 * @package Tests\SocialApi\Lib\Model
 */
class SocialApiConfigTest extends \PHPUnit_Framework_TestCase
{
    use InputDataChecker;

    public function buildApiConfig()
    {
        return new ApiConfig(
            new ApiName(rand(0, 1) ? ApiName::GITHUB : ApiName::FACEBOOK),
            'some string' . rand(0, 1000),
            'some secret' . rand(0, 1000),
            'some url' . rand(0, 1000),
            ['one', 'two'],
            boolval(rand(0, 1))
        );
    }

    public function testModel()
    {
        $this->checkInputData(
            [
                new ManualInput([[$this->buildApiConfig()], [$this->buildApiConfig(), $this->buildApiConfig()]]),
            ],
            function (
                $apiConfig
            ) {
                $foo = new SocialApiConfig($apiConfig);

                $this->assertEquals($apiConfig, $foo->getApiConfigList());
            }
        );
    }
}
