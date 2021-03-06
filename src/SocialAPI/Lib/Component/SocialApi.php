<?php

namespace SocialAPI\Lib\Component;

use Psr\Log\LoggerAwareInterface;
use SocialAPI\Lib\Exception\SocialApiException;
use SocialAPI\Lib\Util\Logger\LoggerTrait;
use SocialAPI\Module\Facebook\Component\Facebook;
use SocialAPI\Module\Facebook\Component\FacebookConfig;
use SocialAPI\Module\GitHub\Component\GitHub;
use SocialAPI\Module\GitHub\Component\GitHubConfig;
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
     * @throws SocialApiException
     */
    public function initApis($request)
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

                case 'github':
                    $config = new GitHubConfig(
                        isset($config['isEnabled'])     ? $config['isEnabled']      : null,
                        isset($config['appId'])         ? $config['appId']          : null,
                        isset($config['appSecret'])     ? $config['appSecret']      : null,
                        isset($config['redirectUrl'])   ? $config['redirectUrl']    : null,
                        isset($config['scopeList'])     ? $config['scopeList']      : null
                    );

                    $api = new GitHub($config, $request, $this->getLogger());

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
            throw new \InvalidArgumentException($msg);
        }

        if (!isset($this->apiList[$name])) {
            $msg = ucfirst($name) . ' API is not allowed or not configurated';
            $this->getLogger()->error(
                $msg,
                [
                    'object' => $this,
                ]
            );
            throw new SocialApiException($msg);
        }

        return $this->apiList[$name];
    }

    /**
     * Get Facebook API instance
     * @return ApiInterface
     * @throws SocialApiException
     */
    public function getFacebook()
    {
        return $this->getApi('facebook');
    }

    /**
     * Get VK API instance
     * @return ApiInterface
     * @throws SocialApiException
     */
    public function getVk()
    {
        return $this->getApi('vk');
    }

    /**
     * Get Instagram API interface
     * @return ApiInterface
     * @throws SocialApiException
     */
    public function getInstagram()
    {
        return $this->getApi('instagram');
    }

    /**
     * Get GitHub API interface
     * @return ApiInterface
     * @throws SocialApiException
     */
    public function getGitHub()
    {
        return $this->getApi('github');
    }
}
