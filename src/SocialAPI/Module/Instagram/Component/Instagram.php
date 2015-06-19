<?php

namespace SocialAPI\Module\Instagram\Component;

use SocialAPI\Lib\Component\ApiInterface;
use SocialAPI\Lib\Component\BaseApi;
use SocialAPI\Lib\Model\ApiResponse\Profile;
use SocialAPI\Lib\Model\ApiResponse\ProfileInterface;
use SocialAPI\Module\Instagram\Exception\InstagramModuleException;

class Instagram extends BaseApi implements ApiInterface
{
    /**
     * Url to API action for request oauth code
     */
    const OAUTH_CODE_URL = 'https://api.instagram.com/oauth/authorize/';

    /**
     * Url to API for access tolken request
     */
    const ACCESS_TOKEN_URL = 'https://api.instagram.com/oauth/access_token';

    /**
     * Base API url
     */
    const API_URL = 'https://api.instagram.com/v1/';

    /**
     * Init api method
     */
    public function initApi()
    {
        // Do nothing
    }

    /**
     * Generate login url
     * @return string
     */
    public function generateLoginUrl()
    {
        $params = http_build_query([
            'client_id'     => $this->getConfig()->getAppId(),
            'scope'         => implode(' ', $this->getConfig()->getScopeList()),
            'redirect_uri'  => $this->getConfig()->getRedirectUrl(),
            'response_type' => 'code',
            'state'         => 'test',
        ]);

        return self::OAUTH_CODE_URL . '?' . $params;
    }

    /**
     * Generate logout url
     * @return string
     */
    public function generateLogoutUrl()
    {
        return '/';
    }

    /**
     * Generate access token from code
     * @param string $code
     * @return string
     *
     * @throws InstagramModuleException
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
            'grant_type'    => 'authorization_code',
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

            throw new InstagramModuleException($msg);
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

            throw new InstagramModuleException($msg);
        }


        $result = json_decode($response->getBody());
        if (isset($result->error) || !isset($result->access_token)) {
            $msg = 'Request to API was unsuccessful with error: ' . $result->error_message;
            $this->getLogger()->error(
                $msg,
                [
                    'object'        => $this,
                    'statusCode'    => $response->getStatusCode(),
                    'result'        => $result,
                ]
            );

            throw new InstagramModuleException($msg);
        }

        $this->setAccessToken($result->access_token);

        return $this->getAccessToken();
    }

    /**
     * Call selected API method and check result, if ok - return it
     * @param $url
     * @param array $params
     * @return mixed
     *
     * @throws InstagramModuleException
     */
    public function callApiMethod($url, array $params = [])
    {
        if ($this->getAccessToken() === null) {
            $msg = 'You need to set access token before use API methods';
            $this->getLogger()->error(
                $msg,
                [
                    'object' => $this,
                ]
            );

            throw new InstagramModuleException($msg);
        }

        if (!is_string($url)) {
            $msg = 'Only string allowed for url';
            $this->getLogger()->error(
                $msg,
                [
                    'object' => $this,
                ]
            );

            throw new InstagramModuleException($msg);
        }

        $params = array_merge(['access_token' => $this->getAccessToken()], $params);

        try {
            $response = $this->getHttpClient()->get($url . '?' . http_build_query($params));
            echo $response->getStatusCode();
        } catch (\Exception $e) {
            $msg = 'Fail to send http request to API';
            $this->getLogger()->error(
                $msg,
                [
                    'object'    => $this,
                    'url'       => $url,
                    'params'    => $params,
                    'exception' => $e,
                ]
            );

            throw new InstagramModuleException($msg);
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

            throw new InstagramModuleException($msg);
        }

        $result = json_decode($response->getBody());
        if (isset($result->meta->error_type)) {
            $msg = 'Request to API was unsuccessful with error: ' . $result->meta->error_message;
            $this->getLogger()->error(
                $msg,
                [
                    'object'        => $this,
                    'url'           => $url,
                    'params'        => $params,
                    'statusCode'    => $result->meta->code,
                    'result'        => $result,
                ]
            );

            throw new InstagramModuleException($msg);
        }

        return $result;
    }

    /**
     * Get my profile data
     * @return ProfileInterface
     */
    public function getMyProfile()
    {
        return $this->getProfile('self');
    }

    /**
     * Post message on member wall
     * @return bool
     *
     * @throws InstagramModuleException
     */
    public function postOnMyWall()
    {
        throw new InstagramModuleException('This action is not available for sites by ip :(');
    }

    /**
     * Get list of member friends with basic data
     * @return \SocialAPI\Lib\Model\ApiResponse\ProfileInterface[]
     *
     * @throws InstagramModuleException
     */
    public function getFriends()
    {
        throw new InstagramModuleException('This action is not available for sites by ip :(');
    }

    /**
     * Get selected profile data
     * @param string|null $memberId
     * @return ProfileInterface
     */
    public function getProfile($memberId)
    {
        $url        = self::API_URL . "users/{$memberId}/";
        $response   = $this->callApiMethod($url);
        $profile    = $response->data;

        $firstName  = $profile->username;
        $lastName   = null;
        if (!empty($profile->full_name)) {
            $nameParts = explode(' ',trim($profile->full_name));
            $firstName = array_shift($nameParts);

            if (!empty($nameParts)) {
                $lastName = array_shift($nameParts);
            }
        }

        return new Profile(
            $profile->id,
            $firstName,
            $lastName,
            null,
            $this->parseGender(),
            $this->parseBirthday(),
            $this->parseAvatarUrl($profile->profile_picture)
        );
    }

    /**
     * Convert API gender to single format
     * @param null|int $gender
     * @return null|string
     */
    public function parseGender($gender = null)
    {
        return null;
    }

    /**
     * Convert API birthday to single format
     * @param null $birthday
     * @return \DateTimeImmutable|null
     */
    public function parseBirthday($birthday = null)
    {
        return null;
    }

    /**
     * Convert API avatar url to general format
     * @param null|string $url
     * @return null
     */
    public function parseAvatarUrl($url = null)
    {
        return $url;
    }
}
