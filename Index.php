<?php
session_start();

$loggedIn = $_SESSION['loggedIn'] ?? false;
$username = $_SESSION['username'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<!-- Boiler Plate -->

<head>
    <meta charset="UTF-8" />
    <title>Irish Libraries</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="description" content="Library site to search, reserve, and view all the books a user has reserved" />
    <link rel="stylesheet" href="CSS/Index.css">
</head>

<body>

    <!-- Nav Bar-->
    <div class="container">

        <div class="navbar">

            <div class="logo">
                <img src="Media/Logo.png" alt="Brand Logo">
            </div>

            <p class="navTitle">Irish Libraries</p>

            <ul>
                <li><!-- Display Different Options on NavBar if User is Logeged In-->
                    <?php if(!empty($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true):?>
                        <li><a href="PHP/MyProfile.php">My Profile</a></li>
                        <li><a href="PHP/LogOut.php">Log Out</a></li>
                    <?php else: ?>
                        <a href="PHP/login.php">My Profile</a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>


        
    <div class="pageContent">

        <div class="leftContent">
            <p class="welcome"> Welcome to Irish Libraries</p>
            <p>Discover thousands of books at your fingertips. <br> Search, reserve, and manage your reading journey all in one place.<p>
        </div>
        
        <?php if(!empty($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true):?>
            <div class="lm">
                <p class="ll">Welcome, <?php echo htmlspecialchars($username);?></p> <!-- Welcome Message if User is Logged In-->
            </div>
        <?php else: ?>
            <div class="buttonRow">
                <a href="PHP/Login.php" class="btn1">Log in</a>
                <a href="PHP/Signup.php" class="btn2">Sign up</a>
            </div>
        <?php endif; ?>

        <div class="rightImg">
            <img src="Media/Library.png" alt="Picture of library">
        </div>

    </div>

    
    <footer>©2025 Irish Libraries</footer>
    

</body>

</html>