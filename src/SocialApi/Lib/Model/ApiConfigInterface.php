<?php

namespace SocialApi\Lib\Model;

use SocialApi\Lib\Model\Enum\ApiName;

/**
 * Class ApiConfig
 * @package SocialApi\Lib\Model
 */
interface ApiConfigInterface
{
    /**
     * @return ApiName
     */
    public function getName();

    /**
     * @return string
     */
    public function getAppId();

    /**
     * @return string
     */
    public function getAppSecret();

    /**
     * @return string
     */
    public function getRedirectUrl();

    /**
     * @return array
     */
    public function getScopeList();

    /**
     * @return boolean
     */
    public function isEnabled();
}
