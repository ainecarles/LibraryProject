<?php
session_start();

require_once "PHPConntectSQL.php";

 if(!isset($_SESSION['loggedIn']) || !$_SESSION['loggedIn']){//if the Session loggedIn variable does not exist or its value is false then
    header("Location: Login.php");
    exit;   
}

$username = $_SESSION['username'] ?? '';

//Function for cleaning input
function cleanData($data){
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}//End cleanData

require_once "PHPConntectSQL.php";

if($_SERVER['REQUEST_METHOD'] === 'GET'){

    $title = cleanData($_GET['title'] ?? ''); 
    $author = cleanData($_GET['author'] ?? '');
    $category = $_GET['category'] ?? "allCategories";

    //Pagination

    //Look for a GET variable page if not found default is 1.
    if(isset($_GET["page"])){
        $page = $_GET["page"];
    }
    else{
        $page = 1;
    }

    if($page < 1){
        $page = 1;
    }

    $limit = 5;//Amount of Rows Displayed
    $startFrom = ($page - 1) * $limit;


    $sql = "SELECT b.*, c.CategoryDescription
            FROM books b
            JOIN categories c ON b.CategoryID = c.CategoryID 
            WHERE 1";//Can Append AND easily

    $params = [];//Holds values to replace ? placeholders
    $types = "";//String to describe data type of parameters

    //Title Search
    if(!empty($title)){

        $sql .= " AND b.BookTitle LIKE ?";
        $params[] = "%$title%";
        $types .= "s";
    }

    //Author Search
    if(!empty($author)){
        $sql .= " AND b.Author LIKE ?";
        $params[] = "%$author%";
        $types .= "s";
    }

    //Category Search
    if (!empty($category) && $category !== "allCategories") {
        $sql .= " AND c.CategoryDescription = ?";
        $params[] = $category;
        $types .= "s";
    }

    $sql .= " LIMIT ?, ?";//Append LIMIT at the end
    $params[] = $startFrom;//Add startFrom and limit to params
    $params[] = $limit;
    $types  .= "ii";


    $stmt = $conn->prepare($sql);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);//... - Splat operator (Turns array into separate arguments)
    }

    $stmt->execute();
    $results = $stmt->get_result();

    //Query to Count Total Rows
    $countSql = "SELECT COUNT(*) AS total
                FROM books b
                JOIN categories c ON b.categoryID = c.CategoryID
                WHERE 1";

    $countParams = [];
    $countTypes = "";

    if (!empty($title)) {
        $countSql .= " AND b.BookTitle LIKE ?";
        $countParams[] = "%$title%";
       
        $countTypes .= "s";
    }
    if (!empty($author)) {
        $countSql .= " AND b.Author LIKE ?";
        $countParams[] = "%$author%";
        $countTypes .= "s";
    }

    if (!empty($category) && $category !== "allCategories") {
        $countSql .= " AND c.CategoryDescription = ?";
        $countParams[] = $category;
        $countTypes .= "s";
    }

    $countStmt = $conn->prepare($countSql);

    if (!empty($countParams)) {
        $countStmt->bind_param($countTypes, ...$countParams);
    }

    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $totalRecords = $countResult->fetch_assoc()['total'];

    $totalPages = ceil($totalRecords / $limit);//ceil rounds up to nearest whole number

    //View Reserved Books
    $reserveSql = "SELECT b.ISBN, b.BookTitle, b.Author, r.ReservedDate, c.CategoryDescription
                    FROM reservations r
                    JOIN books b ON r.ISBN = b.ISBN 
                    JOIN categories c ON b.categoryID = c.categoryID
                    WHERE r.Username = ?";
    
    $reserveStmt = $conn->prepare($reserveSql);
    $reserveStmt->bind_param("s", $username);
    $reserveStmt->execute();
    $reserveResult = $reserveStmt->get_result();
    
}
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
        <p>Welcome, <?php echo htmlspecialchars($username);?></p>

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
                        <option value="allCategories" <?= ($category === "allCategories") ? 'selected' : '' ?>>All Categories</option>

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

                <button type="submit">Search</button>
            </form><!-- End Form -->
        </div><!-- End searchBox -->

        <!-- Display Search Results Below Form -->
        <?php if (isset($results) && $results->num_rows > 0): ?>
        <div class="searchResults">
            <h4>Search Results (<?= $results->num_rows ?> books found)</h4>
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
                    <?php endwhile; ?>s
                    <?php
                    // Build Limks for Pagination
                    echo '<ul class="pagination">';
                    for ($i = 1; $i <= $totalPages; $i++) {
                        if($i == $page){
                            $active = "class='active'";
                        }
                        else{
                            $active = "";
                        }

                        $active = ($i == $page) ? "class='active'" : "";
                        echo "<li $active><a href='?page=$i&title=" . urlencode($title) .//urlencode() - Esnures strings are safe to insert into a URL
                            "&author=" . urlencode($author) .
                            "&category=" . urlencode($category) . "'>$i</a></li>";
                    }
                    echo '</ul>';
                    ?>
                </ul>
        </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'GET'): ?><!-- First if statement = false, but form was still submitted with GET so this runs-->
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
                        <button type="submit">Cancel</button>
                </li>
            <?php endwhile; ?>
            


        
    </div><!-- End pageContent -->
</body>
</html>
                                