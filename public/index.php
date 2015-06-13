<?php
    use SocialAPI\Module\Facebook\Component\Facebook;
    use SocialAPI\Module\Facebook\Component\FacebookConfigApiConfig;
    use Symfony\Component\HttpFoundation\Request;

    // Looking for composer
    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        require __DIR__ . '/../vendor/autoload.php';
    } else {
        echo "\n****** No composer autoload file was found ******\n\n";
        exit;
    }

    session_start();

    $config = new FacebookConfigApiConfig(
        1412497612344354,
        '18f0694ffd8d0eb6efbaec59fd9947b0',
        'http://apis.home-server.pp.ua/',
        [
            'email',
            'public_profile',
            'user_friends',
        ]
    );

//    $request = new Request();
//
//    $facebook = new Facebook($config, $request);
//    echo $facebook->generateLoginUrl();exit;

//
//    $code = 'AQDG2d0lwNOMOQ0PksAwhl4rFhQf8lkaQX5wu5zAaDRDWaPdzUaVsk1U--vTwAYNLEg7ttnFmj_kuOBW9WCKfJ_N3wTdeGhZfedtxUQwTJ9edTzRtOZv2cSEf3lZqL4d9IY8duTQloFry7azIZ7fVog7C9MgQLt_zmsP4159Wi7XngtVBxzWZ_HjYOuvmuBMPh0pIvAGy_U5XVMTFDX7cnrAWGjt90VvkHsBMzrc0iRz77HLcF0lkdAu692oj1T33r5YmXTvGQMu6iqxepv56hI61wRF0yXr5K-H0F3zstIDDUS8jnV6X0cAc0xP_gwx8YP73xr09VJ5jwX12yCZu13q';
//    $request = new Request([], ['code' => $code]);
//
//    $facebook = new Facebook($config, $request);
//    $facebook->generateAccessTokenFromCode();
//

    $access  = 'CAAUEqLpuDCIBAFiK7cQARIrB03DbaPuMECpD9bhC48GWDGeEMTZCBRFDZC1Wu7yfF7TRXEsBvxLpX5wnzrGe4UdJhvLgawELRVEwZBOQohd9ugdEtQZBXRZAGyTMszLQv02hy8jpBo46tKYKqFente3YHZCCK0ZC5YECflutuevIQGZCC36aU5NBUk9yZCxG390rZCzCE2ZAqcdrpSDfOav2b6I';
    $request = new Request();

    $facebook = new Facebook($config, $request, $access);
    $profile = $facebook->getMyProfile();

    echo "<pre>";
    print_r($profile);

    echo $profile->getId();

    echo "<br/>" . $facebook->generateLogoutUrl();
