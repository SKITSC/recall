<?php

/*
* Date: 10-06-2021
* Author(s): Iyad Al-Kassab @ SKITSC
* Description: get the list of recordings by offset
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

if (isset($_GET['search']) && !empty($_GET['search'])) {

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