<?php

namespace SocialApi\Lib;

use GuzzleHttp\Client;
use SocialApi\Lib\Exception\SocialApiException;
use SocialApi\Lib\Model\AccessTokenInterface;
use SocialApi\Lib\Model\ApiConfigInterface;
use SocialApi\Lib\Model\Enum\Gender;
use SocialApi\Lib\Model\ProfileInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApiInterface
 * @package SocialApi\Lib
 */
interface ApiInterface
{
    /**
     * @return ApiConfigInterface
     */
    public function getApiConfig();

    /**
     * @return Client
     */
    public function getHttpClient();

    /**
     * @param AccessTokenInterface $token
     */
    public function setAccessToken(AccessTokenInterface $token);

    /**
     * @return null|AccessTokenInterface
     */
    public function getAccessToken();

    /**
     * Generate login url
     *
     * @return string
     */
    public function generateLoginUrl();

    /**
     * Generate logout url
     *
     * @return string
     */
    public function generateLogoutUrl();

    /**
     * Parse request from API and generate access token
     *
     * @param Request $request
     * @return AccessTokenInterface
     */
    public function parseLoginResponse(Request $request);

    /**
     * Generate access token from code
     *
     * @param string $code
     * @return AccessTokenInterface
     * @throws SocialApiException
     */
    public function generateAccessTokenFromCode($code);

    /**
     * Get list of app permission for member
     *
     * @return string[]
     */
    public function getPermissions();

    /**
     * Get profile data by id
     *
     * @param string $id
     * @return ProfileInterface
     */
    public function getProfileById($id);

    /**
     * Get current user profile data
     *
     * @return ProfileInterface
     */
    public function getCurrentProfile();

    /**
     * Post message on member wall
     *
     * @return bool
     */
    public function postOnWall();

    /**
     * Get list of member friends with basic data
     *
     * @return ProfileInterface[]
     */
    public function getFriends();

    /**
     * Convert API gender to single format
     *
     * @param null|string $gender
     * @return Gender
     */
    public function parseGender($gender = null);

    /**
     * Convert API birthday to single format
     *
     * @param null|string $birthday
     * @return \DateTimeInterface|null
     */
    public function parseBirthday($birthday = null);

    /**
     * Convert API avatar url to general format
     *
     * @param null|string $url
     * @return string|null
     */
    public function parseAvatarUrl($url = null);
}