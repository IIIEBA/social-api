<?php

namespace SocialAPI\Lib\Component;

interface ApiInterface
{
    public function getConfig();

    public function getAccessToken();

    public function setAccessToken($accessToken);

    public function initApi();

    public function generateLoginUrl();
}
