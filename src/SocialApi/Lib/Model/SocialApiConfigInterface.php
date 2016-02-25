<?php

namespace SocialApi\Lib\Model;

/**
 * Class SocialApiConfig
 * @package SocialApi\Lib\Model
 */
interface SocialApiConfigInterface
{
    /**
     * @return ApiConfigInterface[]
     */
    public function getApiConfigList();
}
