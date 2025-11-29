<?php

session_start();

//Connect to the database
require_once "PHPConntectSQL.php";

//Errors array to store errors
$errors = [];

//Function for Validation
function validation($u, $p, $cp, $f, $s, $a1, $a2, $c, $t, $m, &$errors, $conn){

    //General errors
    if(empty($u) || empty($p) || empty($cp) || empty($f) || empty($s) || empty($a1) || empty($m)){
        $errors['empty'] = "All required fields must be filled";
    }

    //Username:
    if(preg_match("/\s/", $u)){
        $errors['whiteSpaceU'] = "Username can't have spaces";
    }

    else if(!preg_match("/^[A-Za-z0-9_]+$/", $u)){
            $errors['incorrectCharU'] = "Invalid characters used Username can only contain, letters, numbers, and underscore";
    }

    else{
        $stmt = $conn->prepare("SELECT Username FROM users WHERE Username = ?");
        $stmt->bind_param("s", $u);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows > 0){
            $errors['duplicateU'] = "Username already taken";
        }

        $stmt->close();
    }
        
    
    //Password
    if($p !== $cp){
        $errors['noMatchP'] = "Passwords don't match";
    }

    else if(strlen($p) < 6){
        $errors['shortP'] = "Password must be at least 6 characters long";
    }
    

    //Surname
    if(!preg_match("/^[A-Za-z'-]+$/", $s)){
        $errors['incorrectCharN'] = "Incorrect characters used Names can only contain letters, apostrophes, and hyphens";
    }
    

    //Addresses
    if(!preg_match("/^[A-Za-z0-9 ,.'-]+$/", $a1)){
        $errors['incorrectCharA'] = "Invalid characters used Addresses can only contain numbers, letters, dashes, and commas";
    }

    //City
    if(!preg_match("/^[A-Za-z ,.'-]*$/", $c)){
        $errors['incorrectCharC'] = "Invalid characters used City can only contain letters";
    }

    //Telephone
    if(!preg_match("/^(0[124569]\d{5,7})$/", $t)){
    $errors['errorT'] = "Telephone number has invalid starting digits" . "<br>" . "Telephone must be at least 7 digits";
    }

    //Mobile 
    if(!preg_match("/^08\d{8}$/", $m)){
        $errors['errorM'] = "Mobile number must start with 08 and be 10 digits";
    }

}

//Function for cleaning input
function cleanData($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
   
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    $cp= $_POST['confirmPassword'] ?? '';
    $f = $_POST['firstName'] ?? '';
    $s = $_POST['surname'] ?? '';
    $a1 = $_POST['addressLine1'] ?? '';
    $a2 = $_POST['addressLine2'] ?? '';
    $c = $_POST['city'] ?? '';

    $t = $_POST['telephone'] ?? '';
    $t = preg_replace("/\D/", '', $t);

    $m = $_POST['mobile'] ?? '';
    $m = preg_replace("/\D/", '', $m);;

    validation($u, $p, $cp, $f, $s, $a1, $a2, $c, $t, $m, $errors, $conn);

    if(empty($errors)){

        //Clean data
        $u = cleanData($u);
        $f = cleanData($f);
        $s = cleanData($s);
        $a1 = cleanData($a1);
        $a2 = cleanData($a2);
        $c = cleanData($c);
        $t = cleanData($t);
        $m = cleanData($m); 


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
            $stmt->close();
            header("Location: ../Index.html");
        }

        $stmt->close();
    }
    else{
        $_SESSION['errors'] = $errors;
        $_SESSION['formData'] = $_POST;
        header("Location: Signup.php");
        exit;
    }


}   

$conn->close();
?>