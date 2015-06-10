<?php

namespace SocialAPI\Lib\Component;

interface ApiConfigInterface
{
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
