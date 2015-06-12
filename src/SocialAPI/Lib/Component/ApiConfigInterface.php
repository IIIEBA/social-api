<?php

namespace SocialAPI\Lib\Component;

/**
 * Interface ApiConfigInterface
 *
 * @package SocialAPI\Lib\Component
 */
interface ApiConfigInterface
{
    /**
     * Check API for enabled status
     * @return bool
     */
    public function isEnabled();
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
     * @return string[]
     */
    public function getScopeList();
}
