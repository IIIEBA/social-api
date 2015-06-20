<?php

namespace Test\SocialApi\Lib\Component;

use Psr\Log\LoggerInterface;
use SocialAPI\Lib\Component\BaseApi;
use SocialAPI\Lib\Model\ApiResponse\ProfileInterface;

class BaseApiTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertTrue(true);
    }
}

class BaseApiTestClass extends BaseApi
{
    /**
     * Init api method
     */
    public function initApi()
    {
        throw new \Exception('Method not implemented');
    }

    /**
     * Generate login url
     *
     * @return string
     * @throws \Exception
     */
    public function generateLoginUrl()
    {
        throw new \Exception('Method not implemented');
    }

    /**
     * Generate logout url
     *
     * @return string
     * @throws \Exception
     */
    public function generateLogoutUrl()
    {
        throw new \Exception('Method not implemented');
    }

    /**
     * Generate access token from code
     *
     * @param string $code
     *
     * @return string
     * @throws \Exception
     */
    public function generateAccessTokenFromCode($code)
    {
        throw new \Exception('Method not implemented');
    }

    /**
     * Post message on member wall
     *
     * @return bool
     * @throws \Exception
     */
    public function postOnMyWall()
    {
        throw new \Exception('Method not implemented');
    }

    /**
     * Get list of member friends with basic data
     *
     * @return \SocialAPI\Lib\Model\ApiResponse\ProfileInterface[]
     * @throws \Exception
     */
    public function getFriends()
    {
        throw new \Exception('Method not implemented');
    }

    /**
     * Get selected profile data
     *
     * @param string|null $memberId
     *
     * @return ProfileInterface
     * @throws \Exception
     */
    public function getProfile($memberId)
    {
        throw new \Exception('Method not implemented');
    }

    /**
     * Convert API gender to single format
     *
     * @param null|int $gender
     *
     * @return null|string
     * @throws \Exception
     */
    public function parseGender($gender = null)
    {
        throw new \Exception('Method not implemented');
    }

    /**
     * Convert API birthday to single format
     *
     * @param null $birthday
     *
     * @return \DateTimeImmutable|null
     * @throws \Exception
     */
    public function parseBirthday($birthday = null)
    {
        throw new \Exception('Method not implemented');
    }

    /**
     * Convert API avatar url to general format
     *
     * @param null|string $url
     *
     * @return null
     * @throws \Exception
     */
    public function parseAvatarUrl($url = null)
    {
        throw new \Exception('Method not implemented');
    }

    /**
     * @param LoggerInterface $logger
     *
     * @throws \Exception
     */
    public function setLogger(LoggerInterface $logger)
    {
        throw new \Exception('Method not implemented');
    }

    /**
     * @throws \Exception
     */
    public function getLogger()
    {
        throw new \Exception('Method not implemented');
    }

}