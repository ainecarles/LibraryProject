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

unset($_SESSION['errors'], $_SESSION['formData']); // clear after use
?>

<!DOCTYPE html>
<html lang="en">


<!-- Boiler Plate -->
<head>
    <meta charset="UTF-8" />
    <title>Dublin Libraries Signup</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="description" content="Sign Up Page" />
</head>

<body>
    <h1>Sign Up</h1>

    <!-- Sign up form -->
    <form action="Insert.php" method="post">

        <div>
            <label for="username">Username:</label>
            <input type="text" name="username" placeholder="Eg: user_123"
            value="<?php echo htmlspecialchars($u); ?>" required><!--Remember input values -->

            <!-- Display Username Errors -->
             <?php 
             if (isset($errors['whiteSpaceU'])) echo "<p>{$errors['whiteSpaceU']}</p>";
             if (isset($errors['incoorectCharU'])) echo "<p>{$errors['incorrectCharU']}</p>";
             if (isset($errors['duplicateU'])) echo "<p>{$errors['duplicateU']}</p>";
             ?>
        </div>

        <div>
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            
            <!-- Display Password errors -->
             <?php
             if (isset($errors['noMatchP'])) echo "<p>{$errors['noMatchP']}</p>";
             if (isset($errors['shortP'])) echo "<p>{$errors['shortP']}</p>";
             ?>
        </div>

        <div>
            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" name="confirmPassword" required>
        </div>

        <div>
            <label for="firstName">First Name:</label>
            <input type="text" name="firstName" 
            value="<?php echo htmlspecialchars($f); ?>"required>

            <!-- Display First Name Errors -->
            <?php
            if (isset($errors['incorrectCharN'])) echo "<p>{$errors['incorrectCharN']}</p>";
            ?>            
        </div>

        <div>
            <label for="surname">Surname:</label>
            <input type="text" name="surname" 
            value="<?php echo htmlspecialchars($s); ?>"required>

            <!-- Display Surname Errors -->
            <?php
            if (isset($errors['incorrectCharN'])) echo "<p>{$errors['incorrectCharN']}</p>";
            ?>    
        </div>

        <div>
            <label for="addressLine1">Address Line 1:</label>
            <input type="text" name="addressLine1" placeholder="Eg. 24 Main Street"
            value="<?php echo htmlspecialchars($a1); ?>"required>

            <!-- Display Address Errors -->
            <?php
            if (isset($errors['incorrectCharA'])) echo "<p>{$errors['incorrectCharA']}</p>";
            ?>
        </div>

        <div>
            <label for="addressLine2">Address Line 2:</label>
            <input type="text" name="addressLine2"
            value="<?php echo htmlspecialchars($a2);?>">

            <!-- Display Address Errors -->
            <?php
            if (isset($errors['incorrectCharA'])) echo "<p>{$errors['incorrectCharA']}</p>";
            ?>
        </div>

        <div>
            <label for="city">City:</label>
            <input type="text" name="city"
            value="<?php echo htmlspecialchars($c); ?>">

            <!-- Display City Errors -->
            <?php
            if (isset($errors['incorrectCharC'])) echo "<p>{$errors['incorrectCharC']}</p>";
            ?>
        </div>

        <div>
            <label for="telephone">Telephone:</label>
            <input type="tel" name="telephone" placeholder="Eg. 019296673"
            value="<?php echo htmlspecialchars($t); ?>">

            <!-- Display Telehone Errors -->
            <?php
            if (isset($errors['errorT'])) echo "<p>{$errors['errorT']}</p>";
            ?>
        </div>

        <div>
            <label for="mobile">Mobile:</label>
            <input type="tel" name="mobile" placeholder="Eg. 0873647689"
            value="<?php echo htmlspecialchars($m); ?>" required>

            <!-- Display Mobile Phone Errors -->
            <?php
            if (isset($errors['errorM'])) echo "<p>{$errors['errorM']}</p>";
            ?>
        </div>

        <!-- Display empty error -->
        <?php
        if (isset($errors['empty'])) echo "<p>{$errors['empty']}</p>";
        ?>

        <div>
            <input type="submit" value="Sign Up">
        </div>
    </form>

        
        
</body>
</html>