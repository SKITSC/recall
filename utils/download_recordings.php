<?php

/*
* Date: 26-05-2021
* Author(s): Iyad Al-Kassab @ SKITSC
* Description: download recordings utility
* Copyright (C) 2021 Iyad Al-Kassab

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>.
*/

require_once(dirname(__FILE__) . '/../vendor/autoload.php'); 

require_once(dirname(__FILE__) . "/../config.php");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();

$fp = fopen('proc.lock', 'r+');
if (!flock($fp, LOCK_EX | LOCK_NB, $blocking)) {
    if ($blocking) {
        echo "Downloading...";
        exit;
    }
}

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

try {

    $dsn = "mysql:host=" . DB_HOST . ":" . DB_PORT. ";dbname=" . DB_NAME;

    $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e){

    die("ERROR: Could not connect. " . $e->getMessage());
}

// set max execution time to unlimited
ini_set('max_execution_time', 0); // 0 = Unlimited

$process_err = "";

// continue processing here

$recordings_storage_directory = dirname(__FILE__) . "/../recordings/";
if (!file_exists($recordings_storage_directory)) {
    mkdir($recordings_storage_directory, 0755, true);
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
			mkdir($folder, 0755, true);
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

// unset db
unset($stmt);
unset($pdo);

flock($fp, LOCK_UN);

header('Content-type: application/json');
echo json_encode($downloaded_array);

?>