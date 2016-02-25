<?php

namespace SocialApi\Module;

use SocialApi\Lib\ApiInterface;
use SocialApi\Lib\Component\BaseApi;
use SocialAPI\Lib\Model\Enum\Gender;
use SocialApi\Lib\Model\ProfileInterface;

/**
 * Class VkApi
 * @package SocialApi\Module
 */
class VkApi extends BaseApi implements ApiInterface
{
    /**
     * Generate login url
     *
     * @return string
     */
    public function generateLoginUrl()
    {
        // TODO: Implement generateLoginUrl() method.
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
     * @return string
     */
    public function generateAccessTokenFromCode($code)
    {
        // TODO: Implement generateAccessTokenFromCode() method.
    }

    /**
     * Get list of app permission for member
     *
     * @return string[]
     */
    public function getPermissions()
    {
        // TODO: Implement getPermissions() method.
    }

    /**
     * Get profile data by id
     *
     * @param string $id
     * @return ProfileInterface
     */
    public function getProfileById($id)
    {
        // TODO: Implement getProfileById() method.
    }

    /**
     * Post message on member wall
     *
     * @return bool
     */
    public function postOnWall()
    {
        // TODO: Implement postOnWall() method.
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
     * Convert API gender to single format
     *
     * @param null|string $gender
     * @return Gender
     */
    public function parseGender($gender = null)
    {
        // TODO: Implement parseGender() method.
    }

    /**
     * Convert API birthday to single format
     *
     * @param null|string $birthday
     * @return \DateTimeInterface|null
     */
    public function parseBirthday($birthday = null)
    {
        // TODO: Implement parseBirthday() method.
    }

    /**
     * Convert API avatar url to general format
     *
     * @param null|string $url
     * @return string|null
     */
    public function parseAvatarUrl($url = null)
    {
        // TODO: Implement parseAvatarUrl() method.
    }
}
