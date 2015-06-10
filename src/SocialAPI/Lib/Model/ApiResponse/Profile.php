<?php

namespace SocialAPI\Lib\Model\ApiResponse;

class Profile implements ProfileInterface
{
    /**
     * @var mixed
     */
    private $id;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string|null
     */
    private $email;

    /**
     * @var string|null [male|female]
     */
    private $gender;

    /**
     * @var \DateTime|null
     */
    private $birthday;

    /**
     * @var string
     */
    private $avatarUrl;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string|null [male|female]
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @return \DateTime|null
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @return string|null
     */
    public function getAvatarUrl()
    {
        return $this->avatarUrl;
    }

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
    ) {
        $this->id           = $id;
        $this->firstName    = $firstName;
        $this->lastName     = $lastName;
        $this->email        = $email;
        $this->gender       = $gender;
        $this->birthday     = $birthday;
        $this->avatarUrl    = $avatarUrl;
    }
}
