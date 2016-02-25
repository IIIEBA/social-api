<?php

namespace SocialAPI\Lib\Command\Api;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetProfile extends Command
{
    protected function configure()
    {
        $this
            ->setName('api:get-profile')
            ->setDescription('Get profile data')
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
