<?php

namespace SocialAPI\Module\Facebook\Component;

use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookSession;
use Psr\Log\LoggerInterface;
use SocialAPI\Lib\Component\ApiConfigInterface;
use SocialAPI\Lib\Component\ApiInterface;
use SocialAPI\Lib\Model\ApiResponse\Profile;
use SocialAPI\Lib\Model\ApiResponse\ProfileInterface;
use SocialAPI\Lib\Util\Logger\LoggerTrait;
use SocialAPI\Module\Facebook\Exception\FacebookModuleException;
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
        $this->initSession();
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
     * @param ApiConfigInterface $config
     * @param Request $request
     * @param LoggerInterface $logger
     */
    public function __construct(ApiConfigInterface $config, Request $request, LoggerInterface $logger = null)
    {
        $this->config  = $config;
        $this->request = $request;

        if ($logger !== null) {
            $this->setLogger($logger);
        }

        $this->initApi($this->getConfig()->getAppId(), $this->getConfig()->getAppSecret());
    }

    /**
     * Init Facebook SDK
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

        if (!is_string($appSecret)) {
            $msg = 'Only string allowed for appSecret';
            throw new \InvalidArgumentException($msg);
        }

        FacebookSession::setDefaultApplication($appId, $appSecret);
    }

    /**
     * Init facebook session object from access token
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
     * @return string Access token
     *
     * @throws FacebookModuleException
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
            throw new FacebookModuleException('Failed while making request to facebook API');
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
            throw new FacebookModuleException('Cant find access token in response');
        }

        return $accessToken;
    }

    /**
     * TODO: Create normal method, because now it is only for test
     *
     * @return ProfileInterface
     */
    public function getMyProfile()
    {
        $response = (new FacebookRequest(
            $this->getSession(),
            'GET',
            '/me'
        ))->execute()->getResponse();

        $gender = null;
        if ($response->gender === 'male') {
            $gender = 'male';
        } elseif ($response->gender === 'female') {
            $gender = 'female';
        }

        $result = new Profile(
            $response->id,
            $response->first_name,
            $response->last_name,
            $response->email,
            $gender,
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
    public function getFriends()
    {

    }

    /**
     * @param string|null $memberIds
     *
     * @return ProfileInterface
     */
    public function getProfile($memberIds)
    {

    }
}
