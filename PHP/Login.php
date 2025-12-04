<?php

session_start();
$errors = $_SESSION['loginErrors'] ?? '';
$Data = $_SESSION['Data'] ?? [];
$user  = $Data['username'] ?? '';
unset($_SESSION['loginErrors'], $_SESSION['Data']);

?>

<!DOCTYPE html>
<html lang="en">


<!-- Boiler Plate -->
<head>
    <meta charset="UTF-8" />
    <title>Irish Libraries Login</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="description" content="Sign Up Page" />
    <link rel="stylesheet" href="../CSS/Login.css">
</head>

<body>
    <!-- Nav Bar -->
    <div class="container">

        <div class="navbar">

            <div class="logo">
                <img src="../Media/Logo.png" alt="Brand Logo">
            </div>

            <p class="navTitle">Irish Libraries</p>

            <ul>
                <li><a href="../Index.php">Home</a></li>
                <li><a href="Signup.php">Sign Up</a></li>
            </ul>
        </div>
    </div>

    <div class="pageContent">
        <h3>Log In</h3>

        <div class="formBox">
            <form action="CheckLogin.php" method="post">

                <div class="field">
                    <label for="username">Username</label>
                    <input type="text" name="username"
                    value="<?php echo htmlspecialchars($user);?>"><!-- Remember Input Values -->

                    <!-- Display username error -->
                    <?php if(isset($errors['emptyUser'])) echo "<p class='error'>{$errors['emptyUser']}</p>";?>
                </div> <!-- End field -->

                <div class="field">
                    <label for="password">Password</label>
                    <input type="password" name="password">

                    <!-- Display password error  -->
                    <?php if(isset($errors['emptyPass'])) echo "<p class='error'>{$errors['emptyPass']}</p>";?>
                </div> <!-- End field -->

                <!-- Error: If username or password not found -->
                <?php if(isset($errors['invalid'])) echo "<p class='error'>{$errors['invalid']}</p>";?>

                 <div>
                    <input type="submit" value="Log In">
                </div>

                <div class="redirect">
                    <p>Don't have an account? <a href="Signup.php">Sign up here</a></p>
                </div><!-- End redirect -->
            </form>
        </div> <!-- End formBox -->

        <footer>©2025 Irish Libraries</footer>

    </div><!-- End pageContent -->
</body>
</html>


