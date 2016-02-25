<?php

namespace SocialApi\Lib\Model;

use SocialAPI\Lib\Model\Enum\Gender;

/**
 * Class Profile
 * @package SocialApi\Lib\Model
 */
interface ProfileInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @return string
     */
    public function getFirstName();

    /**
     * @return null|string
     */
    public function getLastName();

    /**
     * @return null|string
     */
    public function getEmail();

    /**
     * @return Gender
     */
    public function getGender();

    /**
     * @return \DateTimeInterface
     */
    public function getBirthday();

    /**
     * @return null|string
     */
    public function getAvatarUrl();
}
