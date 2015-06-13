<?php

namespace SocialAPI\Module\Facebook\Component;

use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookSession;
use Psr\Log\LoggerInterface;
use SocialAPI\Lib\Component\BaseApi;
use SocialAPI\Lib\Component\ApiConfigInterface;
use SocialAPI\Lib\Component\ApiInterface;
use SocialAPI\Lib\Model\ApiResponse\Profile;
use SocialAPI\Lib\Model\ApiResponse\ProfileInterface;
use SocialAPI\Module\Facebook\Exception\FacebookModuleApiException;
use Symfony\Component\HttpFoundation\Request;

class Facebook extends BaseApi implements ApiInterface
{
    /**
     * @var FacebookSession
     */
    private $session;

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

        $this->initApi();
    }

    /**
     * Init Facebook SDK
     *
     * @throws \InvalidArgumentException
     */
    public function initApi()
    {
        FacebookSession::setDefaultApplication($this->getConfig()->getAppId(), $this->getConfig()->getAppSecret());
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
     * Generate access token from code
     *
*@param string $code

     *
*@return string
     * @throws FacebookModuleApiException
     */
    public function generateAccessTokenFromCode($code)
    {
        if (!is_string($code)) {
            $msg = 'Only string allowed for code';
            $this->getLogger()->error(
                $msg,
                [
                    'object' => $this,
                ]
            );

            throw new \InvalidArgumentException($msg);
        }

        // Prepare request params
        $params = [
            'client_id'     => $this->getConfig()->getAppId(),
            'redirect_uri'  => $this->getConfig()->getRedirectUrl(),
            'client_secret' => $this->getConfig()->getAppSecret(),
            'code'          => $code,
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
            throw new FacebookModuleApiException('Failed while making request to facebook API');
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
            throw new FacebookModuleApiException('Cant find access token in response');
        }

        return $accessToken;
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
     * Get profile data for selected member id
     * @param string|null $memberId
     *
     * @return ProfileInterface
     */
    public function getProfile($memberId = null)
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
     * Convert API gender to single format
     * @param null|int $gender
     * @return null|string
     */
    public function parseGender($gender = null)
    {
        // TODO: Implement parseGender() method.
    }

    /**
     * Convert API birthday to single format
     * @param null $birthday
     * @return \DateTimeImmutable|null
     */
    public function parseBirthday($birthday = null)
    {
        // TODO: Implement parseBirthday() method.
    }

    /**
     * Convert API avatar url to general format
     * @param null|string $url
     * @return null
     */
    public function parseAvatarUrl($url = null)
    {
        // TODO: Implement parseAvatarUrl() method.
}}
