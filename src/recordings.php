<?php

/*
* Date: 26-05-2021
* Author(s): Iyad Al-Kassab @ SKITSC
* Description: login page for service
*/

require_once("./middlewares/auth.php");

require '../vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader);

require_once "../config.php";
require "db.php";

// environment

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();

// plivo api

use Plivo\RestClient;
use Plivo\Exceptions\PlivoRestException;

$AuthID = $_ENV['PLIVO_AUTH_ID'];
$AuthToken = $_ENV['PLIVO_AUTH_TOKEN'];

$client = new RestClient($AuthID, $AuthToken);

$total_recordings = 0;
$plivo_err = "";

try {

    $response = $client->recordings->list([
        'limit' => 20,
        'offset' => 0
    ]);

    /*
    * the response is an php Object... total_count is Protected and we can't access it
    * serializing the object does not work since we can't have the protected values
    * instead we dump the object as a string and cut everything before '[total_count] => '
    * we regex all numbers left and take the first one
    */

    // the true parameters output the text value in the variable (instead of printing it on screen)
    $response_txt = print_r($response, true);

    // search for '[total_count] => ' and get the position of '[total_count] => ' number...
    $position_total_count = strpos($response_txt, '[total_count] => ');

    // cut everything before the number
    $response_txt = substr($response_txt, $position_total_count);

    // get the first number after '[total_count] => '...
    $total_count = preg_match_all('!\d+!', $response_txt, $matches);
    $total_recordings = $matches[0][0];

} catch (PlivoRestException $ex) {

    print_r(ex);
    $plivo_err = "Call to Plivo API failed! - Check connection!";
}

// unset db
unset($stmt);
unset($pdo);

// globals
$twig->addGlobal("username", $_SESSION['username']);

echo $twig->render('recordings.twig');

?>