<?php

namespace SocialAPI\Module\Instagram\Component;

use SocialAPI\Lib\Component\ApiInterface;
use SocialAPI\Lib\Component\BaseApi;
use SocialAPI\Lib\Model\ApiResponse\ProfileInterface;

class Instagram extends BaseApi implements ApiInterface
{
    /**
     * Url to API action for request oauth code
     */
    const OAUTH_CODE_URL = 'https://api.instagram.com/oauth/authorize/';

    /**
     * Init api method
     */
    public function initApi()
    {
        // TODO: Implement initApi() method.
    }

    /**
     * Generate login url
     *
     * @return string
     */
    public function generateLoginUrl()
    {
        $params = http_build_query([
            'client_id'     => $this->getConfig()->getAppId(),
            'scope'         => implode('+', $this->getConfig()->getScopeList()),
            'redirect_uri'  => $this->getConfig()->getRedirectUrl(),
            'response_type' => 'code',
            'state'         => 'test',
        ]);

        return self::OAUTH_CODE_URL . '?' . $params;
    }

    /**
     * Generate logout url
     *
     * @return string
     */
    public function generateLogoutUrl()
    {
        // TODO: Implement generateLogoutUrl() method.
    }

    /**
     * Generate access token from code
     *
     * @param string $code
     *
     * @return string
     */
    public function generateAccessTokenFromCode($code)
    {
        // TODO: Implement generateAccessTokenFromCode() method.
    }

    /**
     * Post message on member wall
     *
     * @return bool
     */
    public function postOnMyWall()
    {
        // TODO: Implement postOnMyWall() method.
    }

    /**
     * Get list of member friends with basic data
     *
     * @return ProfileInterface[]
     */
    public function getFriends()
    {
        // TODO: Implement getFriends() method.
    }

    /**
     * Get selected profile data
     *
     * @param string|null $memberId
     *
     * @return ProfileInterface
     */
    public function getProfile($memberId)
    {
        // TODO: Implement getProfile() method.
    }

    /**
     * Convert API gender to single format
     *
     * @param null|int $gender
     *
     * @return null|string
     */
    public function parseGender($gender = null)
    {
        // TODO: Implement parseGender() method.
    }

    /**
     * Convert API birthday to single format
     *
     * @param null $birthday
     *
     * @return \DateTimeImmutable|null
     */
    public function parseBirthday($birthday = null)
    {
        // TODO: Implement parseBirthday() method.
    }

    /**
     * Convert API avatar url to general format
     *
     * @param null|string $url
     *
     * @return null
     */
    public function parseAvatarUrl($url = null)
    {
        // TODO: Implement parseAvatarUrl() method.
}}
