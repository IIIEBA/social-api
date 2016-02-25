<?php

namespace SocialApi\Lib\Util\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class LoggerTrait
 *
 * @package SocialAPI\Lib\Util
 */
trait LoggerTrait
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Set selected logger
     * @param LoggerInterface $logger
     * @return void
     */
    public function setLogger(LoggerInterface $logger = null)
    {
        if (is_null($this->logger)) {
            $logger = new NullLogger();
        }

        $this->logger = $logger;
    }

    /**
     * Get logger
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
