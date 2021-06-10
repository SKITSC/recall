<?php

/*
* Date: 26-05-2021
* Author(s): Iyad Al-Kassab @ SKITSC
* Description: get the size of the recordings folder
*/

require_once(dirname(__FILE__) . "/../middlewares/auth.php");

// set max execution time to unlimited
ini_set('max_execution_time', 0); // 0 = Unlimited

$path = "../../recordings";
if (!file_exists($path)) {
    mkdir($path);
}

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($path)
);

$totalSize = 0;
foreach ($iterator as $file) {
    $totalSize += $file->getSize();
}

header('Content-type: application/json');
echo number_format($totalSize, 0, ".", " ");

?>