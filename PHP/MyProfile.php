<?php
session_start();

require_once "PHPConntectSQL.php";

//If User isn't Logged in, Redirect to Login Page
if (!isset($_SESSION['loggedIn']) || !$_SESSION['loggedIn']) {
    header("Location: Login.php");
    exit;
}

//Set Session Variable
$username = $_SESSION['username'] ?? '';

// Function to Clean Data
function cleanData($data) {
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}


$title    = cleanData($_GET['title']   ?? '');
$author   = cleanData($_GET['author']  ?? '');
$category = $_GET['category'] ?? 'allCategories';

// Pagination Variables (only used if a search happens)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;//If the URL includes page, use it as the page number. Otherwise, set page to 1.
if ($page < 1) { $page = 1; }
$limit = 5;
$startFrom = ($page - 1) * $limit;

// Flag to check if the user performed a search
$searchPerformed = ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET));


// If a search was performed, run the search query
if ($searchPerformed) {
    $sql = "SELECT b.*, c.CategoryDescription
            FROM books b
            JOIN categories c ON b.CategoryID = c.CategoryID
            WHERE 1";//Append AND easily

    $params = [];//Array to Store Variables to be Injected
    $types  = "";//String to store data types

    if (!empty($title)) {
        $sql .= " AND b.BookTitle LIKE ?";
        $params[] = "%$title%";
        $types    .= "s";
    }
    if (!empty($author)) {
        $sql .= " AND b.Author LIKE ?";
        $params[] = "%$author%";
        $types    .= "s";
    }
    if (!empty($category) && $category !== "allCategories") {
        $sql .= " AND c.CategoryDescription = ?";
        $params[] = $category;
        $types    .= "s";
    }

    $sql .= " LIMIT ?, ?";//Append limit command to end of og query
    $params[] = $startFrom;//add startFrom and limit to params
    $params[] = $limit;
    $types .= "ii";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $results = $stmt->get_result();

    // Count total for pagination
    $countSql = "SELECT COUNT(*) AS total
                 FROM books b
                 JOIN categories c ON b.CategoryID = c.CategoryID
                 WHERE 1";
    $countParams = [];
    $countTypes  = "";

    if (!empty($title)) {
        $countSql .= " AND b.BookTitle LIKE ?";
        $countParams[] = "%$title%";
        $countTypes    .= "s";
    }
    if (!empty($author)) {
        $countSql .= " AND b.Author LIKE ?";
        $countParams[] = "%$author%";
        $countTypes    .= "s";
    }
    if (!empty($category) && $category !== "allCategories") {
        $countSql .= " AND c.CategoryDescription = ?";
        $countParams[] = $category;
        $countTypes    .= "s";
    }

    $countStmt = $conn->prepare($countSql);

    if (!empty($countParams)) {
        $countStmt->bind_param($countTypes, ...$countParams);
    }

    $countStmt->execute();
    $countResult  = $countStmt->get_result();//Get result of the count query
    $totalRecords = (int)$countResult->fetch_assoc()['total'];//Get the result of the count query as an associative array("total" -> result)
    $totalPages   = max(1, (int)ceil($totalRecords / $limit));//ceil() - rounds up to nearest whole number
}

// Always load reserved books (Doesnt depend on user makking a search)
$reserveSql = "SELECT b.ISBN, b.BookTitle, b.Author, r.ReservedDate, c.CategoryDescription
               FROM reservations r
               JOIN books b ON r.ISBN = b.ISBN
               JOIN categories c ON b.CategoryID = c.CategoryID
               WHERE r.Username = ?";

$reserveStmt = $conn->prepare($reserveSql);
$reserveStmt->bind_param("s", $username);
$reserveStmt->execute();
$reserveResult = $reserveStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<!-- Boiler Plate -->

<head>
    <meta charset="UTF-8" />
    <title>Irish Libraries</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="description" content="Library site to search, reserve, and view all the books a user has reserved" />
    <link rel="stylesheet" href="../CSS/MyProfile.css">
</head>

<body>

    <!-- Nav Bar-->
    <div class="container">

        <div class="navbar">

            <div class="logo">
                <img src="../Media/Logo.png" alt="Brand Logo">
            </div>

            <p class="navTitle">Irish Libraries</p>

            <ul>
                <li><a href="../Index.php"> Home</a></li>
                <li><a href="Logout.php">Log Out</a></li>
            </ul>
        </div>
    </div>

    <div class="pageContent">

        <h3>My Profile</h3>
        <p>Welcome, <?php echo htmlspecialchars($username);?></p><!-- Welcome Message Displays Username-->

        <div class="searchBox">
            <form class="searchForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="get">

                <div class="field">
                    <label for="title">Title</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($title) ?>"><!-- = is shorthand for echo-->
                </div><!-- End field -->

                <div class="field">
                    <label for="author">Author</label>
                    <input type="text" name="author" value="<?= htmlspecialchars($author) ?>">
                </div><!-- End field -->

                <div class="field">
                    <label for="category">Category</label>
                    <select name="category">
                        <option value="allCategories" <?= ($category === "allCategories") ? 'selected' : '' ?>>All Categories</option><!-- Have allCategories as default when page first loads -->

                        <!-- Retrieve Values from Category Table -->
                        <?php
                        $sql = "SELECT CategoryDescription FROM categories";
                        $result = $conn->query($sql);

                        while ($row = $result->fetch_assoc()) {
                            $cat = htmlspecialchars($row['CategoryDescription']);
                            $selected = ($category === $row['CategoryDescription']) ? 'selected' : '';//Is chosen category ($category) = this row's category?
                            echo "<option value=\"$cat\" $selected>$cat</option>";
                        }

                        $conn->close();
                        ?>

                    </select>
                </div><!-- End field -->
                
                <div class="searchActions">
                    <button type="submit" class="btn">Search</button>
                </div>
                

            </form><!-- End Form -->

            <div class="searchActions">
                <a href="MyProfile.php" class="btn">Reset</a>
            </div>
        </div><!-- End searchBox -->

        <!-- Display Search Results Below Form -->
        <?php if (isset($results) && $results->num_rows > 0): ?>
        <div class="searchResults">
            <h4>Search Results (<?= $totalRecords ?> books found)</h4>
                <ul>    
                    <?php while ($row = $results->fetch_assoc()): ?>
                        <li>
                            <strong><?= htmlspecialchars($row['BookTitle']) ?></strong><br>
                            by <?= htmlspecialchars($row['Author']) ?> • <?= htmlspecialchars($row['CategoryDescription']) ?><br>
                            ISBN: <?= htmlspecialchars($row['ISBN']) ?><br>
                            <?php if ($row['Reserved']): ?>
                                <span class="unavailable">Already Reserved</span>
                            <?php else: ?>
                                <form method="post" action="ReserveBook.php">
                                    <input type="hidden" name="isbn" value="<?= htmlspecialchars($row['ISBN']) ?>">
                                    <button type="submit">Reserve</button>
                                </form>
                            <?php endif; ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
        </div>
        <?php
        // Build Links for Pagination
        echo '<ul class="pagination">';
        for ($i = 1; $i <= $totalPages; $i++) {
            if($i == $page){
                $active = "class='active'";
            }
            else{
                $active = "";
            }

            echo "<li $active><a href='?page=$i&title=" . urlencode($title) .//urlencode() - Esnures strings are safe to insert into a URL
            "&author=" . urlencode($author) .
            "&category=" . urlencode($category) . "'>$i</a></li>";
        }
        echo '</ul>';
        ?>
        <?php elseif ($searchPerformed): ?><!-- First if statement = false, but search was still performed so this runs-->
            <p>No books found matching your criteria.</p>
        <?php endif; ?>

        <!-- View Reserved Books -->
         <h4>My Reserved Books</h4>
         <ul>
            <?php while($row = $reserveResult->fetch_assoc()):?>
                <li>
                    <strong><?= htmlspecialchars($row['BookTitle']) ?></strong><br>
                    by <?= htmlspecialchars($row['Author']) ?> • <?= htmlspecialchars($row['CategoryDescription']) ?><br>
                    ISBN: <?= htmlspecialchars($row['ISBN']) ?><br>
                    
                    <form action="CancelReservation.php" method="post">
                        <input type="hidden" name="isbn" value="<?php echo $row['ISBN'];?>">
                        <button type="submit" class="cancelBtn">Cancel</button>
                </li>
            <?php endwhile; ?>

    </div><!-- End pageContent -->
    <footer>©2025 Irish Libraries</footer>
    
</body>
</html>
                                