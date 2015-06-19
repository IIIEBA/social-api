<?php

namespace SocialAPI\Module\Vk\Component;

use GuzzleHttp\Client;
use SocialAPI\Lib\Component\BaseApi;
use SocialAPI\Lib\Component\ApiInterface;
use SocialAPI\Lib\Model\ApiResponse\Profile;
use SocialAPI\Lib\Model\ApiResponse\ProfileInterface;
use SocialAPI\Module\Vk\Exception\VkModuleApiException;

class Vk extends BaseApi implements ApiInterface
{
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
     * List of fields which need to get
     * @var string
     */
    private $profileFieldsList;

    /**
     * Get http client
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
     * Get profile fields list
     * @return array
     */
    public function getProfileFieldsList()
    {
        return $this->profileFieldsList;
    }

    /**
     * Init api method
     */
    public function initApi()
    {
        $this->profileFieldsList = implode(
            ',',
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
     * Return logout link which will be called after general flow
     * @return string
     */
    public function generateLogoutUrl()
    {
        return '/';
    }

    /**
     * Generate access token from code
     *
*@param string $code
     *
*@return string
     * @throws VkModuleApiException
     */
    public function generateAccessTokenFromCode($code)
    {
        if (!is_string($code)) {
            $msg = 'Only string allowed for code';
            $this->getLogger()->error(
                $msg,
                [
                    'object' => $this,
                ]
            );

            throw new \InvalidArgumentException($msg);
        }

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

            throw new VkModuleApiException($msg);
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

            throw new VkModuleApiException($msg);
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

            throw new VkModuleApiException($msg);
        }

        $this->setAccessToken($result->access_token);

        return $this->getAccessToken();
    }

    /**
     * Prepare data for calling selected API action and call it
     * @param string $method
     * @param array $params
     * @return object
     * @throws VkModuleApiException
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

            throw new VkModuleApiException($msg);
        }

        if (!is_string($method)) {
            $msg = 'Only string allowed for method name';
            $this->getLogger()->error(
                $msg,
                [
                    'object' => $this,
                ]
            );

            throw new VkModuleApiException($msg);
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

            throw new VkModuleApiException($msg);
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

            throw new VkModuleApiException($msg);
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

            throw new VkModuleApiException($msg);
        }

        return $result;
    }

    /**
     * Post msg on my wall
     *
     * @throws VkModuleApiException
     */
    public function postOnMyWall()
    {
        throw new VkModuleApiException('This action is not available for sites by ip :(');
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
     * @param string|null $memberId
     * @return ProfileInterface
     */
    public function getProfile($memberId = null)
    {
        $params = [
            'fields' => $this->getProfileFieldsList()
        ];

        if ($memberId !== null) {
            if (!is_string($memberId)) {
                $msg = 'Only string type allowed for member id';
                $this->getLogger()->error(
                    $msg,
                    [
                        'object' => $this,
                    ]
                );

                throw new \InvalidArgumentException($msg);
            }

            $params['user_ids'] = $memberId;
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
     * Convert Vk birthday to general format
     * @param null $birthday
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
