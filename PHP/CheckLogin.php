<?php
session_start();

//Connect to Database
require_once "PHPConntectSQL.php";

//Errors array to store errors
$errors = [];

function validation($user, $pass, &$errors) {

    if (empty($user)) {
        $errors['emptyUser'] = "Username must be filled";
    }

    if (empty($pass)) {
        $errors['emptyPass'] = "Password must be filled";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user = trim($_POST['username']);
    $pass = $_POST['password'];

    validation($user, $pass, $errors);

    //If basic errors
    if (!empty($errors)) {
        $_SESSION['loginErrors'] = $errors;
        $_SESSION['Data'] = $_POST;
        header("Location: Login.php");
        exit;
    }//End if

    //If not check database
    $stmt = $conn->prepare("SELECT Username, Password FROM users WHERE Username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    //If username found
    if ($result->num_rows === 1) {

        $row = $result->fetch_assoc();//Fetch the matching row

        //Check password
        if (password_verify($pass, $row['Password'])) {//password_verifY - Verifies that the given hash matches the given password

            //Successful Login
            $_SESSION['loggedIn'] = true;
            $_SESSION['username'] = $row['Username'];

            header("Location: ../Index.php");
            exit;
        }//End if
    }//End if

    //If username not found OR password wrong
    $errors['invalid'] = "Incorrect username or password";
    $_SESSION['loginErrors'] = $errors;
    $_SESSION['Data'] = $_POST;
    header("Location: Login.php");
    exit;
}
?>
