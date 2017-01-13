<?php
/**
 * Urb_Yotpo
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Urb
 * @package        Urb_Yotpo
 * @copyright      Copyright (c) 2017
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 * @author         george <george@underratedbrilliance.com>
 */

namespace Urb\Yotpo\Client;


use Urb\Yotpo\Exception\YotpoApiException;
use Urb\Yotpo\Helper\Url;

class YotpoApiClient
{
    /**
     * HTTP request methods
     */
    const GET     = 'GET';
    const POST    = 'POST';
    const PUT     = 'PUT';
    const HEAD    = 'HEAD';
    const DELETE  = 'DELETE';
    const TRACE   = 'TRACE';
    const OPTIONS = 'OPTIONS';
    const CONNECT = 'CONNECT';
    const MERGE   = 'MERGE';

    /**
     * YOTPO API DEFAULTS
     */
    const YOTPO_OAUTH_TOKEN_URL        = 'https://api.yotpo.com/oauth/token';
    const YOTPO_BASE_API_URL_UNSECURED = 'http://api.yotpo.com';
    const YOTPO_BASE_API_URL_SECURED   = 'https://api.yotpo.com';
    const YOTPO_API_BATCH_URI          = 'https://staticw2.yotpo.com/batch';
    const DEFAULT_TIMEOUT              = 60;
    const YOTPO_API_PLATFORM_NAME      = 'Api';
    const YOTPO_API_PLATFORM_ID        = 2; // Any other platform

    /**
     * @var YotpoHttpClientInterface
     */
    protected $httpClientHandler;

    /**
     * YotpoApiClient constructor.
     *
     * @param YotpoHttpClientInterface $httpClient
     */
    public function __construct(YotpoHttpClientInterface $httpClient)
    {
        $this->httpClientHandler = $httpClient;
    }

    /**
     * @param YotpoHttpClientInterface $httpClient
     */
    public function setHttpClientHandler(YotpoHttpClientInterface $httpClient)
    {
        $this->httpClientHandler = $httpClient;
    }

    /**
     * @return YotpoHttpClientInterface
     */
    public function getHttpClientHandler()
    {
        return $this->httpClientHandler;
    }

    /**
     * @param bool $secure
     * @return string
     */
    public function getBaseApiUrl($secure = true)
    {
        return $secure? static::YOTPO_BASE_API_URL_SECURED : static::YOTPO_BASE_API_URL_UNSECURED;
    }

    /**
     * @param bool $secure
     * @return string
     */
    public function getBatchApiUrl($secure = true)
    {
        return static::YOTPO_API_BATCH_URI;
    }

    /**
     * @param $url
     * @param $method
     * @param $body
     * @param array $headers
     * @param int $timeOut
     * @return \Urb\Yotpo\Http\Response
     */
    public function sendApiRequest($url, $method, $body = '', $headers = [], $timeOut = self::DEFAULT_TIMEOUT)
    {
        $headers = array_merge([
            'Content-Type' => 'application/json',
        ],$headers);

        return $this->httpClientHandler->send($url,$method,$this->prepareBody($body),$headers,$timeOut);
    }

    /**
     * @param $clientId
     * @param $client_secret
     * @return string
     * @throws YotpoApiException
     */
    public function getAccessToken($clientId, $client_secret)
    {
        $body = [
            'client_id' => $clientId,
            'client_secret' => $client_secret,
            'grant_type' => 'client_credentials',
        ];

        $response = $this->sendApiRequest(
            static::YOTPO_OAUTH_TOKEN_URL,
            static::POST,
            $body
        );

        if($response->getHttpResponseCode() !== 200)
        {
            throw new YotpoApiException(
                $response->getDecodedBody()['status']['message']
            );
        }

        return $response->getDecodedBody()['access_token'];
    }

    /**
     * @param $body
     * @return string
     */
    public function prepareBody($body)
    {
        if(is_string($body))
        {
            return $body;
        }

        return json_encode($body);
    }

    public function prepareUrl($url,array $params,$appKey)
    {
        if(!strpos($url,'{{APP_KEY}}') === false)
        {
            $url = str_replace('{{APP_KEY}}',$appKey,$url);
        }

        return Url::appendParamsToUrl($url,$params);
    }

}