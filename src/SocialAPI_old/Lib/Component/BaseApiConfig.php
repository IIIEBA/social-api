<?php

namespace SocialAPI\Lib\Component;

use SocialAPI\Lib\Exception\InvalidArgument\EmptyArrayException;
use SocialAPI\Lib\Exception\InvalidArgument\EmptyStringException;
use SocialAPI\Lib\Exception\InvalidArgument\NotStringException;

/**
 * Class BaseApiConfig
 *
 * @package SocialAPI\Lib\Component
 */
class BaseApiConfig implements ApiConfigInterface
{
    /**
     * @var bool
     */
    private $isEnabled = false;

    /**
     * @var int|string
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
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Get app id
     * @return int
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * Get app secret
     * @return string
     */
    public function getAppSecret()
    {
        return $this->appSecret;
    }

    /**
     * Get redirect url
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * Get scope list
     * @return string[]
     */
    public function getScopeList()
    {
        return $this->scopeList;
    }

    /**
     * Init config
     * @param bool $isEnabled
     * @param int|string $appId
     * @param string $appSecret
     * @param string $redirectUrl
     * @param array $scopeList
     */
    public function __construct($isEnabled, $appId, $appSecret, $redirectUrl, array $scopeList)
    {
        if (!is_bool($isEnabled)) {
            throw new \InvalidArgumentException('Only bool isEnabled allowed');
        }

        if (is_int($appId)) {
            if ($appId < 1) {
                throw new \InvalidArgumentException('App id must be greater then 0');
            }
        } elseif (!is_string($appId)) {
            throw new NotStringException('appId');
        } elseif ($appId === '') {
            throw new EmptyStringException('appId');
        }

        if (!is_string($appSecret)) {
            throw new NotStringException('lastName');
        } elseif ($appSecret === '') {
            throw new EmptyStringException('lastName');
        }

        if (!is_string($redirectUrl)) {
            throw new NotStringException('lastName');
        } elseif ($redirectUrl === '') {
            throw new EmptyStringException('lastName');
        }

        if (empty($scopeList)) {
            throw new EmptyArrayException('scopeList');
        }

        $this->isEnabled    = $isEnabled;
        $this->appId        = $appId;
        $this->appSecret    = $appSecret;
        $this->redirectUrl  = $redirectUrl;
        $this->scopeList    = $scopeList;
    }
}
