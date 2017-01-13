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

use Exception;

class HttpClientFactory
{
    private function __construct()
    {

    }

    public static function createHttpClient($handler = 'curl')
    {
       /* if(!$handler)
        {
            return self::detectDefaultClient();
        }*/

        if($handler instanceof YotpoHttpClientInterface)
        {
            return $handler;
        }

        if ('curl' === $handler) {
            if (!extension_loaded('curl')) {
                throw new Exception('The cURL extension must be loaded in order to use the "curl" handler.');
            }

            return new YotpoCurlHttpClient();
        }

    }
}