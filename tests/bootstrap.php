<?php
require __DIR__ . '/../vendor/autoload.php';
$GLOBALS['bitrix24'] = array(
    'domain' => '',
    'login' => '',
    'password' => '',
    'webhook' => '',
);
$f = __DIR__ . '/../../../../bitrix24.config.php';
if (is_file($f)) {
    $GLOBALS['bitrix24'] = require $f;
}
