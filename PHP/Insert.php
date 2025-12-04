<?php

session_start();

//Connect to the database
require_once "PHPConntectSQL.php";

//Errors array to store errors
$errors = [];

//Function for Validation
function validation($u, $p, $cp, $f, $s, $a1, $a2, $c, $t, $m, &$errors, $conn){

    //Username:
    if(empty($u)){
        $errors['emptyU'] = "Username must be filled";
    }

    else if(preg_match("/\s/", $u)){
        $errors['whiteSpaceU'] = "Username can't have spaces";
    }

    else if(!preg_match("/^[A-Za-z0-9_]+$/", $u)){
            $errors['incorrectCharU'] = "Username can only contain, letters, numbers, and underscore";
    }

    else if(strlen($u) > 15){
        $errors['tooLongU'] = "Username has a maximum of 15 characters";
    }

    else{//Check for duplicate usernames in database
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
    if(empty($p)){
        $errors['emptyP'] = "Password must be filled";
    }

    else if($p !== $cp){
        $errors['noMatchP'] = "Passwords don't match";
    }

    else if(strlen($p) < 6){
        $errors['shortP'] = "Password must be at least 6 characters long";
    }

    //Confirm Password
    if(empty($cp)){
        $errors['emptyCP'] = "Confirm Password must be filled";
    }
    

    //First Name
    if(empty($f)){
        $errors['emptyFN'] = "First name must be filled";
    }

    else if(!preg_match("/^[A-Za-z'-]+$/", $f)){
        $errors['incorrectCharFN'] = "Names can only contain letters and the following: ' -";
    }

    //Surname
    if(empty($s)){
        $errors['emptyS'] = "Surname must be filled";
    }

    else if(!preg_match("/^[A-Za-z'-]+$/", $s)){
        $errors['incorrectCharS'] = "Names can only contain letters and the following: ' -";
    }
    

    //Address Line 1
    if(empty($a1)){
        $errors['emptyA1'] = "Address Line 1 must be filled";
    }
    
    else if(!preg_match("/^[A-Za-z0-9 ,.'-]+$/", $a1)){
        $errors['incorrectCharA1'] = "Addresses can only contain numbers, letters and the following: , . ' -";
    }

    //Address Line 2
    if(!empty($a2)){
        if(!preg_match("/^[A-Za-z0-9 ,.'-]+$/", $a2)){
            $errors['incorrectCharA2'] = "Addresses can only contain numbers, letters and the following: , . ' -";
        }
    }



    //City
    if(!empty($c)){
        if(!preg_match("/^[A-Za-z ,.'-]*$/", $c)){
            $errors['incorrectCharC'] = "City can only contain letters and the following: , . ' -";
        }
    }


    //Telephone
    if(!empty($t)){
        if(!preg_match("/^(0[124569]\d{5,7})$/", $t)){
            $errors['errorT'] = "Invalid starting digits telephone must be at least 7 digits"; 
        }
    }

    //Mobile 
    if(empty($m)){
        $errors['emptyM'] = "Mobile must be filled";
    }

    else if(!preg_match("/^08\d{8}$/", $m)){
        $errors['errorM'] = "Mobile number must start with 08 and be 10 digits";
    }

}//End validatio
// n

//Function for cleaning input
function cleanData($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}//End cleanData
   
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $u = $_POST['username'];
    $p = $_POST['password'];
    $cp= $_POST['confirmPassword'];
    $f = $_POST['firstName'];
    $s = $_POST['surname'];
    $a1 = $_POST['addressLine1'];
    $a2 = $_POST['addressLine2'];
    $c = $_POST['city'];

    $t = $_POST['telephone'];
    $t = preg_replace("/\D/", '', $t);

    $m = $_POST['mobile'];
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
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");//$stmt = statement object to hold SQL, ? = placeholder
    
        $stmt->bind_param("sssssssss",
                            $u,
                            $hashedPwd,
                            $f,
                            $s,
                            $a1,
                            $a2,
                            $c,
                            $t,
                            $m);//Bind the value. "s" - Type of parameter, $u etc -Inject this into the ?

        if($stmt->execute()){//Then execute it
            $_SESSION['loggedIn'] = true;
            $_SESSION['username'] = $u; 
            $stmt->close();
            $conn->close();
            header("Location: MyProfile.php");
            exit;
        }//End if

        
    }//End if
    else{
        $conn->close();
        $_SESSION['errors'] = $errors;
        $_SESSION['formData'] = $_POST;
        header("Location: Signup.php");
        exit;
    }//End Else


}//End if

?>

<!--Remember to fucking encrypt the password too omfg ->