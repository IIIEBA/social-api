<?php

namespace SocialAPI\Module\Instagram\Component;

use SocialAPI\Lib\Component\ApiInterface;
use SocialAPI\Lib\Component\BaseApi;
use SocialAPI\Lib\Model\ApiResponse\Profile;
use SocialAPI\Lib\Model\ApiResponse\ProfileInterface;
use SocialAPI\Lib\Model\Enum\RequestMethod;
use SocialAPI\Lib\Model\Enum\ResponseType;
use SocialAPI\Module\Instagram\Exception\InstagramModuleException;

class Instagram extends BaseApi implements ApiInterface
{
    /**
     * Url to API action for request oauth code
     */
    const OAUTH_CODE_URL = 'https://api.instagram.com/oauth/authorize/';

    /**
     * Url to API for access token request
     */
    const ACCESS_TOKEN_URL = 'https://api.instagram.com/oauth/access_token';

    /**
     * Base API url
     */
    const API_URL = 'https://api.instagram.com/v1/';

    /**
     * Request method to API
     */
    const METHOD = RequestMethod::GET;

    /**
     * API Response type
     */
    const RESPONSE_TYPE = ResponseType::JSON;

    /**
     * Init api method
     */
    public function initApi()
    {
        // Do nothing for this API
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
        throw new InstagramModuleException('This action is not available for sites by api :(');
    }

    /**
     * Get list of member friends with basic data
     * @return \SocialAPI\Lib\Model\ApiResponse\ProfileInterface[]
     *
     * @throws InstagramModuleException
     */
    public function getFriends()
    {
        throw new InstagramModuleException('This action is not available for sites by api :(');
    }

    /**
     * Get selected profile data
     * @param string|null $memberId
     * @return ProfileInterface
     */
    public function getProfile($memberId)
    {
        $url        = self::API_URL . "users/{$memberId}/";
        $response   = $this->callApiMethod(
            $url,
            new RequestMethod(self::METHOD),
            new ResponseType(self::RESPONSE_TYPE)
        );
        $profile    = $response->data;

        $firstName  = $profile->username;
        $lastName   = null;
        if (!empty($profile->full_name)) {
            $nameParts = explode(' ', trim($profile->full_name));
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
            $this->parseGender(null),
            $this->parseBirthday(null),
            $this->parseAvatarUrl($profile->profile_picture)
        );
    }

    /**
     * Convert API gender to single format
     * @param int|null $gender
     * @return string|null
     */
    public function parseGender($gender = null)
    {
        return $gender;
    }

    /**
     * Convert API birthday to single format
     * @param string|null $birthday
     * @return \DateTimeImmutable|null
     */
    public function parseBirthday($birthday = null)
    {
        return $birthday;
    }

    /**
     * Convert API avatar url to general format
     * @param string|null $url
     * @return string|null
     */
    public function parseAvatarUrl($url = null)
    {
        return $url;
    }
}
