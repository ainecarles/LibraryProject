<?php

//Function for Validation
function validation($u, $p, $cp, $f, $s, $a1, $a2, $c, $t, $m, &$errors){

    //Username:
    if(empty($u)){
        $errors['emptyU'] = "Username cannot be empty";
    }

    if(preg_match('/\s/', $u)){
        $errors['whiteSpaceU'] = "Username can't have spaces";

    }

    if(!preg_match('/^[A-Za-z0-9_]+$/', $u)){
        $errors['incorrectCharU'] = "Invalid characters used <br> Username can only contain, letters, numbers, and underscore";
    }

    //Password
    if(empty($p)){
        $errors['emptyP'] = "Password can't be empty";
    }


    if($p !== $cp){
        $errors['noMatchP'] = "Passwords don't match";
    }

    //First Name and Surname
    if(empty($f)) $errors['emptyF'] = "First name can't be empty";
    if(empty($s)) $errors['emptyS'] = "Surname can't be empty";

    //Address line 1
    if(empty($a1)) $errors['emptyA1'] = "Address line 1 can't be empty";

    if(!preg_match('/^[A-Za-z0-9,-]+$/', $a1)){
        $errors['incorrectCharA1'] = "Invalid characters used <br> Addresses can only contain numbers, letters, dashes, and commas";
    }

    //Address Line 2
    if(!preg_match('/^[A-Za-z0-9,-]+$/', $a2)){
        $errors['incorrectCharA2'] = "Invalid characters used <br> Addresses can only contain numbers, letters, dashes, and commas";
    }

    //City
    if(!preg_match('/^[A-Za-z]+$/', $c)){
        $errors['incorrectCharC'] = "Invalid characters used <br> City can only contain letters";
    }

    //Telephone
    if(!preg_match('/^[0-9]+$/', $t)){
        $errors['incorrectCharT'] = "Invalid characters used <br> Telephone can only contain numbers";
    }

    //Mobile
    if(!preg_match('/^[0-9]+$/', $m)){
        $errors['incorrectCharM'] = "Invalid characters used <br> Mobile can only contain numbers";
    }

}

//Connect to the database
require_once "PHPConntectSQL.php";

//Errors array to store errors
$errors = [];
   
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    $cp= $_POST['confirmPassword'] ?? '';
    $f = trim($_POST['firstName'] ?? '');
    $s = trim($_POST['surname'] ?? '');
    $a1 = trim($_POST['addressLine1'] ?? '');
    $a2 = trim($_POST['addressLine2'] ?? '');
    $c = trim($_POST['city'] ?? '');
    $t = trim($_POST['telephone'] ?? '');
    $m = trim($_POST['mobile'] ?? '');

    validation($u, $p, $cp, $f, $s, $a1, $a2, $c, $t, $m, $errors);


    if(empty($errors)){

        //Hash password for security
        $hashedPwd = password_hash($p, PASSWORD_DEFAULT);

        //Insert data into database
        $stmt = $conn->prepare("INSERT INTO users (Username, Password, Firstname, Surname, AddressLine1, AddressLine2, City, Telephone, Mobile)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
        $stmt->bind_param("sssssssss",
                            $u,
                            $hashedPwd,
                            $f,
                            $s,
                            $a1,
                            $a2,
                            $c,
                            $t,
                            $m);

        if($stmt->execute()){
            echo "User created successfully";
        }
        else{
           echo "Error: " . $stmt->error; 
        }

        $stmt -> close();
    }

    else{

        foreach($errors as $error){
            echo $error;
        }
        
    }
    

}   

$conn->close();
?>
