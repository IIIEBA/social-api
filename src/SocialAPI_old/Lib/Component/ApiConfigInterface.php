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
     * Get app id
     * @return string
     */
    public function getAppId();

    /**
     * Get app secret
     * @return string
     */
    public function getAppSecret();

    /**
     * Get redirect url
     * @return string
     */
    public function getRedirectUrl();

    /**
     * Get scope list
     * @return string[]
     */
    public function getScopeList();
}
