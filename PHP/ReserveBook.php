<?php
session_start();

require_once "PHPConntectSQL.php";

//Ensure User is Logged In
if(!isset($_SESSION['username'])){
    header("Location: Login.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $isbn = $_POST['isbn'];
    $username = $_SESSION['username'];

    //Insert Reservation
    $sql = "INSERT INTO reservations (ISBN, Username, ReservedDate)
            VALUES (?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $isbn, $username);

    if($stmt->execute()){
        //Update Reserved in books
        $updateSql = "UPDATE books SET Reserved = 1 WHERE ISBN = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("s", $isbn);
        $updateStmt->execute();

        $_SESSION['message'] = "Book reserved successfully";

    }
    else{
        $_SESSION['message'] = "Error reserving book" . $stmt->error;
    }

}

// Redirect back to profile
header("Location: MyProfile.php");
exit;

?>