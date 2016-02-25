<?php

namespace SocialApi\Module;

use BaseExceptions\Exception\InvalidArgument\EmptyStringException;
use BaseExceptions\Exception\InvalidArgument\NotStringException;
use Facebook\Facebook;
use Psr\Log\LoggerInterface;
use SocialApi\Lib\ApiInterface;
use SocialApi\Lib\Component\BaseApi;
use SocialApi\Lib\Exception\SocialApiException;
use SocialApi\Lib\Model\AccessToken;
use SocialApi\Lib\Model\AccessTokenInterface;
use SocialApi\Lib\Model\ApiConfigInterface;
use SocialApi\Lib\Model\Enum\Gender;
use SocialApi\Lib\Model\Profile;
use SocialApi\Lib\Model\ProfileInterface;

/**
 * Class FacebookApi
 * @package SocialApi\Module
 */
class FacebookApi extends BaseApi implements ApiInterface
{
    /**
     * @var Facebook
     */
    private $facebook;

    /**
     * FacebookApi constructor.
     * @param ApiConfigInterface $apiConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        ApiConfigInterface $apiConfig,
        LoggerInterface $logger = null
    ) {
        parent::__construct($apiConfig, $logger);

        $this->facebook = new Facebook([
            'app_id' => $this->getApiConfig()->getAppId(),
            'app_secret' => $this->getApiConfig()->getAppSecret(),
            'default_graph_version' => 'v2.5',
        ]);
    }

    /**
     * Generate login url
     *
     * @return string
     */
    public function generateLoginUrl()
    {
        return $this->facebook->getRedirectLoginHelper()->getLoginUrl(
            $this->getApiConfig()->getRedirectUrl(),
            $this->getApiConfig()->getScopeList()
        );
    }

    /**
     * Generate logout url
     *
     * @return string
     */
    public function generateLogoutUrl()
    {
        return $this->facebook->getRedirectLoginHelper()->getLogoutUrl(
            $this->getAccessToken()->getToken(),
            "/"
        );
    }

    /**
     * @param AccessTokenInterface $token
     */
    public function setAccessToken(AccessTokenInterface $token)
    {
        $this->token = $token;
        $this->facebook->setDefaultAccessToken($token->getToken());
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

        // Set correct code to GET, because our SDK would take it from there
        $_GET["code"] = $code;

        try {
            $token = $this->facebook->getOAuth2Client()->getLongLivedAccessToken(
                $this->facebook->getRedirectLoginHelper()->getAccessToken(
                    $this->getApiConfig()->getRedirectUrl()
                )
            );

            $this->setAccessToken(
                new AccessToken(
                    $token->getValue(),
                    $token->getExpiresAt()
                )
            );
        } catch (\Exception $error) {
            throw new SocialApiException("Cant get access token with message: " . $error->getMessage(), 0, $error);
        }

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
     * @throws SocialApiException
     */
    public function getCurrentProfile()
    {
        try {
            $response = $this->facebook->get('/me');
            $userNode = $response->getGraphUser();
        } catch (\Exception $error) {
            throw new SocialApiException("Cant get current user with message: " . $error->getMessage(), 0, $error);
        }

        $result = new Profile(
            $userNode->getId(),
            $userNode->getFirstName(),
            $userNode->getLastName(),
            $userNode->getEmail(),
            $this->parseGender($userNode->getGender()),
            $this->parseBirthday($userNode->getBirthday()),
            $this->parseAvatarUrl($userNode->getPicture())
        );
        return $result;
    }

    /**
     * Get profile data by id
     *
     * @param string $id
     * @return ProfileInterface
     */
    public function getProfileById($id)
    {
        // TODO: Implement getProfileById() method.
    }

    /**
     * Post message on member wall
     *
     * @return bool
     */
    public function postOnWall()
    {
        // TODO: Implement postOnWall() method.
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
     * Convert API gender to single format
     *
     * @param null|string $gender
     * @return Gender
     */
    public function parseGender($gender = null)
    {
        if ($gender === 'male') {
            $gender = new Gender(Gender::MALE);
        } elseif ($gender === 'female') {
            $gender = new Gender(Gender::FEMALE);
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
        return new \DateTimeImmutable(date("c", $birthday + 3600 * 60));
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
