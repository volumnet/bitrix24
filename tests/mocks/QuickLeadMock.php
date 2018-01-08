<?php
namespace VolumNet\Bitrix24;

class QuickLeadMock extends QuickLead
{
    /**
     * Class constructor
     * @param string $domain Domain name, including protocol (i.e. https://domain.bitrix24.ru)
     * @param string $login Login name
     * @param string $password Password
     */
    public function __construct($domain, $login, $password)
    {
        parent::__construct($domain, $login, $password);
        $this->url = $domain;
    }
}
