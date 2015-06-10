<?php

namespace SocialAPI\Module\Facebook\Component;

class FacebookConfig
{
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
     * @var null
     */
    private $accessTocken;

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
     * @return null
     */
    public function getAccessTocken()
    {
        return $this->accessTocken;
    }

    /**
     * Init config
     *
     * @param int $appId
     * @param string $appSecret
     * @param string $redirectUrl
     * @param array $scopeList
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($appId, $appSecret, $redirectUrl, array $scopeList, $accessToken = null)
    {
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

        if ($accessToken !== null && !is_string($accessToken)) {
            throw new \InvalidArgumentException('Only string allowed for accessToken');
        }

        $this->appId        = $appId;
        $this->appSecret    = $appSecret;
        $this->redirectUrl  = $redirectUrl;
        $this->scopeList    = $scopeList;
        $this->accessTocken = $accessToken;
    }
}
