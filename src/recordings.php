<?php

/*
* Date: 26-05-2021
* Author(s): Iyad Al-Kassab @ SKITSC
* Description: login page for service
*/

require_once("./middlewares/auth.php");

require '../vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader);

require_once "../config.php";
require "db.php";

// unset db
unset($stmt);
unset($pdo);

// globals
$twig->addGlobal("username", $_SESSION['username']);

echo $twig->render('recordings.twig');

?>