<?php

namespace SocialAPI\Lib\Util\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class LoggerTrait
 * @package SocialAPI\Lib\Util
 */
trait LoggerTrait
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        if ($this->logger === null) {
            $this->logger = new NullLogger();
        }

        return $this->logger;
    }
}
