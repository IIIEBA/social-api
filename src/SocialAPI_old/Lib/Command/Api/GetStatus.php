<?php

namespace SocialAPI\Lib\Command\Api;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetStatus extends Command
{
    protected function configure()
    {
        $this
            ->setName('api:get-status')
            ->setDescription('Check selected API status')
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
