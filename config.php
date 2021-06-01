<?php

/*
* Date: 21-05-2021
* Author(s): Iyad Al-Kassab @ SKITSC
* Description: config for app
*/

require 'vendor/autoload.php';

define('__ROOT__', dirname(dirname(__FILE__)));
define('DIR_VENDOR', __ROOT__ . '/vendor/');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$env = $_ENV['ENV'];
if (strcmp($env,'DEV') == 0) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    //silence errors if not provided correct keys
    error_reporting(0);
}
?>