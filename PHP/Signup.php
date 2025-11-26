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
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

        <div>
            <label for="username">Username:</label>
            <input type="text" name="username" required>
        </div>

        <div>
            <label for="password">Password:</label>
            <input type="password" name="password" required>
        </div>

        <div>
            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" name="confirmPassword" required>
        </div>

        <div>
            <label for="firstName">First Name:</label>
            <input type="text" name="firstName" required>
        </div>

        <div>
            <label for="surname">Surname:</label>
            <input type="text" name="surname" required>
        </div>

        <div>
            <label for="addressLine1">Address Line 1:</label>
            <input type="text" name="addressLine1" required>
        </div>

        <div>
            <label for="addressLine2">Address Line 2:</label>
            <input type="text" name="addressLine2">
        </div>

        <div>
            <label for="city">City:</label>
            <input type="text" name="city">
        </div>

        <div>
            <label for="telephone">Telephone:</label>
            <input type="tel" name="telephone">
        </div>

        <div>
            <label for="mobile">Mobile:</label>
            <input type="tel" name="mobile">
        </div>

        <div>
            <input type="submit" value="Sign Up">
        </div>
    </form>

        
        
</body>
</html>