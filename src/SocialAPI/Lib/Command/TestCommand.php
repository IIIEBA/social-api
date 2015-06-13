<?php

namespace SocialAPI\Lib\Command;

use SocialAPI\Lib\Component\SocialApi;
use SocialAPI\Module\Facebook\Component\FacebookConfig;
use SocialAPI\Module\Instagram\Component\InstagramConfig;
use SocialAPI\Module\Vk\Component\VkConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    public function configure()
    {
        $this->setName('test:test')
            ->setDescription('Facebook API integration tests')
            ->addArgument('api', InputArgument::REQUIRED, 'With which api we will work?')
            ->addArgument('action', InputArgument::REQUIRED, 'With api action we will test?');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        session_start();
        $_SESSION['state'] = 'test';

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
            new VkConfig(
                true,
                4291109,
                'khVCGw6QRG3W2tEBgq0i',
                'http://apis.home-server.pp.ua/api',
                [
                    'friends',
                    'photos',
                    'status',
                    'email',
                    'offline',
                    'nohttps',
                    'wall',
                ]
            ),
            new InstagramConfig(
                true,
                '0e53afa4e56144c1bf3e9cb68147db5a',
                'ac38e27fa55343cb85aa266960cd48b7',
                'http://apis.home-server.pp.ua/apis',
                [
                    'basic',
                    'comments',
                    'relationships',
                    'likes'
                ]
            )
        ];

        $_GET['code'] = '2ee6d25de33bd0173e';
        $_GET['state'] = 'test';

        $socialApi = new SocialApi($config);

        if ($input->getArgument('api') == 'vk') {
            $accessToken = 'cd7b9e91ecae781403f840840c0f28fc67b3511f85f3cab81445966244da672ae1819e2783a8d91ea7cc8';

            if ($input->getArgument('action') == 'auth_url') {
                $output->writeln($socialApi->getVk()->generateLoginUrl());
            }

            if ($input->getArgument('action') == 'get_access_token') {
                $output->writeln($socialApi->getVk()->parseLoginResponse());
            }

            if ($input->getArgument('action') == 'api') {
                $socialApi->getVk()->setAccessToken($accessToken);
                print_r($socialApi->getVk()->getFriends());
            }
        } elseif ($input->getArgument('api') == 'instagram') {
            if ($input->getArgument('action') == 'auth_url') {
                $output->writeln($socialApi->getInstagram()->generateLoginUrl());
            }
        }

    }
}
