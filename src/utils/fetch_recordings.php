<?php

/*
* Date: 26-05-2021
* Author(s): Iyad Al-Kassab @ SKITSC
* Description: fetch recordings utility, this would be running as a cron job every minute, if you need last phone calls directly accessible in the system
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

$process_err = "";

//number of fetches per update $_GET

// the limit imposed by Plivo API
$limit_fetch = 20;
$recordings_to_fetch_per_update = 5;

$recordings_retrieved = array();

try {
    $k = 0;
    while ($k < $recordings_to_fetch_per_update) {

        // get 20 (limit_fetch) and continue
        $response = $client->recordings->list(
            [
                'limit' => $limit_fetch,
                'offset' => $k
            ]
        );

        // see documentation on get_object_vars
        $response_object = get_object_vars($response);

        for ($i = 0; $i < $limit_fetch; $i++) {

            if (empty($response_object['resources'][$i])) {
                continue;
            }

            $call_uuid = get_object_vars($response_object['resources'][$i])['properties']['callUuid'];
            $addTime = get_object_vars($response_object['resources'][$i])['properties']['addTime'];
            $recording_url = get_object_vars($response_object['resources'][$i])['properties']['recordingUrl'];
			$recording_duration_ms = get_object_vars($response_object['resources'][$i])['properties']['recordingDurationMs'];
            
			// fetch call details
			$from_number = 'UNKNOWN';
			$to_number = 'UNKNOWN';

            /*
            * -- Ticket submitted to plivo support --
            * Notice: Undefined index: total_rate in /var/www/html/vendor/plivo/plivo-php/src/Plivo/Resources/Call/Call.php on line 57
            * Notice: Undefined index: hangup_cause_code in /var/www/html/vendor/plivo/plivo-php/src/Plivo/Resources/Call/Call.php on line 58
            * Notice: Undefined index: hangup_cause_name in /var/www/html/vendor/plivo/plivo-php/src/Plivo/Resources/Call/Call.php on line 59
            * Notice: Undefined index: hangup_source in /var/www/html/vendor/plivo/plivo-php/src/Plivo/Resources/Call/Call.php on line 60
            * Notice: Undefined index: call_uuid in /var/www/html/vendor/plivo/plivo-php/src/Plivo/Resources/Call/Call.php on line 65
            * Notice: Undefined index: call_uuid in /var/www/html/vendor/plivo/plivo-php/src/Plivo/Resources/Call/Call.php on line 68
            * Array ( [properties] => Array ( [answerTime] => [billDuration] => [billedDuration] => [callDirection] => [callDuration] => [callUuid] => [endTime] => [from] => [initiationTime] => [parentCallUuid] => [resourceUri] => [to] => [totalAmount] => [totalRate] => [hangupCauseCode] => [hangupCauseName] => [hangupSource] => ) )
            */

            // it returns sometime an empty array... so check if not null...
			try {
    			$call_details = $client->calls->get($call_uuid);
				$call_details_object = get_object_vars($call_details);

				if (isset($call_details_object['properties']['from'])) {
					$from_number = $call_details_object['properties']['from'];
				}

				if (isset($call_details_object['properties']['to'])) {
					$to_number = $call_details_object['properties']['to'];
				}
			} catch (PlivoRestException $ex) {
 			   $process_err = $ex;
			}

			/*
            * for legacy version...
			* 
            * try {
            * 
			* } catch (PlivoRestException $ex) {
			*   print_r($ex);
			* }
            */

            if (empty($call_uuid) || empty($addTime) || empty($recording_url)) {
                continue; //Don't add to db!
            }

            // verify if call_uuid is in db already... if yes : continue (jmp loop)

            $sql = "SELECT * FROM backer_recordings WHERE call_uuid = ?";
            $stmt = $pdo->prepare($sql);

            if ($stmt->execute([$call_uuid])) {
                $row_count = $stmt->rowCount();
                if ($row_count > 0) {
                    // the call_uuid is already in DB!
                    continue;
                }
            } else {
                $process_err = "Error with STMT!";
                die();
            }
			
			$addTime = substr($addTime, 0, 19);
            $recording_duration_s = intval($recording_duration_ms/1000);

            // insert in db
            try {
                $sql = "INSERT INTO backer_recordings (call_uuid, add_time, recording_url, recording_duration, from_number, to_number) VALUES (?, ?, ?, ?, ?, ?)";
	            $stmt = $pdo->prepare($sql);
	            $stmt->bindParam(1, $call_uuid);
	            $stmt->bindParam(2, $addTime);
	            $stmt->bindParam(3, $recording_url);
				$stmt->bindParam(4, $recording_duration_s);
				$stmt->bindParam(5, $from_number);
				$stmt->bindParam(6, $to_number);
	
	            if ($stmt->execute()) {
                    // add this to json array
                    $record = array();
                    $record[] = $call_uuid;
                    $record[] = $addTime;
                    $record[] = $recording_url;
                    $record[] = $recording_duration_s;
                    $record[] = $from_number;
                    $record[] = $to_number;

                    array_push($recordings_retrieved, $record);

                } else {
	                $process_err = "Error inserting in DB!";
	                die();
	            }
			} catch (PDOException $e) {
				trigger_error('Error occured while trying to insert into the DB:' . $e->getMessage(), E_USER_ERROR);
			}

        }
        
        $k += $limit_fetch;
    }
} catch (PlivoRestException $ex) {
    $process_err = $ex;
    $process_err .= "Call to Plivo API failed! - Check connection!";
}

// unset db
unset($stmt);
unset($pdo);

// send data
$recordings_json = json_encode($recordings_retrieved, JSON_FORCE_OBJECT);

header('Content-type: application/json');
echo $recordings_json;
?>