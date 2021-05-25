<?php

/*
* Date: 21-05-2021
* Author(s): Iyad Al-Kassab @ SKITSC
* Description: verify if user authentified, if yes, continue to requested page
*/

if (!isset($_SESSION)) {
    session_start();
}
 
if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true) {} else {
    if (strpos($_SERVER['REQUEST_URI'], "login.php") !== false){
        // already going to login.php
    } else {
        header("location: login.php");
        exit;
    }
}

?>