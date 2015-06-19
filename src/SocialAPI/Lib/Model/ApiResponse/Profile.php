<?php

namespace SocialAPI\Lib\Model\ApiResponse;

/**
 * Class Profile
 *
 * @package SocialAPI\Lib\Model\ApiResponse
 */
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
     * Get id
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get first name
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Get last name
     * @return string|null
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Get email
     * @return string|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get gender
     * @return string|null [male|female]
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Get birthday
     * @return \DateTime|null
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Get avatar url
     * @return string|null
     */
    public function getAvatarUrl()
    {
        return $this->avatarUrl;
    }

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
    ) {
        if (is_int($id)) {
            if ($id < 1) {
                throw new \InvalidArgumentException('If id is int, it must be greater then 0');
            }
        } elseif (!is_string($id)) {
            throw new \InvalidArgumentException('Only int or string allowed for id');
        }

        if (!is_string($firstName)) {
            throw new \InvalidArgumentException('Only string allowed for firstName');
        }

        if ($lastName !== null && !is_string($lastName)) {
            throw new \InvalidArgumentException('Only string or null allowed for lastName');
        }

        if ($email !== null && !is_string($email)) {
            throw new \InvalidArgumentException('Only string or null allowed for email');
        }

        if ($gender !== null && (!is_string($gender) || !in_array($gender, ['male', 'female']))) {
            throw new \InvalidArgumentException('Only string or null allowed for gender');
        }

        if ($birthday !== null && !($birthday instanceof \DateTimeImmutable)) {
            throw new \InvalidArgumentException('Only string or DateTimeImmutable allowed for birthday');
        }

        if ($avatarUrl !== null && !is_string($avatarUrl)) {
            throw new \InvalidArgumentException('Only string or null allowed for avatarUrl');
        }

        $this->id           = $id;
        $this->firstName    = $firstName;
        $this->lastName     = $lastName;
        $this->email        = $email;
        $this->gender       = $gender;
        $this->birthday     = $birthday;
        $this->avatarUrl    = $avatarUrl;
    }
}
