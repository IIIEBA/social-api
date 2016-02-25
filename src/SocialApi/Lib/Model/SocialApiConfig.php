<?php

namespace SocialApi\Lib\Model;

use BaseExceptions\Exception\InvalidArgument\EmptyArrayException;

/**
 * Class SocialApiConfig
 * @package SocialApi\Lib\Model
 */
class SocialApiConfig implements SocialApiConfigInterface
{
    /**
     * @var array
     */
    private $apiConfigList;

    /**
     * SocialApiConfig constructor.
     *
     * @param ApiConfigInterface[] $apiConfigList
     */
    public function __construct(
        array $apiConfigList
    ) {
        if (empty($apiConfigList)) {
            throw new EmptyArrayException("apiConfigList");
        }

        $this->apiConfigList = $apiConfigList;
    }

    /**
     * @return ApiConfigInterface[]
     */
    public function getApiConfigList()
    {
        return $this->apiConfigList;
    }
}
