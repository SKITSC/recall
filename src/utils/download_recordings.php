<?php

/*
* Date: 26-05-2021
* Author(s): Iyad Al-Kassab @ SKITSC
* Description: download recordings utility
*/

require_once(dirname(__FILE__) . "/../middlewares/auth.php");

require_once(dirname(__FILE__) . '/../../vendor/autoload.php');

require_once(dirname(__FILE__) . "/../../config.php");
require_once(dirname(__FILE__) . "/../db.php");

// set max execution time to unlimited
ini_set('max_execution_time', 0); // 0 = Unlimited

$process_err = "";

ob_start();

// have a separate lock file because it blocks the first one
$fp = fopen('update.lock', 'r+');
if (!flock($fp, LOCK_EX | LOCK_NB, $blocking)) {
    if ($blocking) {
        echo "Downloading...";
        exit;
    }
}

echo "OK...";
header('Content-Length: ' . ob_get_length());
header('Connection: close');
ob_end_flush();
ob_flush();
flush();
if (is_callable('fastcgi_finish_request')) {
    fastcgi_finish_request();
}

// continue processing here

$recordings_storage_directory = dirname(__FILE__) . "/../../recordings/";
if (!file_exists($recordings_storage_directory)) {
    mkdir($recordings_storage_directory, 0644, true);
}

$sql = 'SELECT * FROM backer_recordings WHERE downloaded=0';
if (isset($_GET['force_download']) && !empty($_GET['force_download'])) {

    if ($_GET['force_download'] === "1") {
        $sql = 'SELECT * FROM backer_recordings';
    } 
}

$stmt = $pdo->prepare($sql);

$downloaded_array = array();

if ($stmt->execute()) {
    
	$data = $stmt->fetchAll();
    
    $total_rows = count($data);
    for ($i = 0; $i < $total_rows; $i++) {
		
		$db_id = $data[$i][0];
		$call_uuid = $data[$i][1];
		$date = $data[$i][2];
		$recording_url = $data[$i][3];
		
        // parse data
		$year = substr($date, 0, 4);
		$month = substr($date, 5, 2);
		$day = substr($date, 8, 2);
		$time = substr($date, 11, 10);
		$time = str_replace(":", "-", $time);
		
		$folder = $recordings_storage_directory . $year . "/" . $month . "/" . $day . "/";
        
		if (!file_exists($folder)) {
			mkdir($folder, 0644, true);
		}
		
		$filename = $time . "-id-" . $db_id . ".mp3";
		if (!file_exists($folder . $filename)) {
			
            $content = file_get_contents($recording_url);
            file_put_contents($folder . $filename, $content);

            $downloaded_array[] = $filename;

            // update db
            $sql = 'UPDATE backer_recordings SET downloaded=1 WHERE id=?';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(1, $db_id);

            if ($stmt->execute()) {} else {
                $process_err = "Error updating DB!";
                die();
            }
		}
	}
} else {
	$process_err = "Error with STMT!";
	die();
}

flock($fp, LOCK_UN);

//no need to send a response
//header('Content-type: application/json');
//echo json_encode($downloaded_array);

?>