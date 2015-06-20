<?php

namespace Tests\SocialApi\Lib\Component;

use PHPUnit_Framework_TestCase;
use SocialAPI\Lib\Component\ApiInterface;
use SocialAPI\Lib\Component\BaseApi;
use SocialAPI\Lib\Component\SocialApi;
use SocialAPI\Lib\Exception\SocialApiException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SocialApiTest
 *
 * @package Tests\SocialApi\Lib\Component
 */
class SocialApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * Return ApiInterface
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function mockApi()
    {
        return $this->getMockBuilder(BaseApi::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * Test __construct method
     */
    public function testConstruct()
    {
        $request    = Request::createFromGlobals();
        $config     = [
            'test'      => [
                'isEnabled' => false,
            ],
        ];

        // Test correct set od constructor
        $socialApi = new SocialApi($request, $config);
        $this->assertEquals($config, $socialApi->getApiConfigList());
    }

    /**
     * Test init method
     * @throws SocialApiException
     */
    public function testInit()
    {
        $request    = Request::createFromGlobals();
        $config     = [
            'facebook' => [
                'isEnabled' => false,
            ],
        ];
        $socialApi      = new SocialApi($request, $config);
        $reflection     = new \ReflectionClass($socialApi);
        $configField    = $reflection->getProperty('apiConfigList');
        $configField->setAccessible(true);

        // Test for disabled APIs
        $this->assertTrue(count($socialApi->getEnabledApiList()) === 0);

        // Set new not empty config
        $config = [
            'vk' => [
                'isEnabled'     => true,
                'appId'         => 1,
                'appSecret'     => 'secret',
                'redirectUrl'   => 'redirect',
                'scopeList'     => [
                    'someFeature',
                ],
            ],
        ];
        $configField->setValue($socialApi, $config);
        $socialApi->initApis($request);

        $this->assertTrue(count($socialApi->getEnabledApiList()) === 1);
        $this->assertTrue($socialApi->getVk() instanceof ApiInterface);
    }

    /**
     * Test Init method for incorrect API name
     * @expectedException \SocialAPI\Lib\Exception\SocialApiException
     */
    public function testInitForUnsupportedApiName()
    {
        $config = [
            'test' => [
                'isEnabled' => true,
            ],
        ];
        new SocialApi(Request::createFromGlobals(), $config);
    }

    /**
     * Test addApi method
     * @throws SocialApiException
     */
    public function testAddApi()
    {
        $socialApi      = new SocialApi(Request::createFromGlobals(), []);
        $reflection     = new \ReflectionClass($socialApi);
        $apiListField   = $reflection->getProperty('apiList');
        $apiListField->setAccessible(true);
        $apiList        = $apiListField->getValue($socialApi);

        // Test initial empty api list
        $this->assertTrue(is_array($apiList));
        $this->assertTrue(empty($apiList));

        // Set new api
        $socialApi->addApi('test', $this->mockApi());
        $apiList = $apiListField->getValue($socialApi);

        // Test new api data
        $this->assertTrue(count($apiList) === 1);
        $this->assertTrue(isset($apiList['test']));
        $this->assertTrue($apiList['test'] instanceof ApiInterface);
    }

    /**
     * Test getApi method
     * @throws SocialApiException
     */
    public function testGetApi()
    {
        $socialApi  = new SocialApi(Request::createFromGlobals(), []);
        $testApi    = $this->mockApi();
        $socialApi->addApi('test', $testApi);

        $this->assertEquals($testApi, $socialApi->getApi('test'));

        // Test not int API name
        foreach ([null, 432, 2.3, new \stdClass(), [], true] as $name) {
            try {
                $socialApi->getApi($name);
                $this->fail(gettype($name) . ' is not allowed as name');
            } catch (\InvalidArgumentException $e) {
                $this->assertTrue(true, 'Only string allowed for name');
            }
        }

        // Test not allowed API
        try {
            $socialApi->getApi('my');
            $this->fail('Cant return not existed API');
        } catch (SocialApiException $e) {
            $this->assertTrue(true, 'Not allowed api was selected');
        }
    }

    /**
     * Test getApiConfigList method for correct return
     */
    public function testGetApiConfigList()
    {
        $config = [
            'test' => [
                'isEnabled' => false,
            ],
        ];
        $socialApi = new SocialApi(Request::createFromGlobals(), $config);

        $this->assertEquals($config, $socialApi->getApiConfigList());
    }

    /**
     * Test getFacebook method
     * @throws SocialApiException
     */
    public function testGetFacebook()
    {
        $api        = $this->mockApi();
        $socialApi  = new SocialApi(Request::createFromGlobals(), []);
        $socialApi->addApi('facebook', $api);

        $this->assertEquals($api, $socialApi->getFacebook());
    }

    /**
     * Test getVk method
     * @throws SocialApiException
     */
    public function testGetVk()
    {
        $api        = $this->mockApi();
        $socialApi  = new SocialApi(Request::createFromGlobals(), []);
        $socialApi->addApi('vk', $api);

        $this->assertEquals($api, $socialApi->getVk());
    }

    /**
     * Test getInstagram method
     * @throws SocialApiException
     */
    public function testGetInstagram()
    {
        $api        = $this->mockApi();
        $socialApi  = new SocialApi(Request::createFromGlobals(), []);
        $socialApi->addApi('instagram', $api);

        $this->assertEquals($api, $socialApi->getInstagram());
    }

    /**
     * Test getEnabledApiList method
     * @throws SocialApiException
     */
    public function testGetEnabledApiList()
    {
        $apiOne     = $this->mockApi();
        $apiTwo     = $this->mockApi();
        $socialApi  = new SocialApi(Request::createFromGlobals(), []);
        $socialApi->addApi('one', $apiOne);
        $socialApi->addApi('two', $apiTwo);

        $this->assertEquals(['one', 'two'], $socialApi->getEnabledApiList());
    }
}
