<?php

namespace SocialAPI\Module\Facebook\Component;

use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\FacebookSession;
use SocialAPI\Lib\Component\ApiConfigInterface;
use SocialAPI\Lib\Component\ApiInterface;
use SocialAPI\Lib\Model\ApiResponse\Profile;
use SocialAPI\Lib\Model\ApiResponse\ProfileInterface;
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
     * Init facebook session object from access token
     *
     * @param bool $reInit
     */
    public function initSession($reInit = false)
    {
        if ($this->getSession() === null || $reInit === true) {
            $this->session = new FacebookSession($this->getAccessToken());
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

    /**
     * Generate user logout url
     *
     * @return string
     * @throws \Facebook\FacebookSDKException
     */
    public function generateLogoutUrl()
    {
        $helper    = new FacebookRedirectLoginHelper($this->getConfig()->getRedirectUrl());
        $logoutUrl = $helper->getLogoutUrl($this->getSession(), $this->getConfig()->getRedirectUrl());

        return $logoutUrl;
    }

    /**
     * Parse request for code variable and request access token by it
     *
     * @throws FacebookException
     * @throws FacebookRequestException
     */
    public function generateAccessTokenFromCode()
    {
        // Prepare request params
        $params = [
            'client_id'     => $this->getConfig()->getAppId(),
            'redirect_uri'  => $this->getConfig()->getRedirectUrl(),
            'client_secret' => $this->getConfig()->getAppSecret(),
            'code'          => $this->getRequest()->get('code'),
        ];

        // Making request
        $request = new FacebookRequest(
            FacebookSession::newAppSession($this->getConfig()->getAppId(), $this->getConfig()->getAppId()),
            'GET',
            '/oauth/access_token',
            $params
        );

        // Get response
        try {
            $response = $request->execute()->getResponse();
        } catch (\Exception $e) {
            $this->getLogger()->error(
                'Failed while making request to facebook API',
                [
                    'class'     => $this,
                    'exception' => $e,
                ]
            );
            throw new FacebookException('Failed while making request to facebook API');
        }

        // Few manipulations for backward compatibility
        $accessToken = null;
        if (is_object($response) && isset($response->access_token)) {
            $accessToken = $response->access_token;
        } elseif (is_array($response) && isset($response['access_token'])) {
            $accessToken = $response['access_token'];
        }

        if (isset($accessToken)) {
            $this->setAccessToken($accessToken);
            $this->initSession($accessToken);
        } else {
            $this->getLogger()->error(
                'Cant find access token in response',
                [
                    'class'    => $this,
                    'response' => $response,
                ]
            );
            throw new FacebookException('Cant find access token in response');
        }
    }

    /**
     * TODO: Create normal method, because now it is only for test
     *
     * @return ProfileInterface
     */
    public function getMyProfile()
    {
        $response = (new FacebookRequest(
            $this->getSession(), 'GET', '/me'
        ))->execute()->getResponse();

        $result = new Profile(
            $response->id,
            $response->first_name,
            $response->last_name,
            $response->email,
            ($response->gender === 'male') ? 'male'
                : (($response->gender === 'female') ? 'female' : null),
            null,
            null
        );

        return $result;
    }

    /**
     * @return bool
     */
    public function postOnMyWall()
    {

    }

    /**
     * @return ProfileInterface[]
     */
    public function getMyFriends()
    {

    }

    /**
     * @param string|int $memberId
     *
     * @return ProfileInterface
     */
    public function getMyFriend($memberId)
    {

    }
}
