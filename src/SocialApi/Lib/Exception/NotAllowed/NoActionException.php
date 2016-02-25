<?php

namespace SocialApi\Lib\Exception\NotAllowed;

/**
 * Class NoActionException
 * @package SocialApi\Lib\Exception\NotAllowed
 */
class NoActionException extends NotAllowedException
{
    /**
     * NoActionException constructor.
     */
    public function __construct()
    {
        parent::__construct("Action no implemented on API server");
    }
}
