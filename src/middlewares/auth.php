<?php

/*
* Date: 21-05-2021
* Author(s): Iyad Al-Kassab @ SKITSC
* Description: verify if user authentified, if yes, go to dashboard
*/

session_start();
 
if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true){
    header("location: dashboard.php");
    exit;
}

?>