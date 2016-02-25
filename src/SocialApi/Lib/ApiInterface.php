<?php

namespace SocialApi\Lib;

use GuzzleHttp\Client;
use SocialAPI\Lib\Model\Enum\Gender;
use SocialApi\Lib\Model\ProfileInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiInterface
 * @package SocialApi\Lib
 */
interface ApiInterface
{
    /**
     * @return Client
     */
    public function getHttpClient();

    /**
     * @param string $token
     */
    public function setToken($token);

    /**
     * @return null|string
     */
    public function getToken();

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
     * @param Response $response
     * @return string
     */
    public function parseLoginResponse(Response $response);

    /**
     * Generate access token from code
     *
     * @param string $code
     * @return string
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
     * @return \DateTimeImmutable|null
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
