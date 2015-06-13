<?php

namespace SocialAPI\Lib\Model\ApiResponse;

interface ProfileInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return string
     */
    public function getFirstName();

    /**
     * @return string|null
     */
    public function getLastName();

    /**
     * @return string|null
     */
    public function getEmail();

    /**
     * @return string|null [male|female]
     */
    public function getGender();

    /**
     * @return \DateTimeImmutable|null
     */
    public function getBirthday();

    /**
     * @return string|null
     */
    public function getAvatarUrl();

    /**
     * @param mixed $id
     * @param string $firstName
     * @param null|string $lastName
     * @param null|string $email
     * @param null|string $gender
     * @param null|\DateTime $birthday
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
