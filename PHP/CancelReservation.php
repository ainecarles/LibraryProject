<?php
session_start();

require_once "PHPConntectSQL.php";

if (!isset($_SESSION['username'])) {
    header("Location: Login.php");
    exit;
}if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $isbn = $_POST['isbn'];
    $username = $_SESSION['username'];

    //Delete reservation
    $sql = "DELETE FROM reservations WHERE ISBN = ? AND Username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $isbn, $username);

    if($stmt->execute()){
        //Reset Reserved in books
        $updateSql = "UPDATE books SET Reserved = 0 WHERE ISBN = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("s", $isbn);
        $updateStmt->execute();
        
        $_SESSION['message'] = "Reservation cancelled";
    }
    else{
        $_SESSION['message'] = "Error cancelling reservation" . $stmt->error;
    }

    header("Location: MyProfile.php");
    exit;


}



?>