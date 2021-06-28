<?php

/*
* Date: 26-05-2021
* Author(s): Iyad Al-Kassab @ SKITSC
* Description: get the size of the recordings folder
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