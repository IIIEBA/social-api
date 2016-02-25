<?php

namespace SocialApi\Lib\Component;

use BaseExceptions\Exception\InvalidArgument\NotStringException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;
use SocialApi\Lib\ApiInterface;
use SocialApi\Lib\Exception\SocialApiException;
use SocialApi\Lib\Model\ApiConfigInterface;
use SocialAPI\Lib\Model\Enum\RequestMethod;
use SocialAPI\Lib\Model\Enum\ResponseType;
use SocialApi\Lib\Model\ProfileInterface;
use SocialAPI\Lib\Util\Logger\LoggerTrait;

/**
 * Class BaseApi
 * @package SocialApi\Lib\Component
 */
abstract class BaseApi implements ApiInterface
{
    use LoggerTrait;

    /**
     * @var ApiConfigInterface
     */
    private $apiConfig;

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var string|null
     */
    private $token;

    /**
     * BaseApi constructor.
     * @param ApiConfigInterface $apiConfig
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        ApiConfigInterface $apiConfig,
        LoggerInterface $logger = null
    ) {
        $this->setLogger($logger);

        $this->apiConfig = $apiConfig;

        $this->httpClient = new Client([
            'allow_redirects' => true,
            'exceptions' => false,
        ]);
    }

    /**
     * @return Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        if (!is_string($token)) {
            throw new NotStringException("token");
        }

        $this->token = $token;
    }

    /**
     * @return null|string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Get current user profile data
     *
     * @return ProfileInterface
     */
    public function getCurrentProfile()
    {
        return $this->getProfileById(null);
    }

    /**
     * Request API server with params
     *
     * @param string $url
     * @param RequestMethod $method
     * @param ResponseType $type
     * @param array $params
     * @return mixed
     * @throws SocialApiException
     */
    public function callApiMethod(
        $url,
        RequestMethod $method,
        ResponseType $type,
        array $params = []
    ) {
        if (is_null($this->getToken())) {
            throw new SocialApiException("You need to set access token before use API methods");
        }

        if (!is_string($url)) {
            throw new NotStringException("url");
        }

        // Prepare options
        $headers = [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];
        $params = array_merge(['access_token' => $this->getToken()], $params);

        // Trying to send request
        try {
            switch ($method->getValue()) {
                case RequestMethod::POST:
                    $options  = array_merge($headers, ['form_params' => $params]);
                    $response = $this->getHttpClient()->post($url, $options);
                    break;

                case RequestMethod::GET:
                    $response = $this->getHttpClient()->get($url . '?' . http_build_query($params), $headers);
                    break;

                default:
                    throw new SocialApiException("Not supported request method was selected");
            }
        } catch (\Exception $e) {
            throw new SocialApiException("Fail to send http request to API server");
        }

        // Trying to get response data
        if (empty($response->getBody())) {
            throw new SocialApiException("Empty response was given from server");
        }

        // Decode response
        switch ($type->getValue()) {
            case ResponseType::JSON:
                $result = json_decode($response->getBody());
                break;

            default:
                throw new SocialApiException("Not supported response type was selected");
        }

        // Checking response for errors
        if (isset($result->error)) {
            throw new SocialApiException("Request to API was unsuccessful with error: " . $result->error->error_msg);
        }

        return $result;
    }
}
