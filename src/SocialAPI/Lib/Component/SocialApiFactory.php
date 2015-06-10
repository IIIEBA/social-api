<?php

namespace SocialAPI\Lib\Component;

use Psr\Log\LoggerAwareInterface;
use SocialAPI\Lib\Util\LoggerTrait;

class SocialApiFactory implements LoggerAwareInterface
{
    use LoggerTrait;

    public function __construct()
    {

    }
}
