<?php

/*
* Date: 26-05-2021
* Author(s): Iyad Al-Kassab @ SKITSC
* Description: get the total recordings from plivo.
*/

require_once(dirname(__FILE__) . "/../middlewares/auth.php");

require_once(dirname(__FILE__) . '/../../vendor/autoload.php');

require_once(dirname(__FILE__) . "/../../config.php");
require_once(dirname(__FILE__) . "/../db.php");

// set max execution time to unlimited
ini_set('max_execution_time', 0); // 0 = Unlimited

// environment

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__FILE__) . "/../..");
$dotenv->load();

// plivo api

use Plivo\RestClient;
use Plivo\Exceptions\PlivoRestException;

$AuthID = $_ENV['PLIVO_AUTH_ID'];
$AuthToken = $_ENV['PLIVO_AUTH_TOKEN'];

$client = new RestClient($AuthID, $AuthToken);

$process_err = $fatal_keys_err = "";

// the limit imposed by Plivo API
$limit_fetch = 20;

$total_count = 0;

try {
    $response = $client->recordings->list(
        [
                'limit' => $limit_fetch,
                'offset' => 0
        ]
    );

    //the response is an php Object... total_count is Protected and we can't access it
    //serializing the object does not work since we can't have the protected values
    //instead we dump the object as a string and get cut everything before '[total_count] => '
    //we regex all numbers left and take the first one

    //the true parameters output the text value in the variable (instead of printing it on screen)
    $response_txt = print_r($response, true);

    //search for '[total_count] => ' and get the position of '[total_count] => ' number...
    $position_total_count = strpos($response_txt, '[total_count] => ');

    //cut everything before the number
    $response_txt = substr($response_txt, $position_total_count);

    //get the first number after '[total_count] => '...
    $match = '';
    $total_count = preg_match_all('!\d+!', $response_txt, $matches);
    $total_count = $matches[0][0];

} catch (PlivoRestException $ex) {
    $process_err = $ex;
    $process_err .= "Call to Plivo API failed! - Check connection!";
} catch (Throwable $th) {
    $process_err = $th;
    $fatal_keys_err = "Error with your Plivo API keys! Please verify that you have correctly imported the environment files!";
}

header('Content-type: application/json');

if (!empty($fatal_keys_err)) {
    echo $fatal_keys_err;
} else {
    echo $total_count;
}

?>