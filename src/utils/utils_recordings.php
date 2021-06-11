<?php

/*
* Date: 10-06-2021
* Author(s): Iyad Al-Kassab @ SKITSC
* Description: utils to get data on the database
*/

require_once(dirname(__FILE__) . "/../middlewares/auth.php");

require_once(dirname(__FILE__) . '/../../vendor/autoload.php');

require_once(dirname(__FILE__) . "/../../config.php");
require_once(dirname(__FILE__) . "/../db.php");

// environment

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__FILE__) . "/../..");
$dotenv->load();

$process_err = "";

$sql_query = "";

if (isset($_GET['data_required']) && !empty($_GET['data_required'])) {

    switch ($_GET['data_required']) {
        case "total_calls_number":
            $sql_query = 'SELECT COUNT(call_uuid) FROM backer_recordings';
            break;
        case "most_dialed_number":
            $sql_query = "SELECT to_number, count(to_number) AS max_occurence FROM backer_recordings GROUP BY to_number ORDER BY max_occurence DESC LIMIT 1";
            break;
        case "most_calling_number":
            $sql_query = "SELECT from_number, count(from_number) AS max_occurence FROM backer_recordings GROUP BY from_number ORDER BY max_occurence DESC LIMIT 1";
            break;
        case "shortest_call":
            $sql_query = "SELECT MIN(recording_duration) FROM backer_recordings";
            break;
        case "longuest_call":
            $sql_query = "SELECT MAX(recording_duration) FROM backer_recordings";
            break;
        default:
            break;
    }
}

$util_data = "";

if (!empty($sql_query)) {
    $stmt = $pdo->prepare($sql_query);

    if ($stmt->execute()) {
        $util_data = $stmt->fetchColumn();
    } else {
        $process_err = "error with stmt!";
    }
} else {
    $process_err = "failed to obtain query!";
}

header('Content-type: application/json');

if (empty($util_data)) {
    $util_data = "0"; //or "no data..." ?
}
echo $util_data;

?>