<?php

namespace SocialAPI\Module\Facebook\Component;

use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequestException;
use Facebook\FacebookSession;
use SocialAPI\Lib\Component\ApiConfigInterface;
use SocialAPI\Lib\Component\ApiInterface;
use SocialAPI\Lib\Util\LoggerTrait;
use SocialAPI\Module\Exception\FacebookException;
use Symfony\Component\HttpFoundation\Request;

class Facebook implements ApiInterface
{
    use LoggerTrait;

    /**
     * @var FacebookConfig
     */
    private $config;

    /**
     * @var Request
     */
    private $request;

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
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
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
     * @param ApiConfigInterface $config
     * @param Request $request
     * @param null|string $accessToken
     */
    public function __construct(ApiConfigInterface $config, Request $request, $accessToken = null)
    {
        $this->config  = $config;
        $this->request = $request;

        $this->initApi($this->getConfig()->getAppId(), $this->getConfig()->getAppSecret());

        if ($accessToken !== null) {
            $this->setAccessToken($accessToken);
            $this->initSessionFromAccessCode($this->getAccessToken());
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
     * Init facebook session object from access token
     *
     * @param bool $reInit
     */
    public function initSessionFromAccessCode($reInit = false)
    {
        if ($this->getSession() === null || $reInit === true) {
            $this->session = new FacebookSession($this->getAccessToken());
        }
    }

    /**
     * Init facebook session object after redirect
     */
    public function initSessionFromRedirect()
    {
        $helper = new FacebookRedirectLoginHelper($this->getConfig()->getRedirectUrl());

        try {
            $this->session = $helper->getSessionFromRedirect();
        } catch (FacebookRequestException $e) {
            throw new FacebookException($e->getMessage());
        } catch (\Exception $e) {
            throw new FacebookException($e->getMessage());
        }
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
