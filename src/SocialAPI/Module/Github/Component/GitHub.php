<?php

namespace SocialAPI\Module\GitHub\Component;

use SocialAPI\Lib\Component\BaseApi;
use SocialAPI\Lib\Exception\InvalidArgument\NotStringException;
use SocialAPI\Lib\Exception\SocialApiException;
use SocialAPI\Lib\Model\ApiResponse\ProfileInterface;
use SocialAPI\Module\GitHub\Exception\GitHubModuleException;

class GitHub extends BaseApi
{
       /**
     * Url to API action for request oauth code
     */
    const OAUTH_CODE_URL = 'https://github.com/login/oauth/authorize';

    /**
     * Url to API action for generation access code
     */
    const ACCESS_TOKEN_URL = 'https://github.com/login/oauth/access_token';

    /**
     * Url for all authorized API actions
     */
    const API_URL = 'https://api.github.com/';

    /**
     * Init api method
     */
    public function initApi()
    {
        // TODO: Implement initApi() method.
    }

    /**
     * Generate login url
     *
     * @return string
     */
    public function generateLoginUrl()
    {
        $scopeList = $this->getConfig()->getScopeList();

        $params = http_build_query([
            'client_id'     => $this->getConfig()->getAppId(),
            'scope'         => implode(',', $scopeList),
            'redirect_uri'  => $this->getConfig()->getRedirectUrl(),
            'state'         => 'test',
        ]);

        return self::OAUTH_CODE_URL . '?' . $params;
    }

    /**
     * Generate logout url
     *
     * @return string
     */
    public function generateLogoutUrl()
    {
        return '/';
    }

    /**
     * Generate access token from code, set and return it
     * @param string $code
     * @return string
     * @throws GitHubModuleException
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

            throw new NotStringException('code');
        }

        $params = [
            'client_id'     => $this->getConfig()->getAppId(),
            'client_secret' => $this->getConfig()->getAppSecret(),
            'code'          => $code,
            'redirect_uri'  => $this->getConfig()->getRedirectUrl(),
        ];

        try {
            $response = $this->getHttpClient()->post(
                self::ACCESS_TOKEN_URL,
                [
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                    'form_params' => $params,
                ]
            );
        } catch (\Exception $e) {
            $msg = 'Fail to send http request to API';
            $this->getLogger()->error(
                $msg,
                [
                    'object'    => $this,
                    'exception' => $e,
                ]
            );

            throw new GitHubModuleException($msg);
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

            throw new GitHubModuleException($msg);
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

            throw new GitHubModuleException($msg);
        }

        $this->setAccessToken($result->access_token);

        return $this->getAccessToken();
    }

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

            throw new GitHubModuleException($msg);
        }

        if (!is_string($method)) {
            $msg = 'Only string allowed for method name';
            $this->getLogger()->error(
                $msg,
                [
                    'object' => $this,
                ]
            );

            throw new NotStringException('method');
        }

        $params = array_merge(['access_token' => $this->getAccessToken()], $params);

        var_dump(self::API_URL . $method);
        try {
            $response = $this->getHttpClient()->post(
                self::API_URL . $method,
                [
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                    'form_params' => $params,
                ]
            );
        } catch (\Exception $e) {
            $msg = 'Fail to send http request to API';
            $this->getLogger()->error(
                $msg,
                [
                    'object'    => $this,
                    'exception' => $e,
                ]
            );

            throw new GitHubModuleException($msg);
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

            throw new GitHubModuleException($msg);
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

            throw new GitHubModuleException($msg);
        }

        return $result;
    }

    public function getMyProfile()
    {
        return $this->callApiMethod('user');
    }

    /**
     * Post message on member wall
     *
     * @return bool
     */
    public function postOnMyWall()
    {
        // TODO: Implement postOnMyWall() method.
    }

    /**
     * Get list of member friends with basic data
     *
     * @return ProfileInterface[]
     */
    public function getFriends()
    {
        // TODO: Implement getFriends() method.
    }

    /**
     * Get selected profile data
     *
     * @param string|null $memberId
     *
     * @return ProfileInterface
     */
    public function getProfile($memberId)
    {
        $method = "users";
        if ($memberId !== null) {
            if (!is_string($memberId)) {
                throw new NotStringException('memberId');
            }

            $method .= "/{$memberId}";
        }

        return $this->callApiMethod($method . $memberId);
    }

    /**
     * Convert API gender to single format
     *
     * @param null|int $gender
     *
     * @return null|string
     */
    public function parseGender($gender = null)
    {
        // TODO: Implement parseGender() method.
    }

    /**
     * Convert API birthday to single format
     *
     * @param null $birthday
     *
     * @return \DateTimeImmutable|null
     */
    public function parseBirthday($birthday = null)
    {
        // TODO: Implement parseBirthday() method.
    }

    /**
     * Convert API avatar url to general format
     *
     * @param null|string $url
     *
     * @return null
     */
    public function parseAvatarUrl($url = null)
    {
        // TODO: Implement parseAvatarUrl() method.
}}
