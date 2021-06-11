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

// globals
$twig->addGlobal("username", $_SESSION['username']);
$twig->addGlobal("last_login", $_SESSION['last_login']);

// unset db
unset($stmt);
unset($pdo);

echo $twig->render('recordings.twig');

?>