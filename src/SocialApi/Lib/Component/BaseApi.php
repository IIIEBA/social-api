<?php

namespace SocialApi\Lib\Component;

use BaseExceptions\Exception\InvalidArgument\NotStringException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;
use SocialApi\Lib\ApiInterface;
use SocialApi\Lib\Exception\SocialApiException;
use SocialApi\Lib\Model\AccessTokenInterface;
use SocialApi\Lib\Model\ApiConfigInterface;
use SocialApi\Lib\Model\Enum\RequestMethod;
use SocialApi\Lib\Model\Enum\ResponseType;
use SocialApi\Lib\Model\ProfileInterface;
use SocialApi\Lib\Util\Logger\LoggerTrait;
use Symfony\Component\HttpFoundation\Request;

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
    protected $httpClient;

    /**
     * @var AccessTokenInterface|null
     */
    protected $token;

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
     * @return ApiConfigInterface
     */
    public function getApiConfig()
    {
        return $this->apiConfig;
    }

    /**
     * @return Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @param AccessTokenInterface $token
     */
    public function setAccessToken(AccessTokenInterface $token)
    {
        $this->token = $token;
    }

    /**
     * @return null|AccessTokenInterface
     */
    public function getAccessToken()
    {
        return $this->token;
    }

    /**
     * Parse request from API and generate access token
     *
     * @param Request $request
     * @return AccessTokenInterface
     * @throws SocialApiException
     */
    public function parseLoginResponse(Request $request)
    {
        if (is_null($request->get('code'))) {
            throw new SocialApiException("No code was found in response");
        } elseif (!is_null($request->get('error'))) {
            throw new SocialApiException(
                "Failed to parse response from API with error: " . $this->getRequest()->get('error_description')
            );
        }

        return $this->generateAccessTokenFromCode($request->get('code'));
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
     * @param bool $isTokenRequired
     * @return object
     * @throws SocialApiException
     */
    public function callApiMethod(
        $url,
        RequestMethod $method,
        ResponseType $type,
        array $params = [],
        $isTokenRequired = true
    ) {
        if ($isTokenRequired && is_null($this->getAccessToken())) {
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

        // Add token to params if required
        if ($isTokenRequired) {
            $params = array_merge(['access_token' => $this->getAccessToken()->getToken()], $params);
        }

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
            $errMsg = is_object($result->error) ? $result->error->error_msg : $result->error;
            throw new SocialApiException("Request to API was unsuccessful with error: " . $errMsg);
        }

        return $result;
    }
}
