<?php

namespace SocialApi\Lib\Model;

/**
 * Class AccessToken
 * @package SocialApi\Lib\Model
 */
interface AccessTokenInterface
{
    /**
     * @return string
     */
    public function getToken();

    /**
     * @return \DateTimeInterface|null
     */
    public function getExpireAt();

    /**
     * @return null|string
     */
    public function getRenewToken();
}
