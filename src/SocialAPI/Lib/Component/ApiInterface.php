<?php

namespace SocialAPI\Lib\Component;

use Psr\Log\LoggerInterface;
use SocialAPI\Lib\Model\ApiResponse\ProfileInterface;
use Symfony\Component\HttpFoundation\Request;

interface ApiInterface
{
    /**
     * @param ApiConfigInterface $config
     * @param Request $request
     * @param LoggerInterface $logger
     */
    public function __construct(ApiConfigInterface $config, Request $request, LoggerInterface $logger = null);

    /**
     * @return ApiConfigInterface
     */
    public function getConfig();

    /**
     * @return Request
     */
    public function getRequest();

    /**
     * @return string
     */
    public function getAccessToken();

    /**
     * @param string $accessToken
     */
    public function setAccessToken($accessToken);

    /**
     * Init api method
     */
    public function initApi();

    /**
     * @return string
     */
    public function generateLoginUrl();

    /**
     * @return string
     */
    public function generateLogoutUrl();

    public function parseLoginResponse();

    /**
     * Parse request for code variable and request access token by it
     * @param string $code
     *
     * @return string
     */
    public function generateAccessTokenFromCode($code);

    /**
     * @return bool
     */
    public function postOnMyWall();

    /**
     * @return ProfileInterface
     */
    public function getMyProfile();

    /**
     * @return ProfileInterface[]
     */
    public function getFriends();

    /**
     * @param string|null $memberIds
     *
     * @return ProfileInterface
     */
    public function getProfile($memberIds);
}
