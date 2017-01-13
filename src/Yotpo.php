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


namespace Urb\Yotpo;

use Urb\Yotpo\Client\HttpClientFactory;
use Urb\Yotpo\Client\YotpoApiClient;
use Urb\Yotpo\Exception\YotpoApiException;

class Yotpo
{
    /**
     * YOTPO API ENDPOINTS
     */
    const YOTPO_API_GET_REVIEWS_ENDPOINT = '/v1/apps/{{APP_KEY}}/reviews';

    protected $appKey;

    protected $appSecret;

    protected $accessToken;

    protected $client;

    public function __construct(array $config = [])
    {
        $config  = array_merge([
            'app_key'                 => null,
            'app_secret'              => null,
            'access_token'            => null,
            'http_client_handler'     => null,
            'persistent_data_handler' => null,
        ], $config);

        $this->configCheck($config);

        $this->appKey = $config['app_key'];
        $this->appSecret = $config['app_secret'];

        $this->client = new YotpoApiClient(
            HttpClientFactory::createHttpClient($config['http_client_handler'])
        );

        $this->setAccessToken($config['access_token']);

    }

    protected function configCheck(array $config = [])
    {
        if(!$config['app_key'])
        {
            throw new YotpoApiException('Required app_key was not supplied in config');
        }

        if(!$config['app_secret'])
        {
            throw new YotpoApiException('Required app_secret was not supplied in config');
        }

    }

    public function setAccessToken($accessToken)
    {
        if(is_string($accessToken))
        {
            $this->accessToken = $accessToken;
        }

        $this->accessToken = $this->client->getAccessToken(
            $this->appKey,
            $this->appSecret
        );

        return $this;

    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function getReviews(array $param = [])
    {
        $param = array_merge([
            'utoken'    => $this->getAccessToken(),
            'count'     => 30,
            'page'      => 1
        ], $param);

        $url = $this->client->prepareUrl(
            YotpoApiClient::YOTPO_BASE_API_URL_SECURED.static::YOTPO_API_GET_REVIEWS_ENDPOINT,
            $param,
            $this->appKey
        );

        return $this->client->sendApiRequest(
            $url,
            YotpoApiClient::GET
        )->getDecodedBody();
    }


}