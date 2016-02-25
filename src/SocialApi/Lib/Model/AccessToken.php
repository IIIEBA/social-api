<?php

namespace SocialApi\Lib\Model;

use BaseExceptions\Exception\InvalidArgument\EmptyStringException;
use BaseExceptions\Exception\InvalidArgument\NotStringException;

/**
 * Class AccessToken
 * @package SocialApi\Lib\Model
 */
class AccessToken implements AccessTokenInterface
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var \DateTimeInterface|null
     */
    private $expireAt;

    /**
     * @var null|string
     */
    private $renewToken;

    /**
     * AccessToken constructor.
     * @param string $token
     * @param \DateTimeInterface|null $expireAt
     * @param string|null $renewToken
     */
    public function __construct(
        $token,
        \DateTimeInterface $expireAt = null,
        $renewToken = null
    ) {
        if (!is_string($token)) {
            throw new NotStringException("token");
        }
        if (empty($token)) {
            throw new EmptyStringException("token");
        }

        if (!is_null($renewToken)) {
            if (!is_string($renewToken)) {
                throw new NotStringException("renewToken");
            }
            if (empty($renewToken)) {
                throw new EmptyStringException("renewToken");
            }
        }

        $this->token = $token;
        $this->expireAt = $expireAt;
        $this->renewToken = $renewToken;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getExpireAt()
    {
        return $this->expireAt;
    }

    /**
     * @return null|string
     */
    public function getRenewToken()
    {
        return $this->renewToken;
    }
}
