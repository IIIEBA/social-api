<?php

namespace SocialApi\Lib\Model;

use BaseExceptions\Exception\InvalidArgument\EmptyStringException;
use BaseExceptions\Exception\InvalidArgument\NotPositiveNumericException;
use BaseExceptions\Exception\InvalidArgument\NotStringException;
use SocialApi\Lib\Model\Enum\Gender;

/**
 * Class Profile
 * @package SocialApi\Lib\Model
 */
class Profile implements ProfileInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var null|string
     */
    private $lastName;

    /**
     * @var null|string
     */
    private $email;

    /**
     * @var Gender
     */
    private $gender;

    /**
     * @var \DateTimeInterface
     */
    private $birthday;

    /**
     * @var null|string
     */
    private $avatarUrl;

    /**
     * Profile constructor.
     * @param string $id
     * @param string $firstName
     * @param string|null $lastName
     * @param string|null $email
     * @param Gender $gender
     * @param \DateTimeInterface $birthday
     * @param string|null $avatarUrl
     */
    public function __construct(
        $id,
        $firstName,
        $lastName = null,
        $email = null,
        Gender $gender = null,
        \DateTimeInterface $birthday = null,
        $avatarUrl = null
    ) {
        if (is_int($id)) {
            if ($id < 1) {
                throw new NotPositiveNumericException("id");
            }
        } elseif (is_string($id)) {
            if (empty($id)) {
                throw new EmptyStringException("id");
            }
        } else {
            throw new NotStringException("id");
        }

        if (!is_string($firstName)) {
            throw new NotStringException("firstName");
        } elseif (empty($firstName)) {
            throw new EmptyStringException("firstName");
        }

        if ($lastName !== null) {
            if (!is_string($lastName)) {
                throw new NotStringException("lastName");
            } elseif (empty($lastName)) {
                throw new EmptyStringException("lastName");
            }
        }

        if ($email !== null) {
            if (!is_string($email)) {
                throw new NotStringException("email");
            } elseif (empty($email)) {
                throw new EmptyStringException("email");
            }
        }

        if (is_null($gender)) {
            $gender = new Gender(Gender::UNKNOWN);
        }

        if ($avatarUrl !== null) {
            if (!is_string($avatarUrl)) {
                throw new NotStringException("avatarUrl");
            } elseif (empty($avatarUrl)) {
                throw new EmptyStringException("avatarUrl");
            }
        }

        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->gender = $gender;
        $this->birthday = $birthday;
        $this->avatarUrl = $avatarUrl;
    }

    /**
     * @return string
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
     * @return null|string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return null|string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return Gender
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @return null|string
     */
    public function getAvatarUrl()
    {
        return $this->avatarUrl;
    }
}
