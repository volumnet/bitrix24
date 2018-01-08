<?php
/**
 * Bitrix24 quick lead creation
 * @author Alex V. Surnin <info@volumnet.ru>
 * @copyright Volume Networks, 2018
 */
namespace VolumNet\Bitrix24;

use \Exception;
use VolumNet\CURL\CURL;

/**
 * Bitrix24 quick lead creation class
 */
class QuickLead
{
    /**
     * Source ID field
     */
    const SOURCE_ID = 'WEB';

    /**
     * Normal response errorcode
     */
    const ERR_OK = 201;

    /**
     * URL to connect
     * @var string
     */
    protected $url;

    /**
     * Login name
     * @var string
     */
    protected $login;

    /**
     * Password
     * @var string
     */
    protected $password;

    /**
     * Class constructor
     * @param string $domain Domain name, including protocol (i.e. https://domain.bitrix24.ru)
     * @param string $login Login name
     * @param string $password Password
     */
    public function __construct($domain, $login, $password)
    {
        $this->url = rtrim($domain, '/') . '/crm/configs/import/lead.php';
        $this->login = $login;
        $this->password = $password;
    }


    /**
     * Send data to quick lead creation
     * @param array $data Data in the following format: https://dev.1c-bitrix.ru/community/blogs/chaos/crm-sozdanie-lidov-iz-drugikh-servisov.php
     * @return mixed Response
     * @throws Exception exception with error code and message from remote url
     */
    public function send(array $data = array())
    {
        if (!$this->login || !$this->password) {
            throw new Exception('No login/password specified');
        }
        if (!$data['TITLE']) {
            throw new Exception('No title provided');
        }
        $curl = new CURL();

        $POST = array_merge(
            array('LOGIN' => $this->login, 'PASSWORD' => $this->password, 'SOURCE_ID' => self::SOURCE_ID),
            $data
        );

        $result = $curl->getURL($this->url, $POST);
        $result = $this->fixJSON($result);
        $result = json_decode($result);
        if (!$result) {
            throw new Exception('No response retrieved');
        } elseif ($result->error && ((int)$result->error != self::ERR_OK)) {
            throw new Exception($result->error_message, (int)$result->error);
        }
        return $result;
    }


    public function fixJSON($json)
    {
        $newJSON = '';

        $jsonLength = strlen($json);
        for ($i = 0; $i < $jsonLength; $i++) {
            if ($json[$i] == "'") {
                $nextQuote = strpos($json, $json[$i], $i + 1);
                $quoteContent = substr($json, $i + 1, $nextQuote - $i - 1);
                $newJSON .= '"' . str_replace('"', '\\"', $quoteContent) . '"';
                $i = $nextQuote;
            } else {
                $newJSON .= $json[$i];
            }
        }

        return $newJSON;
    }
}
