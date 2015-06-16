<?php

namespace SocialAPI\Lib\Model\ApiResponse;

interface ProfileInterface
{
    /**
     * Get id
     * @return mixed
     */
    public function getId();

    /**
     * Get first anme
     * @return string
     */
    public function getFirstName();

    /**
     * Get last name
     * @return string|null
     */
    public function getLastName();

    /**
     * Get email
     * @return string|null
     */
    public function getEmail();

    /**
     * Get gender
     * @return string|null [male|female]
     */
    public function getGender();

    /**
     * Get birthday
     * @return \DateTimeImmutable|null
     */
    public function getBirthday();

    /**
     * Get avatar url
     * @return string|null
     */
    public function getAvatarUrl();

    /**
     * Set fields to model
     * @param mixed $id
     * @param string $firstName
     * @param null|string $lastName
     * @param null|string $email
     * @param null|string $gender
     * @param null|\DateTimeImmutable $birthday
     * @param null|string $avatarUrl
     */
    public function __construct(
        $id,
        $firstName,
        $lastName   = null,
        $email      = null,
        $gender     = null,
        $birthday   = null,
        $avatarUrl  = null
    );
}
