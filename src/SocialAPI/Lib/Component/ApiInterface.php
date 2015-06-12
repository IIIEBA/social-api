<?php

namespace SocialAPI\Lib\Component;

use SocialAPI\Lib\Model\ApiResponse\ProfileInterface;
use Symfony\Component\HttpFoundation\Request;

interface ApiInterface
{
    /**
     * @param ApiConfigInterface $config
     * @param Request $request
     * @param null|string $accessToken
     */
    public function __construct(ApiConfigInterface $config, Request $request, $accessToken = null);

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
     * @param string|int $appId
     * @param string $appSecret
     */
    public function initApi($appId, $appSecret);

    /**
     * @return string
     */
    public function generateLoginUrl();

    /**
     * @return string
     */
    public function generateLogoutUrl();

    /**
     * Parse request for code variable and request access token by it
     */
    public function generateAccessTokenFromCode();

    /**
     * @return ProfileInterface
     */
    public function getMyProfile();

    /**
     * @return bool
     */
    public function postOnMyWall();

    /**
     * @return ProfileInterface[]
     */
    public function getMyFriends();

    /**
     * @param string|int $memberId
     *
     * @return ProfileInterface
     */
    public function getMyFriend($memberId);
}
