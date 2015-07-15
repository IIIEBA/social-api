<?php

namespace SocialAPI\Lib\Component;

use GuzzleHttp\Client;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use SocialAPI\Lib\Exception\BaseApiException;
use SocialAPI\Lib\Exception\InvalidArgument\NotStringException;
use SocialAPI\Lib\Model\ApiResponse\ProfileInterface;
use SocialAPI\Lib\Model\Enum\RequestMethod;
use SocialAPI\Lib\Model\Enum\ResponseType;
use SocialAPI\Lib\Util\Logger\LoggerTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BaseApi
 *
 * @package SocialAPI\Lib\Component
 */
abstract class BaseApi implements ApiInterface, LoggerAwareInterface
{
    use LoggerTrait;

    /**
     * @var ApiConfigInterface
     */
    protected $config;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * Init facebook api class
     * @param ApiConfigInterface $config
     * @param Request $request
     * @param LoggerInterface $logger
     */
    public function __construct(ApiConfigInterface $config, Request $request, LoggerInterface $logger = null)
    {
        $this->config  = $config;
        $this->request = $request;

        if ($logger !== null) {
            $this->setLogger($logger);
        }

        $this->initApi();
    }

    /**
     * Get config
     * @return ApiConfigInterface
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get request
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get access token
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set access token
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        if (!is_string($accessToken)) {
            $msg = 'Only string allowed for accessToken';
            $this->getLogger()->error(
                $msg,
                [
                    'object' => $this
                ]
            );
            throw new \InvalidArgumentException($msg);
        }

        $this->accessToken = $accessToken;
    }

    /**
     * Get http client
     * @return Client
     */
    public function getHttpClient()
    {
        if ($this->httpClient === null) {
            $this->httpClient = new Client([
                'allow_redirects'   => true,
                'exceptions'        => false,
            ]);
        }

        return $this->httpClient;
    }

    /**
     * Parse request from API and generate access token
     * @return string
     *
     * @throws BaseApiException
     */
    public function parseLoginResponse()
    {
        $accessToken = null;
        if ($this->getRequest()->get('code') !== null) {
            // TODO: tmp disabled
//            if (
//                $this->getRequest()->get('state') === null
//                || !isset($_SESSION['state'])
//                || $this->getRequest()->get('state') != $_SESSION['state']
//            ) {
//                $msg = 'State doesnt match in request and response';
//                $this->getLogger()->error(
//                    $msg,
//                    [
//                        'object' => $this,
//                    ]
//                );
//
//                throw new BaseApiException($msg);
//            }

            $accessToken = $this->generateAccessTokenFromCode($this->getRequest()->get('code'));
        } elseif ($this->getRequest()->get('error') !== null) {
            $msg = 'Failed to parse response from API with error:' . $this->getRequest()->get('error_description');
            $this->getLogger()->error(
                $msg,
                [
                    'object'        => $this,
                    'error'         => $this->getRequest()->get('error'),
                    'error_desc'    => $this->getRequest()->get('error_description'),
                ]
            );

            throw new BaseApiException($msg);
        }

        if ($accessToken === null) {
            $msg = 'Strange response was given from API';
            $this->getLogger()->error(
                $msg,
                [
                    'object' => $this,
                ]
            );

            throw new BaseApiException($msg);
        }

        return $accessToken;
    }

    /**
     * Generate random string
     * @param $bytes
     * @return string
     *
     * @throws BaseApiException
     */
    public function generateRandom($bytes)
    {
        if (!is_numeric($bytes)) {
            throw new BaseApiException(
                'random() expects an integer'
            );
        }
        if ($bytes < 1) {
            throw new BaseApiException(
                'random() expects an integer greater than zero'
            );
        }
        $buf = '';
        // http://sockpuppet.org/blog/2014/02/25/safely-generate-random-numbers/
        if (!ini_get('open_basedir')
            && is_readable('/dev/urandom')) {
            $fp = fopen('/dev/urandom', 'rb');
            if ($fp !== false) {
                $buf = fread($fp, $bytes);
                fclose($fp);
                if ($buf !== false) {
                    return bin2hex($buf);
                }
            }
        }

        if (function_exists('mcrypt_create_iv')) {
            $buf = mcrypt_create_iv($bytes, MCRYPT_DEV_URANDOM);
            if ($buf !== false) {
                return bin2hex($buf);
            }
        }

        while (strlen($buf) < $bytes) {
            $buf .= md5(uniqid(mt_rand(), true), true);
            // We are appending raw binary
        }
        return bin2hex(substr($buf, 0, $bytes));
    }

    /**
     * Send request to API
     *
     * @param string $url
     * @param RequestMethod|string $method Only [post|get] allowed
     * @param ResponseType|string $responseType Only [json] allowed
     * @param array $params
     *
     * @return mixed
     * @throws BaseApiException
     */
    public function callApiMethod($url, RequestMethod $method, ResponseType $responseType, array $params = [])
    {
        if ($this->getAccessToken() === null) {
            $msg = 'You need to set access token before use API methods';
            $this->getLogger()->error(
                $msg,
                [
                    'object' => $this,
                ]
            );

            throw new BaseApiException($msg);
        }

        if (!is_string($url)) {
            $msg = 'Only string allowed for url name';
            $this->getLogger()->error(
                $msg,
                [
                    'object' => $this,
                ]
            );

            throw new NotStringException('url');
        }

        // Prepare options
        $headers = [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ];
        $params = array_merge(['access_token' => $this->getAccessToken()], $params);

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
                    $msg = 'Strange method type was given';
                    $this->getLogger()->error(
                        $msg,
                        [
                            'object' => $this,
                            'method' => $method,
                        ]
                    );

                    throw new BaseApiException($msg);
            }
        } catch (\Exception $e) {
            $msg = 'Fail to send http request to API';
            $this->getLogger()->error(
                $msg,
                [
                    'object'    => $this,
                    'exception' => $e,
                ]
            );

            throw new BaseApiException($msg);
        }

        // Trying to get response data
        if (empty($response->getBody())) {
            $msg = 'Request to API return empty result';
            $this->getLogger()->error(
                $msg,
                [
                    'object'     => $this,
                    'statusCode' => $response->getStatusCode(),
                ]
            );

            throw new BaseApiException($msg);
        }

        // Decode response
        switch ($responseType->getValue()) {
            case ResponseType::JSON:
                $result = json_decode($response->getBody());
                break;

            default:
                $msg = 'Strange response type was given';
                $this->getLogger()->error(
                    $msg,
                    [
                        'object'        => $this,
                        'responseType'  => $responseType,
                    ]
                );

                throw new BaseApiException($msg);
        }

        // Checking response for errors
        if (isset($result->error)) {
            $msg = 'Request to API was unsuccessful with error: ' . $result->error->error_msg;
            $this->getLogger()->error(
                $msg,
                [
                    'object'        => $this,
                    'method'        => $method,
                    'responseType'  => $responseType,
                    'statusCode'    => $response->getStatusCode(),
                    'result'        => $result,
                ]
            );

            throw new BaseApiException($msg);
        }

        return $result;
    }

    /**
     * Get my profile data
     * @return ProfileInterface
     */
    public function getMyProfile()
    {
        return $this->getProfile(null);
    }
}
