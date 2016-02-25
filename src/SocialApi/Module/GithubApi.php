<?php

namespace SocialApi\Module;

use BaseExceptions\Exception\InvalidArgument\EmptyStringException;
use BaseExceptions\Exception\InvalidArgument\NotStringException;
use SocialApi\Lib\ApiInterface;
use SocialApi\Lib\Component\BaseApi;
use SocialApi\Lib\Exception\NotAllowed\NoActionException;
use SocialApi\Lib\Exception\SocialApiException;
use SocialAPI\Lib\Model\Enum\Gender;
use SocialAPI\Lib\Model\Enum\RequestMethod;
use SocialAPI\Lib\Model\Enum\ResponseType;
use SocialApi\Lib\Model\Profile;
use SocialApi\Lib\Model\ProfileInterface;

/**
 * Class GitHubApi
 * @package SocialApi\Module
 */
class GitHubApi extends BaseApi implements ApiInterface
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
     * Generate login url
     *
     * @return string
     */
    public function generateLoginUrl()
    {
        $scopeList = $this->getApiConfig()->getScopeList();

        $params = http_build_query([
            'client_id'     => $this->getApiConfig()->getAppId(),
            'scope'         => implode(',', $scopeList),
            'redirect_uri'  => $this->getApiConfig()->getRedirectUrl(),
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
        // TODO: Implement generateLogoutUrl() method.

        return "/";
    }

    /**
     * Generate access token from code
     *
     * @param string $code
     * @return string
     * @throws SocialApiException
     */
    public function generateAccessTokenFromCode($code)
    {
        if (!is_string($code)) {
            throw new NotStringException('code');
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
            ],
            false
        );

        if (!isset($result->access_token)) {
            throw new SocialApiException("Access token missed in response");
        }

        $this->setAccessToken($result->access_token);

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
     * Get current user profile data
     *
     * @return ProfileInterface
     */
    public function getCurrentProfile()
    {
        $profile = $this->callApiMethod(
            self::API_URL . 'user',
            new RequestMethod(RequestMethod::GET),
            new ResponseType(ResponseType::JSON)
        );
        $email = $this->getMyEmail();
        list($firstName, $lastName) = $parts = explode(" ", trim($profile->name));

        return new Profile(
            $profile->id,
            $firstName,
            $lastName,
            $email,
            $this->parseGender(null),
            $this->parseBirthday(null),
            $this->parseAvatarUrl($profile->avatar_url)
        );
    }

    /**
     * Get email for current profile
     *
     * @return string|null
     */
    public function getMyEmail()
    {
        $result = null;
        $emails = $this->callApiMethod(
            self::API_URL . 'user/emails',
            new RequestMethod(RequestMethod::GET),
            new ResponseType(ResponseType::JSON)
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
     * Get profile data by id
     *
     * @param string $id
     * @return ProfileInterface
     * @throws NoActionException
     */
    public function getProfileById($id)
    {
        if (!is_string($id)) {
            throw new NotStringException("id");
        }
        if (empty($id)) {
            throw new EmptyStringException("id");
        }

        $profile = $this->callApiMethod(
            self::API_URL . "users/{$id}",
            new RequestMethod(RequestMethod::GET),
            new ResponseType(ResponseType::JSON)
        );
        list($firstName, $lastName) = $parts = explode(" ", trim($profile->name));

        return new Profile(
            $profile->id,
            $firstName,
            $lastName,
            $profile->email,
            $this->parseGender(null),
            $this->parseBirthday(null),
            $this->parseAvatarUrl($profile->avatar_url)
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
     * @return ProfileInterface[]
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
        // TODO: Implement parseGender() method.

        return null;
    }

    /**
     * Convert API birthday to single format
     *
     * @param null|string $birthday
     * @return \DateTimeInterface|null
     */
    public function parseBirthday($birthday = null)
    {
        // TODO: Implement parseBirthday() method.

        return null;
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
