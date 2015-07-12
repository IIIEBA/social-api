<?php

namespace SocialAPI\Module\GitHub\Component;

use SocialAPI\Lib\Component\BaseApi;
use SocialAPI\Lib\Exception\BaseApiException;
use SocialAPI\Lib\Exception\InvalidArgument\NotStringException;
use SocialAPI\Lib\Model\ApiResponse\Profile;
use SocialAPI\Lib\Model\ApiResponse\ProfileInterface;
use SocialAPI\Lib\Model\Enum\RequestMethod;
use SocialAPI\Lib\Model\Enum\ResponseType;
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
     * Request method to API
     */
    const METHOD = RequestMethod::GET;

    /**
     * API response type
     */
    const RESPONSE_TYPE = ResponseType::JSON;

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

    /**
     * Get current profile data
     * @return ProfileInterface
     * @throws BaseApiException
     */
    public function getMyProfile()
    {
        $profile    = $this->callApiMethod(
            self::API_URL . 'user',
            [],
            new RequestMethod(self::METHOD),
            new ResponseType(self::RESPONSE_TYPE)
        );
        $email      = $this->getMyEmail();

        $name = $this->parseName($profile->name);

        return new Profile(
            $profile->id,
            $name['first'],
            $name['last'],
            $email,
            $this->parseGender(null),
            $this->parseBirthday(null),
            $this->parseAvatarUrl($profile->avatar_url)
        );
    }

    /**
     * Get user primary email
     * @return string|null
     * @throws BaseApiException
     */
    public function getMyEmail()
    {
        $result = null;
        $emails = $this->callApiMethod(
            self::API_URL . 'user/emails',
            [],
            new RequestMethod(self::METHOD),
            new ResponseType(self::RESPONSE_TYPE)
        );

        foreach ($emails as $email) {
            if ($email->primary) {
                $result = $email->email;
                break;
            }
        }

        return $result;
    }

    /**
     * Post message on member wall
     * @throws GitHubModuleException
     */
    public function postOnMyWall()
    {
        throw new GitHubModuleException('This action is not available for sites by api :(');
    }

    /**
     * Get list of member friends with basic data
     * @throws GitHubModuleException
     */
    public function getFriends()
    {
        throw new GitHubModuleException('This action is not available for sites by api :(');
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
        if (!is_string($memberId)) {
            throw new NotStringException('memberId');
        }

        $method = "users/{$memberId}";

        $profile = $this->callApiMethod(
            self::API_URL . $method,
            [],
            new RequestMethod(self::METHOD),
            new ResponseType(self::RESPONSE_TYPE)
        );
        $name    = $this->parseName($profile->name);

        return new Profile(
            $profile->id,
            $name['first'],
            $name['last'],
            $profile->email,
            $this->parseGender(null),
            $this->parseBirthday(null),
            $this->parseAvatarUrl($profile->avatar_url)
        );
    }

    /**
     * Convert name to first and last name
     * @param string $name
     * @return array
     */
    public function parseName($name)
    {
        if (!is_string($name)) {
            throw new NotStringException('name');
        }

        $parts = explode(" ", trim($name));

        return [
            'first' => reset($parts),
            'last'  => end($parts),
        ];
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
        return $gender;
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
        return $birthday;
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
        return $url;
    }
}
