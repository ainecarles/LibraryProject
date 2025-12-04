<?php
session_start();
$errors = $_SESSION['errors'] ?? [];
$formData = $_SESSION['formData'] ?? [];
$u  = $formData['username'] ?? '';
$f  = $formData['firstName'] ?? '';
$s  = $formData['surname'] ?? '';
$a1 = $formData['addressLine1'] ?? '';
$a2 = $formData['addressLine2'] ?? '';
$c  = $formData['city'] ?? '';
$t  = $formData['telephone'] ?? '';
$m  = $formData['mobile'] ?? '';

unset($_SESSION['errors'], $_SESSION['formData']); //Clear after use
?>

<!DOCTYPE html>
<html lang="en">


<!-- Boiler Plate -->
<head>
    <meta charset="UTF-8" />
    <title>Irish Libraries Signup</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="description" content="Sign Up Page" />
    <link rel="stylesheet" href="../CSS/Signup.css">
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
                <li><a href="../Index.html">Home</a></li>
                <li><a href="Login.php">Log In</a></li>
            </ul>
        </div>
    </div>

    <div class="pageContent">
        <h3 class="header">Sign Up</h3>
        <p class="header">Create your account to start reserving books</p>

        <!-- Sign up form -->
         <div class="formBox">
            <form action="Insert.php" method="post">

                <div class="field">
                    <label for="username" class="required">Username</label>
                    <input type="text" name="username" placeholder="Eg: user_123"
                    value="<?php echo htmlspecialchars($u); ?>"><!--Remember input values -->

                    <!-- Display Username Errors -->
                    <?php 
                    if (isset($errors['emptyU'])) echo "<p class='error'>{$errors['emptyU']}</p>";
                    if (isset($errors['whiteSpaceU'])) echo "<p class='error'>{$errors['whiteSpaceU']}</p>";
                    if (isset($errors['incorrectCharU'])) echo "<p class='error'>{$errors['incorrectCharU']}</p>";
                    if (isset($errors['tooLongU'])) echo "<p class='error'>{$errors['tooLongU']}</p>";
                    if (isset($errors['duplicateU'])) echo "<p class='error'>{$errors['duplicateU']}</p>";
                    ?>
                </div>

                <div class="twoCol">
                    <div class="field">
                        <label for="password" class="required">Password</label>
                        <input type="password" name="password" placeholder="Min. 6 characters">
                        
                        <!-- Display Password errors -->
                        <?php
                        if (isset($errors['emptyP'])) echo "<p class='error'>{$errors['emptyP']}</p>";
                        if (isset($errors['noMatchP'])) echo "<p class='error'>{$errors['noMatchP']}</p>";
                        if (isset($errors['shortP'])) echo "<p class='error'>{$errors['shortP']}</p>";
                        ?>
                    </div>

                    <div class="field">
                        <label for="confirmPassword" class="required">Confirm Password</label>
                        <input type="password" name="confirmPassword" placeholder="Re-enter password">

                        <?php if (isset($errors['emptyCP'])) echo "<p class='error'>{$errors['emptyCP']}</p>";?>
                    </div>
                </div><!-- End twoCol -->

                <div class="twoCol">
                    <div class="field">
                        <label for="firstName" class="required">First Name</label>
                        <input type="text" name="firstName" placeholder="John"
                        value="<?php echo htmlspecialchars($f); ?>">

                        <!-- Display First Name Errors -->
                        <?php
                        if (isset($errors['emptyFN'])) echo "<p class='error'>{$errors['emptyFN']}</p>";
                        if (isset($errors['incorrectCharFN'])) echo "<p class='error'>{$errors['incorrectCharFN']}</p>";
                        ?>            
                    </div>

                    <div class="field">
                        <label for="surname" class="required">Surname</label>
                        <input type="text" name="surname" placeholder="Doe" 
                        value="<?php echo htmlspecialchars($s); ?>">

                        <!-- Display Surname Errors -->
                        <?php
                        if (isset($errors['emptyS'])) echo "<p class='error'>{$errors['emptyS']}</p>";
                        if (isset($errors['incorrectCharS'])) echo "<p class='error'>{$errors['incorrectCharS']}</p>";
                        ?>    
                    </div>
                </div><!-- End Two Col -->

                <div class="field">
                    <label for="addressLine1" class="required">Address Line 1</label>
                    <input type="text" name="addressLine1" placeholder="Eg. 24 Main Street"
                    value="<?php echo htmlspecialchars($a1); ?>">

                    <!-- Display Address Errors -->
                    <?php
                    if (isset($errors['emptyA1'])) echo "<p class='error'>{$errors['emptyA1']}</p>";
                    if (isset($errors['incorrectCharA1'])) echo "<p class='error'>{$errors['incorrectCharA1']}</p>";
                    ?>
                </div>

                <div class="field">
                    <label for="addressLine2">Address Line 2</label>
                    <input type="text" name="addressLine2" placeholder="Apartment, suite etc. (optional)"
                    value="<?php echo htmlspecialchars($a2);?>">

                    <!-- Display Address Errors -->
                    <?php
                    if (isset($errors['incorrectCharA2'])) echo "<p class='error'>{$errors['incorrectCharA2']}</p>";
                    ?>
                </div>

                <div class="field">
                    <label for="city">City</label>
                    <input type="text" name="city" placeholder="Dublin"
                    value="<?php echo htmlspecialchars($c); ?>">

                    <!-- Display City Errors -->
                    <?php
                    if (isset($errors['incorrectCharC'])) echo "<p class='error'>{$errors['incorrectCharC']}</p>";
                    ?>
                </div>

                <div class="twoCol">
                    <div class="field">
                        <label for="telephone">Telephone</label>
                        <input type="tel" name="telephone" placeholder="Eg. 019296673 (optional)"
                        value="<?php echo htmlspecialchars($t); ?>">

                        <!-- Display Telehone Errors -->
                        <?php
                        if (isset($errors['errorT'])) echo "<p class='error'>{$errors['errorT']}</p>";
                        ?>
                    </div>

                    <div class="field">
                        <label for="mobile" class="required">Mobile</label>
                        <input type="tel" name="mobile" placeholder="Eg. 0873647689 (10 digits)"
                        value="<?php echo htmlspecialchars($m); ?>">

                        <!-- Display Mobile Phone Errors -->
                        <?php
                        if (isset($errors['emptyM'])) echo "<p class='error'>{$errors['emptyM']}</p>";
                        if (isset($errors['errorM'])) echo "<p class='error'>{$errors['errorM']}</p>";
                        ?>
                    </div>
                </div><!-- End Two Col -->

                <div>
                    <input type="submit" value="Sign Up">
                </div>

                <!-- Redirect to Login Page-->
                 <div class="redirect">
                    <p> Already have an account? <a href="Login.php"> Log in here </a></p>
                </div>
            </form>
        </div><!-- End formBox -->

        <footer>©2025 Irish Libraries</footer>

    </div><!-- End pageContent -->

        
        
</body>
</html>
