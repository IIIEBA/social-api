<?php

namespace SocialAPI\Lib\Component;

use Psr\Log\LoggerInterface;
use SocialAPI\Lib\Model\ApiResponse\ProfileInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface ApiInterface
 *
 * @package SocialAPI\Lib\Component
 */
interface ApiInterface
{
    /**
     * Set few basic elements of api
     * @param ApiConfigInterface $config
     * @param Request $request
     * @param LoggerInterface $logger
     */
    public function __construct(ApiConfigInterface $config, Request $request, LoggerInterface $logger = null);

    /**
     * Get config
     * @return ApiConfigInterface
     */
    public function getConfig();

    /**
     * Get request
     * @return Request
     */
    public function getRequest();

    /**
     * Get access token
     * @return string
     */
    public function getAccessToken();

    /**
     * Set access token
     * @param string $accessToken
     */
    public function setAccessToken($accessToken);

    /**
     * Init api method
     */
    public function initApi();

    /**
     * Generate login url
     * @return string
     */
    public function generateLoginUrl();

    /**
     * Generate logout url
     * @return string
     */
    public function generateLogoutUrl();

    /**
     * Parse request from API and generate access token
     * @return string
     */
    public function parseLoginResponse();

    /**
     * Generate access token from code
     * @param string $code
     *
     * @return string
     */
    public function generateAccessTokenFromCode($code);

    /**
     * Post message on member wall
     * @return bool
     */
    public function postOnMyWall();

    /**
     * Get my profile data
     * @return ProfileInterface
     */
    public function getMyProfile();

    /**
     * Get list of member friends with basic data
     * @return ProfileInterface[]
     */
    public function getFriends();

    /**
     * Get selected profile data
     * @param string|null $memberId
     *
     * @return ProfileInterface
     */
    public function getProfile($memberId);

    /**
     * Convert API gender to single format
     * @param null|int $gender
     *
     * @return null|string
     */
    public function parseGender($gender = null);

    /**
     * Convert API birthday to single format
     * @param null $birthday
     *
     * @return \DateTimeImmutable|null
     */
    public function parseBirthday($birthday = null);

    /**
     * Convert API avatar url to general format
     * @param null|string $url
     *
     * @return null
     */
    public function parseAvatarUrl($url = null);
}
