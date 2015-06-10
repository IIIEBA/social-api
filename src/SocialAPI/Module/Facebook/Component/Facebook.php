<?php

namespace SocialAPI\Module\Facebook\Component;

use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookSession;
use SocialAPI\Lib\Component\ApiInterface;
use SocialAPI\Lib\Util\LoggerTrait;

class Facebook implements ApiInterface
{
    use LoggerTrait;

    /**
     * @var FacebookConfig
     */
    private $config;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var FacebookSession
     */
    private $session;

    /**
     * @return FacebookConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param FacebookConfig $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return FacebookSession
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Init facebook api class
     *
     * @param FacebookConfig $config
     */
    public function __construct(FacebookConfig $config)
    {
        $this->setConfig($config);

        $this->initApi($this->getConfig()->getAppId(), $this->getConfig()->getAppSecret());

        if ($this->getConfig()->getAccessTocken() !== null) {
            $this->setAccessToken($this->getConfig()->getAccessTocken());
            $this->initSession($this->getAccessToken());
        }
    }

    /**
     * Init Facebook SDK
     *
     * @param int $appId
     * @param string $appSecret
     *
     * @throws \InvalidArgumentException
     */
    public function initApi($appId, $appSecret)
    {
        if (!is_int($appId)) {
            throw new \InvalidArgumentException('Only int appId allowed');
        } elseif ($appId < 1) {
            throw new \InvalidArgumentException('App id must be greater then 0');
        }

        FacebookSession::setDefaultApplication($appId, $appSecret);
    }

    /**
     * Return session object instance
     *
     * @param bool $reinit
     *
     * @return FacebookSession
     */
    public function initSession($reinit = false)
    {
        if ($this->getSession() === null || $reinit === true) {
            $this->session = new FacebookSession($this->getAccessToken());
        }

        return $this->session;
    }

    /**
     * Generate redirect url for facebook auth
     * 
     * @return string
     */
    public function generateLoginUrl()
    {
        $helper   = new FacebookRedirectLoginHelper($this->getConfig()->getRedirectUrl());
        $loginUrl = $helper->getLoginUrl($this->getConfig()->getScopeList());

        return $loginUrl;
    }
}
