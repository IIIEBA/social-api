<?php

namespace SocialAPI\Lib\Model\ApiResponse;

use SocialAPI\Lib\Exception\InvalidArgument\EmptyStringException;
use SocialAPI\Lib\Exception\InvalidArgument\NotStringException;
use SocialAPI\Lib\Model\ApiResponse\Enum\ProfileGender;

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
     * @return \DateTimeImmutable|null
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
     * @param ProfileGender|null $gender
     * @param \DateTimeImmutable|null $birthday
     * @param null|string $avatarUrl
     */
    public function __construct(
        $id,
        $firstName,
        $lastName = null,
        $email = null,
        ProfileGender $gender = null,
        \DateTimeImmutable $birthday = null,
        $avatarUrl = null
    ) {
        if (is_int($id)) {
            if ($id < 1) {
                throw new \InvalidArgumentException('If id is int, it must be greater then 0');
            }
        } elseif (is_string($id)) {
            if ($id === '') {
                throw new EmptyStringException('id');
            }
        } else {
            throw new NotStringException('id');
        }

        if (!is_string($firstName)) {
            throw new NotStringException('firstName');
        } elseif ($firstName === '') {
            throw new EmptyStringException('firstName');
        }

        if ($lastName !== null) {
            if (!is_string($lastName)) {
                throw new NotStringException('lastName');
            } elseif ($lastName === '') {
                throw new EmptyStringException('lastName');
            }
        }

        if ($email !== null) {
            if (!is_string($email)) {
                throw new NotStringException('email');
            } elseif ($email === '') {
                throw new EmptyStringException('email');
            }
        }

        if ($avatarUrl !== null) {
            if (!is_string($avatarUrl)) {
                throw new NotStringException('avatarUrl');
            } elseif ($avatarUrl === '') {
                throw new EmptyStringException('avatarUrl');
            }
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
