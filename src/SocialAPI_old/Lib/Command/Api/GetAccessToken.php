<?php

namespace SocialAPI\Lib\Command\Api;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetAccessToken extends Command
{
    protected function configure()
    {
        $this
            ->setName('api:get-token')
            ->setDescription('Get access token by requested code')
            ->addArgument(
                'api',
                InputArgument::REQUIRED,
                'API name'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
