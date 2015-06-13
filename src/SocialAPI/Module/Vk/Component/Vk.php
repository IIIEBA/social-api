<?php

namespace SocialAPI\Module\Vk\Component;

use GuzzleHttp\Client;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use SocialAPI\Lib\Component\ApiConfigInterface;
use SocialAPI\Lib\Component\ApiInterface;
use SocialAPI\Lib\Model\ApiResponse\Profile;
use SocialAPI\Lib\Model\ApiResponse\ProfileInterface;
use SocialAPI\Lib\Util\Logger\LoggerTrait;
use SocialAPI\Module\Vk\Exception\VkModuleException;
use Symfony\Component\HttpFoundation\Request;

class Vk implements ApiInterface, LoggerAwareInterface
{
    use LoggerTrait;

    /**
     * Vk API version
     */
    const API_VERSION = '5.34';

    /**
     * Url to API action for request oauth code
     */
    const OAUTH_CODE_URL = 'https://oauth.vk.com/authorize';

    /**
     * Url to API action for generation access code
     */
    const ACCESS_TOKEN_URL = 'https://oauth.vk.com/access_token';

    /**
     * Url for all authorized API actions
     */
    const API_URL = 'https://api.vk.com/method/';

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var ApiConfigInterface
     */
    private $config;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * List of fields which need to get
     * @var string
     */
    private $profileFieldsList;

    /**
     * @param ApiConfigInterface $config
     * @param Request $request
     * @param LoggerInterface $logger
     *
     * @internal param null|string $accessToken
     */
    public function __construct(ApiConfigInterface $config, Request $request, LoggerInterface $logger = null)
    {
        $this->config  = $config;
        $this->request = $request;

        if ($logger !== null) {
            $this->setLogger($logger);
        }

        $this->initApi();
    }

    /**
     * @return Client
     */
    public function getHttpClient()
    {
        if ($this->httpClient === null) {
            $this->httpClient = new Client([
                'allow_redirects'   => true,
                'exceptions'        => false,
            ]);
        }

        return $this->httpClient;
    }

    /**
     * @return ApiConfigInterface
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return array
     */
    public function getProfileFieldsList()
    {
        return $this->profileFieldsList;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        if (!is_string($accessToken)) {
            $msg = 'Only string allowed for accessToken';
            $this->getLogger()->error(
                $msg,
                [
                    'object' => $this
                ]
            );
            throw new \InvalidArgumentException($msg);
        }

        $this->accessToken = $accessToken;
    }

    /**
     * Init api method
     */
    public function initApi()
    {
        $this->profileFieldsList = implode(
            ',' ,
            [
                'sex',
                'bdate',
                'photo_max_orig',
                'contacts',
                'email'
            ]
        );
    }

    /**
     * Generate redirect url for vk auth
     * @return string
     */
    public function generateLoginUrl()
    {
        $scopeList = array_flip($this->getConfig()->getScopeList());
        if (isset($scopeList['nohttps'])) {
            unset($scopeList['nohttps']);
        }
        $scopeList = array_flip($scopeList);

        $params = http_build_query([
            'client_id'     => $this->getConfig()->getAppId(),
            'scope'         => implode(',', $scopeList),
            'redirect_uri'  => $this->getConfig()->getRedirectUrl(),
            'response_type' => 'code',
            'v'             => self::API_VERSION,
            'state'         => 'test',
        ]);

        return self::OAUTH_CODE_URL . '?' . $params;
    }

    /**
     * @return string
     */
    public function generateLogoutUrl()
    {
        // TODO: Implement generateLogoutUrl() method.

        return $this->getConfig()->getRedirectUrl();
    }

    /**
     * Parse request from API and generate access token
     * @return string
     * @throws VkModuleException
     */
    public function parseLoginResponse()
    {
        $accessToken = null;
        if ($this->getRequest()->get('code') !== null) {
            if (
                $this->getRequest()->get('state') === null
                || !isset($_SESSION['state'])
                || $this->getRequest()->get('state') != $_SESSION['state']
            ) {
                $msg = 'State doesnt match in request and response';
                $this->getLogger()->error(
                    $msg,
                    [
                        'object' => $this,
                    ]
                );

                throw new VkModuleException($msg);
            }

            $accessToken = $this->generateAccessTokenFromCode($this->getRequest()->get('code'));
        } elseif ($this->getRequest()->get('error') !== null) {
            $msg = 'Failed to parse response from Vk API';
            $this->getLogger()->error(
                $msg,
                [
                    'object' => $this,
                ]
            );

            throw new VkModuleException($msg);
        }

        if ($accessToken === null) {
            $msg = 'Strange response was given from Vk API';
            $this->getLogger()->error(
                $msg,
                [
                    'object' => $this,
                ]
            );

            throw new VkModuleException($msg);
        }

        return $accessToken;
    }

    /**
     * Generate access token from code
     * @param string $code
     *
     * @return string
     * @throws VkModuleException
     */
    public function generateAccessTokenFromCode($code)
    {
        $params = [
            'client_id'     => $this->getConfig()->getAppId(),
            'client_secret' => $this->getConfig()->getAppSecret(),
            'code'          => $code,
            'redirect_uri'  => $this->getConfig()->getRedirectUrl(),
        ];

        try {
            $response = $this->getHttpClient()->post(self::ACCESS_TOKEN_URL, ['form_params' => $params]);
        } catch (\Exception $e) {
            $msg = 'Fail to send http request to API';
            $this->getLogger()->error(
                $msg,
                [
                    'object'    => $this,
                    'exception' => $e,
                ]
            );

            throw new VkModuleException($msg);
        }

        if (empty($response->getBody())) {
            $msg = 'Request to API return empty result';
            $this->getLogger()->error(
                $msg,
                [
                    'object'     => $this,
                    'statusCode' => $response->getStatusCode(),
                ]
            );

            throw new VkModuleException($msg);
        }


        $result = json_decode($response->getBody());
        if (isset($result->error) || !isset($result->access_token)) {
            $msg = 'Request to API was unsuccessful with error: ' . $result->error_description;
            $this->getLogger()->error(
                $msg,
                [
                    'object'        => $this,
                    'statusCode'    => $response->getStatusCode(),
                    'result'        => $result,
                ]
            );

            throw new VkModuleException($msg);
        }

        $this->setAccessToken($result->access_token);

        return $this->getAccessToken();
    }

    /**
     * Prepare data for calling selected API action and call it
     * @param string $method
     * @param array $params
     *
     * @return object
     * @throws VkModuleException
     */
    public function callApiMethod($method, array $params = [])
    {
        if ($this->getAccessToken() === null) {
            $msg = 'You need to set access token before use API methods';
            $this->getLogger()->error(
                $msg,
                [
                    'object' => $this,
                ]
            );

            throw new VkModuleException($msg);
        }

        if (!is_string($method)) {
            $msg = 'Only string allowed for method name';
            $this->getLogger()->error(
                $msg,
                [
                    'object' => $this,
                ]
            );

            throw new VkModuleException($msg);
        }

        $params = array_merge(['access_token' => $this->getAccessToken()], $params);

        try {
            $response = $this->getHttpClient()->post(self::API_URL . $method, ['form_params' => $params]);
        } catch (\Exception $e) {
            $msg = 'Fail to send http request to API';
            $this->getLogger()->error(
                $msg,
                [
                    'object'    => $this,
                    'exception' => $e,
                ]
            );

            throw new VkModuleException($msg);
        }

        if (empty($response->getBody())) {
            $msg = 'Request to API return empty result';
            $this->getLogger()->error(
                $msg,
                [
                    'object'     => $this,
                    'statusCode' => $response->getStatusCode(),
                ]
            );

            throw new VkModuleException($msg);
        }

        $result = json_decode($response->getBody());
        if (isset($result->error)) {
            $msg = 'Request to API was unsuccessful with error: ' . $result->error->error_msg;
            $this->getLogger()->error(
                $msg,
                [
                    'object'        => $this,
                    'action'        => $method,
                    'params'        => $params,
                    'statusCode'    => $response->getStatusCode(),
                    'result'        => $result,
                ]
            );

            throw new VkModuleException($msg);
        }

        return $result;
    }

    /**
     * @throws VkModuleException
     */
    public function postOnMyWall()
    {
        throw new VkModuleException('This action is not available for sites by ip :(');
    }

    /**
     * Get profile data for current user
     * @return ProfileInterface
     */
    public function getMyProfile()
    {
        return $this->getProfile();
    }

    /**
     * Return list of friends profiles
     * @return ProfileInterface[]
     */
    public function getFriends()
    {
        $result     = [];
        $response   = $this->callApiMethod('friends.get', ['fields' => $this->getProfileFieldsList()]);

        foreach ($response->response as $profile) {
            $result[] = new Profile(
                $profile->uid,
                $profile->first_name,
                $profile->last_name,
                null,
                $this->parseGender($profile->sex),
                $this->parseBirthday($profile->bdate),
                $this->parseAvatarUrl($profile->photo_max_orig)
            );
        }

        return $result;
    }

    /**
     * Get selected profile data
     * @param string|null $memberIds
     *
     * @return ProfileInterface
     */
    public function getProfile($memberIds = null)
    {
        $params = [
            'fields' => $this->getProfileFieldsList()
        ];

        if ($memberIds !== null) {
            if (!is_string($memberIds)) {
                $msg = 'Only string type allowed for member id';
                $this->getLogger()->error(
                    $msg,
                    [
                        'object' => $this,
                    ]
                );

                throw new \InvalidArgumentException($msg);
            }

            $params['user_ids'] = $memberIds;
        }

        $response = $this->callApiMethod('users.get', $params);

        $profile = reset($response->response);

        return new Profile(
            $profile->uid,
            $profile->first_name,
            $profile->last_name,
            null,
            $this->parseGender($profile->sex),
            $this->parseBirthday($profile->bdate),
            $this->parseAvatarUrl($profile->photo_max_orig)
        );
    }

    /**
     * Convert Vk gender to single format
     * @param null|int $gender
     *
     * @return null|string
     */
    public function parseGender($gender = null)
    {
        if ($gender !== null) {
            if ($gender === 1) {
                $gender = 'female';
            } elseif ($gender === 2) {
                $gender = 'male';
            } else {
                $gender = null;
            }
        }

        return $gender;
    }

    /**
     * @param null $birthday
     *
     * @return \DateTimeImmutable|null
     */
    public function parseBirthday($birthday = null)
    {
        if ($birthday !== null) {
            $parsedDate = explode('.', $birthday);
            if (count($parsedDate) === 3) {
                $birthday = new \DateTimeImmutable($birthday);
            } else {
                $birthday = null;
            }
        }

        return $birthday;
    }

    /**
     * Convert Vk avatar url to general format
     * @param null|string $url
     *
     * @return null
     */
    public function parseAvatarUrl($url = null)
    {
        if ($url !== null) {
            if ($url === 'http://vk.com/images/camera_a.gif') {
                $url = null;
            }
        }

        return $url;
    }
}
