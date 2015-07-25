<?php

namespace SocialAPI\Lib\Component;

use SocialAPI\Lib\Command\Api\GetAccessToken;
use SocialAPI\Lib\Command\Api\GetUrl;
use SocialAPI\Lib\Command\Api\RenewAccessToken;
use SocialAPI\Lib\Command\Api\GetStatus;
use Symfony\Component\Console\Application;

/**
 * Class SocialApiCommand
 * @package SocialAPI\Lib\Componen
 */
class SocialApiCommand
{
    /**
     * @var self
     */
    private static $self;

    /**
     * Singletone getInstance method
     * @return SocialApiCommand
     */
    private static function getInstance()
    {
        if (self::$self === null) {
            self::$self = new self();
        }

        return self::$self;
    }

    /**
     * Init method
     */
    public static function run()
    {
        self::getInstance()->initConsole();
    }

    /**
     * Load all module commands
     * @throws \Exception
     */
    private function initConsole()
    {
        $app = new Application();

        $app->add(new GetStatus());
        $app->add(new GetUrl());
        $app->add(new GetAccessToken());
        $app->add(new RenewAccessToken());

        $app->run();
    }
}
