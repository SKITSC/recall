<?php

/*
* Date: 25-05-2021
* Author(s): Iyad Al-Kassab @ SKITSC
* Description: users page
*/

require_once("./middlewares/auth.php");

require '../vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader);

$twig->addGlobal("username", $_SESSION['username']);

echo $twig->render('users.twig');

?>


