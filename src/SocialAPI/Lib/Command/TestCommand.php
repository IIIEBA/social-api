<?php

namespace SocialAPI\Lib\Command;

use SocialAPI\Lib\Component\SocialApi;
use SocialAPI\Module\Facebook\Component\Facebook;
use SocialAPI\Module\Facebook\Component\FacebookConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;

class TestCommand extends Command
{
    public function configure()
    {
        $this->setName('test:test')
            ->setDescription('Facebook API integration tests');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        session_start();

        $config  = [
            new FacebookConfig(
                true,
                1412497612344354,
                '18f0694ffd8d0eb6efbaec59fd9947b0',
                'http://apis.home-server.pp.ua/api',
                [
                    'email',
                    'public_profile',
                    'user_friends',
                ]
            ),
        ];

        //$_GET['code'] = 'AQDpgK6R1J4-6t47XjwHBrkrdqy6y5SqiUPsStR93mFsy0uKPWQ-SOO1RDoEcBC3EJKm-QJp9n1pfaljhntWxXun9dLfADwnfJ5H48EMovwxlLCkDDRf735d7h1bGtsRTQNHMO8otYfBtVGtDBGo1hPh0fC1k3mj7h4RYp6_hrWccj2fvi0YqOg5cVvp6_LiN0vYG8OTtEbBFWsLMb1SDbMYc06yn8xLgbSFN_qTeqyr3e9bMaEtClKVATK6omAFlW1rwGmeOErWtBGNnvhwKVLDBOByE4JJsX0FHWEzTbS-rGsOTf4V7kFF25uEqKRSkYlkgLK8_kM3ZLnFYN_Mpvdl';
        $accessToken = 'CAAUEqLpuDCIBAET9ZCzWCm7zweRMvdo0EZBPrbmLGFsSUifKp7bEUQq6t3CWkzGGnWfBOkXODVVXg3FpGjkbF65FEPB4JjNY20xPHQ5BqQ3TlsqeMjx8y1w9Q1cGbJt3roxusWuUmewi5z58XC7eEkrzQ3S8LrYPbTDGD1ndUp20iyQCVe8pY3eXgIwHEHjpZCmtePqarZCXl5AQvKwn';

        $socialApi = new SocialApi($config);

        //$output->writeln($socialApi->getFacebook()->generateLoginUrl());

//        $socialApi->getFacebook()->generateAccessTokenFromCode();
//        $output->writeln($socialApi->getFacebook()->getAccessToken());

        $socialApi->getFacebook()->setAccessToken($accessToken);
        $test = $socialApi->getFacebook()->getMyProfile();
        $output->writeln(print_r($test));
    }
}
