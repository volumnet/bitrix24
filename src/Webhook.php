<?php
/**
 * Bitrix24 webhook
 * @author Alex V. Surnin <info@volumnet.ru>
 * @copyright Volume Networks, 2018
 */
namespace VolumNet\Bitrix24;

use \Exception;
use VolumNet\CURL\CURL;

/**
 * Bitrix24 webhook class
 */
class Webhook
{
    /**
     * URL to connect
     * @var string
     */
    protected $url;

    /**
     * Class constructor
     * @param string $domain Domain name, including protocol (i.e. https://domain.bitrix24.ru)
     * @param string $webhook Webhook path (i.e. /rest/1/someWebhookId/)
     */
    public function __construct($domain, $webhook)
    {
        $this->url = $domain . $webhook;
        $this->test = $test;
    }


    /**
     * Calls certain method
     * @param string $methodName Method name, without transport extension (i.e. .xml or .json)
     * @param array $data Method data
     * @return mixed Parsed data from method
     * @throws Exception Exception with error response from the method
     */
    public function method($methodName, array $data = array())
    {
        $curl = new CURL();
        $url = $this->url . $methodName . '.json';
        $result = $curl->getURL($url, $data);
        $json = json_decode($result);
        if (!$result) {
            throw new Exception('No response retrieved');
        } elseif (!$json) {
            throw new Exception('Cannot parse JSON');
        } elseif ($json->error) {
            throw new Exception($result);
        }
        return $json;
    }
}
