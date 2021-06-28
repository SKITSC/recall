<?php

/*
* Date: 10-06-2021
* Author(s): Iyad Al-Kassab @ SKITSC
* Description: get the list of recordings by offset
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

require_once(dirname(__FILE__) . '/../../vendor/autoload.php');

require_once(dirname(__FILE__) . "/../../config.php");
require_once(dirname(__FILE__) . "/../db.php");

// environment

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__FILE__) . "/../..");
$dotenv->load();

$process_err = "";

$sql_query = $stmt = "";

$search_set = false;
$page_set = false;
if (isset($_GET['search_phone']) && !empty($_GET['search_phone'])) {
    $search_phone = htmlspecialchars($_GET['search_phone']);
    $search_set = true;
    $page = 1;
    if (isset($_GET['page']) && !empty($_GET['page'])) {
        if (is_numeric($_GET['page'])) {
            $page = $_GET['page'];
            $page_set = true;
            $sql_query = "SELECT * FROM backer_recordings WHERE (from_number LIKE :sql_from OR to_number LIKE :sql_to) LIMIT " . ($page - 1) * 50 . ",50";
        }
    } else {
        $sql_query = "SELECT * FROM backer_recordings WHERE (from_number LIKE :sql_from OR to_number LIKE :sql_to)";
    }
    
    $stmt = $pdo->prepare($sql_query);
    
    $search_phone = "%" . $search_phone . "%";

    $stmt->bindParam(":sql_from", $search_phone);
    $stmt->bindParam(":sql_to", $search_phone);
} else {
    if (isset($_GET['page']) && !empty($_GET['page'])) {
        $page = $_GET['page'];
        if (is_numeric($page)) {
            $sql_query = "SELECT * FROM backer_recordings LIMIT " . ($page - 1) * 50 . ",50";

            $stmt = $pdo->prepare($sql_query);
        } else {
            $process_err = "page must be numeric";
        }
    } else {
        $sql_query = "SELECT * FROM backer_recordings LIMIT 50";
    }
}

$util_data = "";

if (!empty($stmt)) {
    if ($stmt->execute()) {
        if ($search_set == true && $page_set == false) {
            $row_count = $stmt->rowCount();
            $util_data = $row_count;
            if ($row_count == 0) {
                $util_data = "No recordings...";
            }
        } else {
            while ($row = $stmt->fetch()) {
                $util_data .= "<tr class='row-hover' onclick=play_audio(" . "'" . $row[3] . "'" . ")>";
                    $util_data .= "<td class='table-recordings-cell col-call-number'>";
                    $util_data .= sprintf("%02d", $row[0]) . ". ";
                    $util_data .= "</td>";
                    $util_data .= "<td class='table-recordings-cell col-call-uuid'>";
                    $util_data .= $row[1];
                    $util_data .= "</td>";
                    $util_data .= "<td class='table-recordings-cell col-call-time'>";
                    $util_data .= $row[2];
                    $util_data .= "</td>";
                    $util_data .= "<td class='table-recordings-cell col-call-duration'>";
                    $duration_seconds = $row[6];
                    $util_data .= sprintf("%02d:%02d:%02d", $duration_seconds/3600, $duration_seconds/60 % 60, $duration_seconds % 60 );
                    $util_data .= "</td>";
                    $util_data .= "<td class='table-recordings-cell col-call-from'>";
                    $util_data .= $row[7];
                    $util_data .= "</td>";
                    $util_data .= "<td class='table-recordings-cell col-call-to'>";
                    $util_data .= $row[8];
                    $util_data .= "</td>";
                    $util_data .= "<td class='table-recordings-cell col-download'>";
                    $util_data .= "<a class='download_button' target='_blank' rel='noopener noreferrer' href='" . $row[3] . "'><img class='download-img' src='static/img/download.svg' /></div></a>";
                    $util_data .= "</td>";
                $util_data .= "</tr>";
            }
        }
    } else {
        $process_err = "error with stmt!";
    }
} else {
    $process_err = "failed to obtain query!";
}

header('Content-type: application/json');

if (empty($util_data)) {
    $util_data = "<tr><td colspan='6' style='text-align:center;padding-top:15px;'>Nothing to see here...</td></tr>"; //or "no data..." ?
}
echo $util_data;

?>