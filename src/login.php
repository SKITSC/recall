<?php

/*
* Date: 21-05-2021
* Author(s): Iyad Al-Kassab @ SKITSC
* Description: login page for service
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

        $sql = "SELECT id, username, password, last_login FROM backer_users WHERE username = :username";
        
        if ($stmt = $pdo->prepare($sql)) {

            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = trim($_POST["username"]);

            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        
                        $id = $row["id"];
                        $username = $row["username"];
                        $hashed_password = $row["password"];
                        $last_login = $row["last_login"];

                        if (password_verify($password, $hashed_password)) {
                            
                            session_write_close();
                            session_start();
                            
                            $_SESSION["logged_in"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["last_login"] = $last_login;          
                            
                            $update_login_sql = "UPDATE backer_users SET last_login = NOW() WHERE username = :username";
                            $stmt = $pdo->prepare($update_login_sql);
                            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);

                            if ($stmt->execute()) {} else {
                                echo "DB error!";
                            }
                            
                            header("location: dashboard.php");
                            exit();
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
        }
    }

    unset($stmt);
    unset($pdo);
}

echo $twig->render('login.twig', ['username' => $username,
                                'username_err' => $username_err,
                                'password_err' => $password_err,
                                'login_err' => $login_err]);

?>