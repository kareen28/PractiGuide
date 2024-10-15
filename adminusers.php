<?php
include 'sidebadmin.php';
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "mydatabase");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// יצירת טבלת המשתמשים המורחקים אם היא לא קיימת
$tblBannedEmails = "CREATE TABLE IF NOT EXISTS tblBannedEmails(
    id INT(9) PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(50) NOT NULL UNIQUE
)";
if ($conn->query($tblBannedEmails) !== TRUE) {
    echo "Error creating table: " . $conn->error;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["delete_and_ban"])) {
        $deleteId = $_POST["delete_and_ban"];
        $selectEmailQuery = "SELECT Email FROM Users WHERE id = '$deleteId'";
        $result = $conn->query($selectEmailQuery);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $bannedEmail = $row['Email'];
            $insertBanQuery = "INSERT INTO tblBannedEmails (email) VALUES ('$bannedEmail')";
            if ($conn->query($insertBanQuery) === TRUE) {
                echo "User banned successfully.";
            } else {
                echo "Error banning user: " . $conn->error;
            }
        }
        $deleteQuery = "DELETE FROM Users WHERE id = '$deleteId'";
        if ($conn->query($deleteQuery) === TRUE) {
            echo "";
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    }

    if (isset($_POST["unban_email"])) {
        $unbanEmail = $_POST["unban_email"];
        $unbanQuery = "DELETE FROM tblBannedEmails WHERE email = '$unbanEmail'";
        if ($conn->query($unbanQuery) === TRUE) {
            echo "User unbanned successfully.";
        } else {
            echo "Error unbanning user: " . $conn->error;
        }
    }
}

// עיבוד החיפוש
$searchTerm = '';
if (isset($_POST['search']) && !empty($_POST['search_term'])) {
    $searchTerm = trim($conn->real_escape_string($_POST['search_term']));
    $searchTerm = strtolower($searchTerm);
    $selectQuery = "SELECT * FROM Users WHERE LOWER(username) LIKE '%$searchTerm%' OR LOWER(Email) LIKE '%$searchTerm%'";
} else {
    $selectQuery = "SELECT * FROM Users";
}

$result = $conn->query($selectQuery);

$bannedUsersQuery = "SELECT * FROM tblBannedEmails";
$bannedResult = $conn->query($bannedUsersQuery);
?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management</title>
    <style>
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Arial', sans-serif;
    background-color: #f3e5f5; /* Light Purple */
    color: #4a148c; /* Dark Purple */
}

header {
    background-color: #6a1b9a; /* Medium Purple */
    color: #fff;
    padding: 20px;
    text-align: center;
}

h1 {
    margin: 0;
}

* {
    box-sizing: border-box;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}



/* Additional styles for the Users Management page */
.table-container {
    padding: 20px;
}

table {
    width: 80%; /* Reduced width */
    border-collapse: collapse;
    margin: 0 auto; /* Center align the table */
    table-layout: fixed; /* Fixed table layout to prevent resizing */
    font-size: 85%; /* Smaller font size */
    margin-right: 20px; 
}

th, td {
    border: 1px solid #7b1fa2; /* Dark Purple */
    padding: 6px; /* Reduced padding */
    text-align: left;
    white-space: nowrap; /* Prevent text from wrapping */
    overflow: hidden; /* Hide overflow text */
    text-overflow: ellipsis; /* Add ellipsis for overflowing text */
    height: 20px; /* Reduced height */
}

th {
    background-color: #ba68c8; /* Light Purple */
    color: #ffffff; /* White text for better contrast */
}

td {
    background-color: #f3e5f5; /* Light Purple background */
}

button {
    background-color: #8e24aa; /* Dark Purple */
    color: #fff;
    border: none;
    padding: 10px 15px; /* Increased padding */
    cursor: pointer;
    width: 120px; /* Increased width */
    height: 40px; /* Increased height */
    font-size: 100%; /* Standard font size */
    border-radius: 5px; /* Rounded corners */
}

button:hover {
    background-color: #7b1fa2; /* Darker purple on hover */
}

input[type="text"], input[type="email"], input[type="password"] {
    width: 100%;
    padding: 5px; /* Reduced padding */
    margin: 5px 0;
    box-sizing: border-box;
    border: 1px solid #7b1fa2; /* Dark Purple */
    border-radius: 3px;
    font-size: 85%; /* Smaller font size */
}

/* Prevent element shifting */
* {
    transition: none !important;
}

    </style>
</head>
<body>
    <div class="main-content">
        <div class="table-wrapper">
            <center>
                <h2>ניהול משתמשים</h2>
                <form method="POST" style="width:300px">
                    <input type="text" name="search_term" placeholder="חפש לפי שם משתמש או מייל" value="<?php echo htmlspecialchars($searchTerm); ?>">
                    <center>
                        <button type="submit" name="search" style="width:100px">חפש</button>
                    </center>
                </form>
            </center>
            <br>
            <br>
            <form method="post" action="">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>שם משתמש</th>
                                <th>מייל</th>
                                <th>חסימה</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['Email']); ?></td>
                                <td>
                                    <form method="post" action="">
                                        <button type="submit" name="delete_and_ban" value="<?php echo $row['id']; ?>">לחסום משתמש</button>
                                    </form>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
        <div class="table-wrapper">
            <center><h2>ניהול משתמשים חסומים</h2></center>
            <table>
                <thead>
                    <tr>
                        <th>מייל</th>
                        <th>ביטול חסימה</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $bannedResult->fetch_assoc()) { ?>
                    <tr>
                        <form method="post" action="">
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <input type="hidden" name="unban_email" value="<?php echo htmlspecialchars($row['email']); ?>">
                                <button type="submit">בטל חסימה</button>
                            </td>
                        </form>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
