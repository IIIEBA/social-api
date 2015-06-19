<?php

namespace SocialAPI\Lib\Component;

use Psr\Log\LoggerAwareInterface;
use SocialAPI\Lib\Exception\SocialApiException;
use SocialAPI\Lib\Util\Logger\LoggerTrait;
use SocialAPI\Module\Facebook\Component\Facebook;
use SocialAPI\Module\Facebook\Component\FacebookConfig;
use SocialAPI\Module\Instagram\Component\Instagram;
use SocialAPI\Module\Instagram\Component\InstagramConfig;
use SocialAPI\Module\Vk\Component\VkConfig;
use SocialAPI\Module\Vk\Component\Vk;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SocialApi
 *
 * @package SocialAPI\Lib\Component
 */
class SocialApi implements LoggerAwareInterface
{
    use LoggerTrait;

    /**
     * @var ApiConfigInterface[]
     */
    private $apiConfigList;

    /**
     * @var ApiInterface[]
     */
    private $apiList = [];

    /**
     * Get api config list
     * @return ApiConfigInterface[]
     */
    public function getApiConfigList()
    {
        return $this->apiConfigList;
    }

    /**
     * @param array $apiConfigList
     * @param Request $request
     *
     * @throws SocialApiException
     */
    public function __construct(Request $request, array $apiConfigList = [])
    {
        $this->apiConfigList = $apiConfigList;

        $this->initApis($request);
    }

    /**
     * Init social API`s from config if enabled
     * @param $request
     *
     * @throws SocialApiException
     */
    private function initApis($request)
    {
        foreach ($this->getApiConfigList() as $apiName => $config) {
            if (isset($config['isEnabled']) && $config['isEnabled'] === false) {
                continue;
            }

            switch ($apiName) {
                case 'facebook':
                    $config = new FacebookConfig(
                        isset($config['isEnabled'])     ? $config['isEnabled']      : null,
                        isset($config['appId'])         ? $config['appId']          : null,
                        isset($config['appSecret'])     ? $config['appSecret']      : null,
                        isset($config['redirectUrl'])   ? $config['redirectUrl']    : null,
                        isset($config['scopeList'])     ? $config['scopeList']      : null
                    );

                    $api = new Facebook($config, $request, $this->getLogger());

                    $this->addApi($apiName, $api);
                    break;
                case 'vk':
                    $config = new VkConfig(
                        isset($config['isEnabled'])     ? $config['isEnabled']      : null,
                        isset($config['appId'])         ? $config['appId']          : null,
                        isset($config['appSecret'])     ? $config['appSecret']      : null,
                        isset($config['redirectUrl'])   ? $config['redirectUrl']    : null,
                        isset($config['scopeList'])     ? $config['scopeList']      : null
                    );

                    $api = new Vk($config, $request, $this->getLogger());

                    $this->addApi($apiName, $api);
                    break;
                case 'instagram':
                    $config = new InstagramConfig(
                        isset($config['isEnabled'])     ? $config['isEnabled']      : null,
                        isset($config['appId'])         ? $config['appId']          : null,
                        isset($config['appSecret'])     ? $config['appSecret']      : null,
                        isset($config['redirectUrl'])   ? $config['redirectUrl']    : null,
                        isset($config['scopeList'])     ? $config['scopeList']      : null
                    );

                    $api = new Instagram($config, $request, $this->getLogger());

                    $this->addApi($apiName, $api);
                    break;

                default:
                    $msg = 'Not allowed api config was given to SocialApi';
                    $this->getLogger()->error(
                        $msg,
                        [
                            'object' => $this,
                        ]
                    );
                    throw new SocialApiException($msg);
            }
        }
    }

    /**
     * Add initialized api to allowed list
     * @param string $name
     * @param ApiInterface $api
     *
     * @throws SocialApiException
     */
    public function addApi($name, ApiInterface $api)
    {
        if (!is_string($name)) {
            $msg = 'Only string allowed for apiName';
            $this->getLogger()->error(
                $msg,
                [
                    'object' => $this,
                ]
            );
            throw new SocialApiException($msg);
        }

        $this->apiList[$name] = $api;
    }

    /**
     * Return list of enabled APIs
     * @return string[]
     */
    public function getEnabledApiList()
    {
        return array_keys($this->apiList);
    }

    /**
     * Get selected API instance
     * @param string $name
     * @return ApiInterface
     *
     * @throws SocialApiException
     */
    public function getApi($name)
    {
        if (!is_string($name)) {
            $msg = 'Only string allowed for name';
            $this->getLogger()->error(
                $msg,
                [
                    'object' => $this,
                ]
            );
            throw new SocialApiException($msg);
        }

        switch ($name) {
            case 'facebook':
                return $this->getFacebook();
                break;

            case 'vk':
                return $this->getVk();
                break;

            case 'instagram':
                return $this->getInstagram();
                break;

            default:
                $msg = 'You trying to get non exist API';
                $this->getLogger()->error(
                    $msg,
                    [
                        'object' => $this,
                    ]
                );
                throw new SocialApiException($msg);
        }
    }

    /**
     * Get Facebook API instance
     * @return ApiInterface
     *
     * @throws SocialApiException
     */
    public function getFacebook()
    {
        if (!isset($this->apiList['facebook']) || $this->apiList['facebook'] === null) {
            $msg = 'Facebook API is disabled by config';
            $this->getLogger()->error(
                $msg,
                [
                    'object' => $this,
                ]
            );
            throw new SocialApiException($msg);
        }

        return $this->apiList['facebook'];
    }

    /**
     * Get VK API instance
     * @return ApiInterface
     *
     * @throws SocialApiException
     */
    public function getVk()
    {
        if (!isset($this->apiList['vk']) || $this->apiList['vk'] === null) {
            $msg = 'VK API is disabled by config';
            $this->getLogger()->error(
                $msg,
                [
                    'object' => $this,
                ]
            );
            throw new SocialApiException($msg);
        }

        return $this->apiList['vk'];
    }

    public function getInstagram()
    {
        if (!isset($this->apiList['instagram']) || $this->apiList['instagram'] === null) {
            $msg = 'Instagram API is disabled by config';
            $this->getLogger()->error(
                $msg,
                [
                    'object' => $this,
                ]
            );
            throw new SocialApiException($msg);
        }

        return $this->apiList['instagram'];
    }
}
