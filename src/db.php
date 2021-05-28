<?php

/*
* Date: 21-05-2021
* Author(s): Iyad Al-Kassab @ SKITSC
* Description: basic link to MySQL server
*/

require_once(dirname(__FILE__) . '/../vendor/autoload.php'); 

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();

$db_host = $_ENV['DB_HOST'];
$db_port = $_ENV['DB_PORT'];
$db_name = $_ENV['DB_NAME'];
$db_username = $_ENV['DB_USERNAME'];
$db_password = $_ENV['DB_PASSWORD'];

define('DB_HOST', $db_host);
define('DB_PORT', $db_port);
define('DB_NAME', $db_name);
define('DB_USERNAME', $db_username);
define('DB_PASSWORD', $db_password);
 
try{

    $dsn = "mysql:host=" . DB_HOST . ":" . DB_PORT. ";dbname=" . DB_NAME;

    $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e){

    die("ERROR: Could not connect. " . $e->getMessage());
}

?>