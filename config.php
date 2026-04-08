<?php

date_default_timezone_set('Europe/Moscow');
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('default_charset', 'UTF-8');
ini_set('memory_limit', '-1');
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

require_once __DIR__ . '/vendor/autoload.php';

define('ROOT', __DIR__);