# VolumNet implementation of Bitrix24 webhooks and quick leads

## Installation

```
composer require volumnet/bitrix24
```

## Usage — for QuickLeads

```
/**
 * @param string $domain Domain name, including protocol (i.e. https://domain.bitrix24.ru)
 * @param string $login Login name
 * @param string $password Password
 */
$ql = new QuickLead('https://domain.bitrix24.ru', 'login', 'password');

/**
 * Send data to quick lead creation
 * @param array $data Data in the following format: https://dev.1c-bitrix.ru/community/blogs/chaos/crm-sozdanie-lidov-iz-drugikh-servisov.php
 * @return mixed Response
 * @throws Exception exception with error code and message from remote url
 */
$ql->send(array(
    'TITLE' => 'Test lead',
    'COMPANY_TITLE' => 'Test company',
    'NAME' => 'User',
    'LAST_NAME' => 'Test',
    'SECOND_NAME' => date('Hi'),
    'ADDRESS' => 'Test address',
    'PHONE_WORK' => '+7 999 000-00-00',
    'EMAIL_WORK' => 'test@test.org',
));
```

More docs here: https://dev.1c-bitrix.ru/community/blogs/chaos/crm-sozdanie-lidov-iz-drugikh-servisov.php

## Usage — for Webhooks

```
/**
 * @param string $domain Domain name, including protocol (i.e. https://domain.bitrix24.ru)
 * @param string $webhook Webhook ID
 */
$wh = new Webhook('http://domain.bitrix24.ru', '0123456789abcdef'); 

/**
 * Calls certain method
 * @param string $methodName Method name, without transport extension (i.e. .xml or .json)
 * @param array $data Method data
 * @return mixed Parsed data from method
 * @throws Exception Exception with error response from the method
 */ 
$wh->method('crm.lead.add', array(
    'fields' => array(
        'TITLE' => 'Test lead',
        'COMPANY_TITLE' => 'Test company',
        'NAME' => 'User',
        'LAST_NAME' => 'Test',
        'SECOND_NAME' => date('Hi'),
        'ADDRESS' => 'Test address',
        'SOURCE_ID' => 'WEB',
        'PHONE' => array(
            array(
                'VALUE' => '+7 999 000-00-00',
                'VALUE_TYPE' => 'WORK'
            )
        ),
        'EMAIL' => array(
            array(
                'VALUE' => 'test@test.org',
                'VALUE_TYPE' => 'WORK'
            )
        )
    )
)); 
```

More docs here: https://dev.1c-bitrix.ru/rest_help/index.php