<?php

namespace SocialApi\Lib\Model;

use BaseExceptions\Exception\InvalidArgument\EmptyArrayException;
use BaseExceptions\Exception\InvalidArgument\EmptyStringException;
use BaseExceptions\Exception\InvalidArgument\NotBooleanException;
use BaseExceptions\Exception\InvalidArgument\NotStringException;
use SocialApi\Lib\Model\Enum\ApiName;

/**
 * Class ApiConfig
 * @package SocialApi\Lib\Model
 */
class ApiConfig implements ApiConfigInterface
{
    /**
     * @var ApiName
     */
    private $name;

    /**
     * @var string
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
     * @var array
     */
    private $scopeList;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * ApiConfig constructor.
     *
     * @param ApiName $name
     * @param string $appId
     * @param string $appSecret
     * @param string $redirectUrl
     * @param array $scopeList
     * @param bool $enabled
     */
    public function __construct(
        ApiName $name,
        $appId,
        $appSecret,
        $redirectUrl,
        array $scopeList,
        $enabled
    ) {
        if (!is_string($appId)) {
            throw new NotStringException("appId");
        }
        if (empty($appId)) {
            throw new EmptyStringException("appId");
        }

        if (!is_string($appSecret)) {
            throw new NotStringException("appSecret");
        }
        if (empty($appSecret)) {
            throw new EmptyStringException("appSecret");
        }

        if (!is_string($redirectUrl)) {
            throw new NotStringException("redirectUrl");
        }
        if (empty($redirectUrl)) {
            throw new EmptyStringException("redirectUrl");
        }

        if (empty($scopeList)) {
            throw new EmptyArrayException("scopeList");
        }
        foreach ($scopeList as $scope) {
            if (!is_string($scope)) {
                throw new NotStringException("scope");
            }
        }

        if (!is_bool($enabled)) {
            throw new NotBooleanException("enabled");
        }

        $this->name = $name;
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->redirectUrl = $redirectUrl;
        $this->scopeList = $scopeList;
        $this->enabled = $enabled;
    }

    /**
     * @return ApiName
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
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
     * @return array
     */
    public function getScopeList()
    {
        return $this->scopeList;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
}
