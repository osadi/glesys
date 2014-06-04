<?php
/**
 * @see https://github.com/GleSYS/API/wiki/Full-API-Documentation
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License v3
 */

namespace Glesys;

use Etechnika\IdnaConvert\IdnaConvert as IdnaConvert;
use Exception;

class GlesysApi
{
    const API_URL = 'https://api.glesys.com/';

    private $apiUser  = '';
    private $apiKey   = '';
    private $verifySSL = false;

    public function __construct($apiUser, $apiKey, $verifySSL = false)
    {
        if ($apiUser) {
            $this->apiUser = $apiUser;
        }

        if ($apiKey) {
            $this->apiKey = $apiKey;
        }

        $this->verifySSL = $verifySSL;
    }


    /**
     * Lazy
     *
     * @param $fn
     * @param $args
     * @return mixed
     * @throws \Exception
     */
    public function __call($fn, $args)
    {

        $args = isset($args[0]) ? $args[0] : null;

        if (null !== $args && !is_array($args)) {
            throw new Exception(sprintf("Error: %s() expects first parameter to be an array", __METHOD__));
        }

        list($method, $module, $function) = preg_split('/(?=[A-Z])/', $fn, -1, PREG_SPLIT_NO_EMPTY);

        return call_user_func_array(
            array(
                $this,
                'makeCall',
            ),
            array(
                sprintf("%s/%s", lcfirst($module), lcfirst($function)),
                $args,
                strtoupper($method),
            )
        );
    }

    protected function makeCall($function, $params = null, $method = 'POST') {

        if (!isset($this->apiUser) || !isset($this->apiKey)) {
            throw new Exception("Error: _makeCall() | $function - This method requires a user and a key.");
        }

        if (isset($params) && is_array($params)) {
            $paramString = '&' . http_build_query($params);
        } else {
            $paramString = null;
        }

        $apiCall = self::API_URL . $function . (('GET' === $method) ? $paramString : null);

        $ch = curl_init();
        curl_setopt_array($ch,
            array(
                CURLOPT_URL            => $apiCall,
                CURLOPT_HTTPHEADER     => array('Accept: application/json'),
                CURLOPT_CONNECTTIMEOUT => 20,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => $this->verifySSL,
                CURLOPT_SSL_VERIFYHOST => $this->verifySSL,
                CURLOPT_USERPWD        => sprintf("%s:%s", $this->apiUser, $this->apiKey),
            )
        );

        if ('POST' === $method) {
            curl_setopt($ch, CURLOPT_POST, count($params));
            curl_setopt($ch, CURLOPT_POSTFIELDS, ltrim($paramString, '&'));
        }

        $jsonData = curl_exec($ch);
        if (false === $jsonData) {
            throw new Exception("Error: _makeCall() - cURL error: " . curl_error($ch));
        }
        curl_close($ch);

        return json_decode($jsonData);
    }

    /**
     * punyencode domain names
     */
    public function punycodeEncode($string)
    {
        return IdnaConvert::encodeString($string);
    }

    /**
     * punydecode domain names
     */
    public function punycodeDecode($string)
    {
        return IdnaConvert::decodeString($string);
    }
}
