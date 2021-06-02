<?php

/*
* Date: 21-05-2021
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
 
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
if ($_SERVER["REQUEST_METHOD"] == "POST"){
 
    //is username or password empty
    if (empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }
    
    if (empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    if (empty($username_err) && empty($password_err)) {

        $sql = "SELECT id, username, password FROM backer_users WHERE username = :username";
        
        if ($stmt = $pdo->prepare($sql)) {

            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = trim($_POST["username"]);

            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        
                        $id = $row["id"];
                        $username = $row["username"];
                        $hashed_password = $row["password"];
                        if (password_verify($password, $hashed_password)) {
                            
                            session_start();
                            
                            $_SESSION["logged_in"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            header("location: dashboard.php");
                        } else{
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Error with stmt!";
            }

            unset($stmt);
        }
    }
    
    unset($pdo);
}

echo $twig->render('login.twig', ['username' => $username,
                                'username_err' => $username_err,
                                'password_err' => $password_err,
                                'login_err' => $login_err]);

?>