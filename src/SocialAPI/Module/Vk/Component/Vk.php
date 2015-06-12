<?php

namespace SocialAPI\Module\Vk\Component;

use SocialAPI\Lib\Component\ApiConfigInterface;
use SocialAPI\Lib\Component\ApiInterface;
use SocialAPI\Lib\Model\ApiResponse\ProfileInterface;
use Symfony\Component\HttpFoundation\Request;

class Vk implements ApiInterface
{

    /**
     * @param ApiConfigInterface $config
     * @param Request $request
     * @param null|string $accessToken
     */
    public function __construct(ApiConfigInterface $config, Request $request, $accessToken = null)
    {
        // TODO: Implement __construct() method.
    }

    /**
     * @return ApiConfigInterface
     */
    public function getConfig()
    {
        // TODO: Implement getConfig() method.
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        // TODO: Implement getRequest() method.
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        // TODO: Implement getAccessToken() method.
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        // TODO: Implement setAccessToken() method.
    }

    /**
     * @param string|int $appId
     * @param string $appSecret
     */
    public function initApi($appId, $appSecret)
    {
        // TODO: Implement initApi() method.
    }

    /**
     * @return string
     */
    public function generateLoginUrl()
    {
        // TODO: Implement generateLoginUrl() method.
    }

    /**
     * @return string
     */
    public function generateLogoutUrl()
    {
        // TODO: Implement generateLogoutUrl() method.
    }

    /**
     * Parse request for code variable and request access token by it
     *
     * @return void
     */
    public function generateAccessTokenFromCode()
    {
        // TODO: Implement generateAccessTokenFromCode() method.
    }

    /**
     * @return ProfileInterface
     */
    public function getMyProfile()
    {
        // TODO: Implement getMyProfile() method.
    }

    /**
     * @return bool
     */
    public function postOnMyWall()
    {
        // TODO: Implement postOnMyWall() method.
    }

    /**
     * @return ProfileInterface[]
     */
    public function getMyFriends()
    {
        // TODO: Implement getMyFriends() method.
    }

    /**
     * @param string|int $memberId
     *
     * @return ProfileInterface
     */
    public function getMyFriend($memberId)
    {
        // TODO: Implement getMyFriend() method.
    }
}
