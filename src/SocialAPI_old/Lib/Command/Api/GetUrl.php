<?php

namespace SocialAPI\Lib\Command\Api;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetUrl extends Command
{
    protected function configure()
    {
        $this
            ->setName('api:get-url')
            ->setDescription('Get authorization url')
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
