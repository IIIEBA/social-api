<?php

namespace SocialAPI\Module\Vk\Component;

use SocialAPI\Lib\Component\ApiConfigInterface;

class VkConfig implements ApiConfigInterface
{
    /**
     * @var bool
     */
    private $isEnabled = false;

    /**
     * @var int
     */
    private $appId;

    /**
     * @var string
     */
    private $appSecret;

    /**
     * @var string
     */
    private $redirectUrl;

    /**
     * @var string[] List of scopes for user
     */
    private $scopeList = [];

    /**
     * Check API for enabled status
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @return int
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @return string
     */
    public function getAppSecret()
    {
        return $this->appSecret;
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * @return string[]
     */
    public function getScopeList()
    {
        return $this->scopeList;
    }

    /**
     * Init config
     *
     * @param bool $isEnabled
     * @param int $appId
     * @param string $appSecret
     * @param string $redirectUrl
     * @param array $scopeList
     */
    public function __construct($isEnabled, $appId, $appSecret, $redirectUrl, array $scopeList)
    {
        if (!is_bool($isEnabled)) {
            throw new \InvalidArgumentException('Only bool isEnabled allowed');
        }

        if (!is_int($appId)) {
            throw new \InvalidArgumentException('Only int appId allowed');
        } elseif ($appId < 1) {
            throw new \InvalidArgumentException('App id must be greater then 0');
        }

        if (!is_string($appSecret)) {
            throw new \InvalidArgumentException('Only string allowed for appSecret');
        }

        if (!is_string($redirectUrl)) {
            throw new \InvalidArgumentException('Only string allowed for redirectUrl');
        }

        if (empty($scopeList)) {
            throw new \InvalidArgumentException('You must set at least one scope');
        }

        $this->isEnabled    = $isEnabled;
        $this->appId        = $appId;
        $this->appSecret    = $appSecret;
        $this->redirectUrl  = $redirectUrl;
        $this->scopeList    = $scopeList;
    }
}
