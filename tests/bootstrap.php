<?php
namespace RAAS\CMS;

use RAAS\Application;

$_SERVER['HTTP_HOST'] = 'future-vision';
$_SERVER['HTTPS'] = 'on';
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/resources/Controller_Cron.php';
$GLOBALS['bitrix24'] = include __DIR__ . '/../bitrix24.config.php';
Application::i()->run('cron');
