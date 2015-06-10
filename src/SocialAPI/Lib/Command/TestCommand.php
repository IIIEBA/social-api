<?php

namespace SocialAPI\Lib\Command;

use SocialAPI\Module\Facebook\Component\Facebook;
use SocialAPI\Module\Facebook\Component\FacebookConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    public function configure()
    {
        $this->setName('test:facebook')
            ->setDescription('Facebook API integration tests');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $config = new FacebookConfig(
            1412497612344354,
            '18f0694ffd8d0eb6efbaec59fd9947b0',
            'my.site.com/auth/api/callback/facebook',
            [
                'email',
                'public_profile',
                'user_friends',
            ]
        );

        $facebook = new Facebook($config);
        $output->writeln($facebook->generateLoginUrl());
    }
}
