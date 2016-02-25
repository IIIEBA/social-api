<?php

namespace SocialApi\Module;

use BaseExceptions\Exception\InvalidArgument\EmptyStringException;
use BaseExceptions\Exception\InvalidArgument\NotStringException;
use Psr\Log\LoggerInterface;
use SocialApi\Lib\ApiInterface;
use SocialApi\Lib\Component\BaseApi;
use SocialApi\Lib\Exception\NotAllowed\NoActionException;
use SocialApi\Lib\Exception\SocialApiException;
use SocialApi\Lib\Model\AccessToken;
use SocialApi\Lib\Model\AccessTokenInterface;
use SocialApi\Lib\Model\ApiConfigInterface;
use SocialApi\Lib\Model\Enum\Gender;
use SocialApi\Lib\Model\Enum\RequestMethod;
use SocialApi\Lib\Model\Enum\ResponseType;
use SocialApi\Lib\Model\Profile;
use SocialApi\Lib\Model\ProfileInterface;

/**
 * Class VkApi
 * @package SocialApi\Module
 */
class VkApi extends BaseApi implements ApiInterface
{
    /**
     * Vk API version
     */
    const API_VERSION = '5.45';

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
     * @var string[]
     */
    private $profileFieldsList;

    /**
     * VkApi constructor.
     *
     * @param ApiConfigInterface $apiConfig
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        ApiConfigInterface $apiConfig,
        LoggerInterface $logger = null
    ) {
        parent::__construct($apiConfig, $logger);

        $this->profileFieldsList = [
            'sex',
            'bdate',
            'photo_max_orig',
            'contacts',
            'email'
        ];
    }


    /**
     * Get profile fields list
     *
     * @return string[]
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
        $this->profileFieldsList = [
            'sex',
            'bdate',
            'photo_max_orig',
            'contacts',
            'email'
        ];
    }

    /**
     * Generate login url
     *
     * @return string
     */
    public function generateLoginUrl()
    {
        $scopeList = array_flip($this->getApiConfig()->getScopeList());
        if (isset($scopeList['nohttps'])) {
            unset($scopeList['nohttps']);
        }

        $params = http_build_query([
            'client_id'     => $this->getApiConfig()->getAppId(),
            'scope'         => implode(',', $scopeList),
            'redirect_uri'  => $this->getApiConfig()->getRedirectUrl(),
            'response_type' => 'code',
            'v'             => self::API_VERSION,
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
            ],
            false
        );

        if (!isset($result->access_token)) {
            throw new SocialApiException("Access token missed in response");
        }

        $this->setAccessToken(
            new AccessToken(
                $result->access_token,
                $result->expires_in ? new \DateTimeImmutable(date("c", time() + $result->expires_in)) : null
            )
        );

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
        $params = [
            'fields' => implode(',', $this->getProfileFieldsList()),
        ];

        if (!is_null($id)) {
            if (!is_string($id)) {
                throw new NotStringException("id");
            }
            if (empty($id)) {
                throw new EmptyStringException("id");
            }
            $params['user_ids'] = $id;
        }

        $response = $this->callApiMethod(
            self::API_URL . 'users.get',
            new RequestMethod(RequestMethod::POST),
            new ResponseType(ResponseType::JSON),
            $params
        );

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
     */
    public function getFriends()
    {
        $result = [];

        $response = $this->callApiMethod(
            self::API_URL . 'friends.get',
            new RequestMethod(RequestMethod::POST),
            new ResponseType(ResponseType::JSON),
            ['fields' => implode(',', $this->getProfileFieldsList())]
        );

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
     * Convert API gender to single format
     *
     * @param null|string $gender
     * @return Gender
     */
    public function parseGender($gender = null)
    {
        if ($gender === 1) {
            $gender = new Gender(Gender::FEMALE);
        } elseif ($gender === 2) {
            $gender = new Gender(Gender::MALE);
        } else {
            $gender = new Gender(Gender::UNKNOWN);
        }

        return $gender;
    }

    /**
     * Convert API birthday to single format
     *
     * @param null|string $birthday
     * @return \DateTimeInterface|null
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
     * Convert API avatar url to general format
     *
     * @param null|string $url
     * @return string|null
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
