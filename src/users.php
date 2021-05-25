<?php

/*
* Date: 25-05-2021
* Author(s): Iyad Al-Kassab @ SKITSC
* Description: users page
*/

require_once("./middlewares/auth.php");

require '../vendor/autoload.php';

require "db.php";

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader);

// globals
$twig->addGlobal("username", $_SESSION['username']);

$users_array = array();

$sql = "SELECT * FROM backer_users";

if ($stmt = $pdo->prepare($sql)) {
    if ($stmt->execute()) {
        while ($row = $stmt->fetch()) {
            array_push($users_array, $row);
        }
    } else {
        echo "error with stmt!";
    }
}

// add - remove a user
$username = $password = $password_repeat = "";
$username_err = $password_err = $process_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['add']) && !empty($_POST['add'])) {
        if (strcmp($_POST['add'],"true") == 0) {

            // add a user
            
            // verify username
            if (isset($_POST['username']) && !empty($_POST['username'])) {
                $username = $_POST['username'];
                if(preg_match('/^[a-zA-Z0-9]{5,}$/', $username)) {
                    // valid username, alphanumeric and longer than or equals 5 chars
                } else {
                    $username_err = "Username can only contain alphanumeric characters and must be longer than 5 characters";
                }
            } else {
                $username_err = "Please fill the username...";
            }
        
            // verify password
            if (isset($_POST['password']) && !empty($_POST['password'])) {
        
                $password = $_POST['password'];
                if (isset($_POST['password_repeat']) && !empty($_POST['password_repeat'])) {
                    $password_repeat = $_POST['password_repeat'];
                    if (strcmp($password, $password_repeat) != 0) {
                        $password_err = "Incorrect password confirmation";
                    }
                } else {
                    $password_err = "Incorrect password confirmation";
                }
        
                $uppercase = preg_match('@[A-Z]@', $password);
                $lowercase = preg_match('@[a-z]@', $password);
                $number    = preg_match('@[0-9]@', $password);
                
                if(!$uppercase || !$lowercase || !$number || strlen($password) < 8) {
                    $password_err = "Password must contain at least 1 lowercase, 1 uppercase and 8 characters";
                }
            } else {
                $password_err = "Please fill the password...";
            }

            // insert
            if (empty($username_err) && empty($password_err)) {
                $sql = "INSERT INTO backer_users (username, password) VALUES (?, ?)";

                if ($stmt = $pdo->prepare($sql)) {
            
                    $stmt->bindParam(":username", $username, PDO::PARAM_STR);
                    $stmt->bindParam(":password", $password, PDO::PARAM_STR);
            
                    if ($stmt->execute()) {
                        echo "New user added successfully!";
                    } else {
                        echo "error with stmt!";
                    }
                }
            }
        }
    } else if (isset($_POST['remove']) && !empty($_POST['remove'])) {
        if (strcmp($_POST['remove'],"true") == 0) {

            // remove a user

            // verify user_id
            if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {

                $user_id = $_POST['user_id'];
        
                if (preg_match('/^\d+$/', $user_id)) {
                    // valid input.
                  } else {
                    $user_id_err = "invalid user id, you must input the id number";
                  }
            } else {
                $user_id_err = "invalid user id";
            }

            // delete
            if (empty($user_id_err)) {
                $sql = "DELETE FROM backer_users WHERE id=?";

                if ($stmt = $pdo->prepare($sql)) {
            
                    $stmt->bindParam(":id", $user_id, PDO::PARAM_STR);
            
                    if ($stmt->execute()) {
                        echo "Removed user successfully!";
                    } else {
                        echo "error with stmt!";
                    }
                }
            }
        }
    } else {
        // nothing
    }

}

// unset db
unset($stmt);
unset($pdo);

echo $twig->render('users.twig', ['users' => $users_array]);
?>


