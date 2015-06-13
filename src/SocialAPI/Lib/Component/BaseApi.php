<?php

namespace SocialAPI\Lib\Component;

use GuzzleHttp\Client;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use SocialAPI\Lib\Exception\BaseApiException;
use SocialAPI\Lib\Model\ApiResponse\ProfileInterface;
use SocialAPI\Lib\Util\Logger\LoggerTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BaseApi
 *
 * @package SocialAPI\Lib\Component
 */
abstract class BaseApi implements ApiInterface, LoggerAwareInterface
{
    use LoggerTrait;

    /**
     * @var ApiConfigInterface
     */
    protected $config;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * Get config
     * @return ApiConfigInterface
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get request
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get access token
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set access token
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        if (!is_string($accessToken)) {
            $msg = 'Only string allowed for accessToken';
            $this->getLogger()->error(
                $msg,
                [
                    'object' => $this
                ]
            );
            throw new \InvalidArgumentException($msg);
        }

        $this->accessToken = $accessToken;
    }

    /**
     * Get http client
     * @return Client
     */
    public function getHttpClient()
    {
        if ($this->httpClient === null) {
            $this->httpClient = new Client([
                'allow_redirects'   => true,
                'exceptions'        => false,
            ]);
        }

        return $this->httpClient;
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
     * Parse request from API and generate access token
     * @return string
     *
     * @throws BaseApiException
     */
    public function parseLoginResponse()
    {
        $accessToken = null;
        if ($this->getRequest()->get('code') !== null) {
            if (
                $this->getRequest()->get('state') === null
                || !isset($_SESSION['state'])
                || $this->getRequest()->get('state') != $_SESSION['state']
            ) {
                $msg = 'State doesnt match in request and response';
                $this->getLogger()->error(
                    $msg,
                    [
                        'object' => $this,
                    ]
                );

                throw new BaseApiException($msg);
            }

            $accessToken = $this->generateAccessTokenFromCode($this->getRequest()->get('code'));
        } elseif ($this->getRequest()->get('error') !== null) {
            $msg = 'Failed to parse response from Facebook API';
            $this->getLogger()->error(
                $msg,
                [
                    'object' => $this,
                ]
            );

            throw new BaseApiException($msg);
        }

        if ($accessToken === null) {
            $msg = 'Strange response was given from Facebook API';
            $this->getLogger()->error(
                $msg,
                [
                    'object' => $this,
                ]
            );

            throw new BaseApiException($msg);
        }

        return $accessToken;
    }


    /**
     * Get my profile data
     * @return ProfileInterface
     */
    public function getMyProfile()
    {
        return $this->getProfile(null);
    }
}
