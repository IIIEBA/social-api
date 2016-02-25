<?php

namespace SocialApi\Lib;

use Psr\Log\LoggerInterface;
use SocialApi\Lib\Exception\SocialApiException;
use SocialApi\Lib\Model\ApiConfigInterface;
use SocialApi\Lib\Model\Enum\ApiName;
use SocialApi\Lib\Model\SocialApiConfigInterface;
use SocialAPI\Lib\Util\Logger\LoggerTrait;
use SocialApi\Module\FacebookApi;
use SocialApi\Module\GitHubApi;
use SocialApi\Module\VkApi;

/**
 * Class SocialApi
 * @package SocialApi\Lib
 */
class SocialApi implements SocialApiInterface
{
    use LoggerTrait;

    /**
     * @var array
     */
    private $apiConfigList;

    /**
     * SocialApi constructor.
     *
     * @param SocialApiConfigInterface $apiConfig
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        SocialApiConfigInterface $apiConfig,
        LoggerInterface $logger = null
    ) {
        $this->setLogger($logger);

        $apiConfigList = $apiConfig->getApiConfigList();
        foreach ($apiConfigList as $apiConfig) {
            $this->apiConfigList[$apiConfig->getName()->getValue()] = $apiConfig;
        }
    }

    /**
     * Get config for selected api
     *
     * @param ApiName $name
     * @return ApiConfigInterface
     */
    public function getConfig(ApiName $name)
    {
        if (!array_key_exists($name->getValue(), $this->apiConfigList)) {
            throw new \LogicException("No config was found for api - " . $name->getValue());
        }

        return $this->apiConfigList[$name->getValue()];
    }

    /**
     * Get new instance of api client
     *
     * @param ApiName $name
     * @return ApiInterface
     * @throws SocialApiException
     */
    public function getApi(ApiName $name)
    {
        switch ($name->getValue()) {
            case ApiName::FACEBOOK:
                return new FacebookApi($this->getConfig($name), $this->getLogger());
                break;

            case ApiName::VK:
                return new VkApi($this->getConfig($name), $this->getLogger());
                break;

            case ApiName::GITHUB:
                return new GitHubApi($this->getConfig($name), $this->getLogger());
                break;

            default:
                throw new SocialApiException("This API does not have realisation, yet");
        }
    }

    /**
     * Get list of enabled api`s
     *
     * @return string[]
     */
    public function getEnabledApiList()
    {
        $result = [];

        foreach ($this->apiConfigList as $name => $config) {
            /**
             * @var ApiConfigInterface $config
             */
            if ($config->isEnabled()) {
                $result[] = $name;
            }
        }

        return $result;
    }

    /**
     * Get associative array with service name and login link
     *
     * @return array
     * @throws SocialApiException
     */
    public function getLoginUrlList()
    {
        $result = [];
        $apiList = $this->getEnabledApiList();

        foreach ($apiList as $name) {
            $api = $this->getApi(new ApiName($name));
            $result[$name] = $api->generateLoginUrl();
        }

        return $result;
    }
}
