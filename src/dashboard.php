<?php

/*
* Date: 21-05-2021
* Author(s): Iyad Al-Kassab @ SKITSC
* Description: dashboard page
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

require_once("./middlewares/auth.php");

require '../vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader);

require_once "../config.php";
require "db.php";

// globals
$twig->addGlobal("username", $_SESSION['username']);
$twig->addGlobal("last_login", $_SESSION['last_login']);

// fetch call number

$total_calls_db = 0;
$sql = 'SELECT COUNT(call_uuid) FROM backer_recordings';

$stmt = $pdo->prepare($sql);

if ($stmt->execute()) {
    
    $total_calls_db = $stmt->fetchColumn();
} else {
	$process_err = "Error with STMT!";
	die();
}

$recordings_array = array();
$sql = 'SELECT * FROM backer_recordings ORDER BY id DESC LIMIT 10';

$stmt = $pdo->prepare($sql);

if ($stmt->execute()) {
    while ($row = $stmt->fetch()) {
        array_push($recordings_array, $row);
    }
} else {
    $process_err = "error with stmt!";
}

$average_call_time = 0;
$sql = 'SELECT AVG(recording_duration) FROM backer_recordings';

$stmt = $pdo->prepare($sql);

if ($stmt->execute()) {
    $average = $stmt->fetchColumn();
    if ($average > 0) {
        $average_call_time = $average;
    }
} else {
    $process_err = "error with stmt!";
}

// unset db
unset($stmt);
unset($pdo);

echo $twig->render('dashboard.twig', ['total_calls_db' => $total_calls_db,
                                    'recordings_array' => $recordings_array,
                                    'average_call_time' => $average_call_time]);

?>