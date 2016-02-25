<?php

namespace SocialApi\Module;

use BaseExceptions\Exception\InvalidArgument\EmptyStringException;
use BaseExceptions\Exception\InvalidArgument\NotStringException;
use BaseExceptions\Exception\LogicException\NotImplementedException;
use SocialApi\Lib\ApiInterface;
use SocialApi\Lib\Component\BaseApi;
use SocialApi\Lib\Exception\NotAllowed\NoActionException;
use SocialApi\Lib\Exception\SocialApiException;
use SocialApi\Lib\Model\AccessToken;
use SocialApi\Lib\Model\AccessTokenInterface;
use SocialApi\Lib\Model\Enum\Gender;
use SocialApi\Lib\Model\Enum\RequestMethod;
use SocialApi\Lib\Model\Enum\ResponseType;
use SocialApi\Lib\Model\Profile;
use SocialApi\Lib\Model\ProfileInterface;

/**
 * Class InstagramApi
 * @package SocialApi\Module
 */
class InstagramApi extends BaseApi implements ApiInterface
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
     * Generate login url
     * @return string
     */
    public function generateLoginUrl()
    {
        $params = http_build_query([
            'client_id'     => $this->getApiConfig()->getAppId(),
            'scope'         => implode(' ', $this->getApiConfig()->getScopeList()),
            'redirect_uri'  => $this->getApiConfig()->getRedirectUrl(),
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
        // TODO: Implement generateLogoutUrl() method.

        return "/";
    }

    /**
     * Generate access token from code
     *
     * @param string $code
     * @return AccessTokenInterface
     * @throws SocialApiException
     */
    public function generateAccessTokenFromCode($code)
    {
        if (!is_string($code)) {
            throw new NotStringException("code");
        }
        if (empty($code)) {
            throw new EmptyStringException("code");
        }

        $result = $this->callApiMethod(
            self::ACCESS_TOKEN_URL,
            new RequestMethod(RequestMethod::POST),
            new ResponseType(ResponseType::JSON),
            [
                'client_id'     => $this->getApiConfig()->getAppId(),
                'client_secret' => $this->getApiConfig()->getAppSecret(),
                'code'          => $code,
                'redirect_uri'  => $this->getApiConfig()->getRedirectUrl(),
                'grant_type'    => 'authorization_code',
            ],
            false
        );

        $this->setAccessToken(new AccessToken($result->access_token));

        return $this->getAccessToken();
    }

    /**
     * Get list of app permission for member
     *
     * @return string[]
     */
    public function getPermissions()
    {
        // TODO: Implement getPermissions() method.
    }

    /**
     * Get profile data by id
     *
     * @param string $id
     * @return ProfileInterface
     */
    public function getProfileById($id)
    {
        if (!is_null($id)) {
            if (!is_string($id)) {
                throw new NotStringException("id");
            }
            if (empty($id)) {
                throw new EmptyStringException("id");
            }
        } else {
            $id = "self";
        }

        $response = $this->callApiMethod(
            self::API_URL . "users/{$id}/",
            new RequestMethod(RequestMethod::GET),
            new ResponseType(ResponseType::JSON)
        );

        $profile = $response->data;
        $firstName = $profile->username;
        $lastName = null;
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
            null,
            null,
            $this->parseAvatarUrl($profile->profile_picture)
        );
    }

    /**
     * Post message on member wall
     *
     * @return bool
     * @throws NoActionException
     */
    public function postOnWall()
    {
        throw new NoActionException();
    }

    /**
     * Get list of member friends with basic data
     *
     * @return \SocialApi\Lib\Model\ProfileInterface[]
     * @throws NoActionException
     */
    public function getFriends()
    {
        throw new NoActionException();
    }

    /**
     * Convert API gender to single format
     *
     * @param null|string $gender
     * @return Gender
     */
    public function parseGender($gender = null)
    {
        throw new NotImplementedException();
    }

    /**
     * Convert API birthday to single format
     *
     * @param null|string $birthday
     * @return \DateTimeInterface|null
     */
    public function parseBirthday($birthday = null)
    {
        throw new NotImplementedException();
    }

    /**
     * Convert API avatar url to general format
     *
     * @param null|string $url
     * @return string|null
     */
    public function parseAvatarUrl($url = null)
    {
        return $url;
    }
}
