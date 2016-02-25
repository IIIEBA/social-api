<?php

namespace SocialApi\Lib;

use SocialApi\Lib\Exception\SocialApiException;
use SocialApi\Lib\Model\Enum\ApiName;

/**
 * Class SocialApi
 * @package SocialApi\Lib
 */
interface SocialApiInterface
{
    /**
     * Get new instance of api client
     *
     * @param ApiName $name
     * @return ApiInterface
     * @throws SocialApiException
     */
    public function getApi(ApiName $name);

    /**
     * Get list of enabled api`s
     *
     * @return string[]
     */
    public function getEnabledApiList();

    /**
     * Get associative array with service name and login link
     *
     * @return array
     * @throws SocialApiException
     */
    public function getLoginUrlList();
}
