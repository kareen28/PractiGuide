<?php
include 'nav.php';
session_start();

// Check username from session
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>האתר שלי</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78Hz0+Bj5HpqVfgrJ8X51t2V6oiz1QPkvc6Bu5qEZYw5uR90KsEyVC4" crossorigin="anonymous">
    <style>
/* Reset default margin and padding */
* {
  margin: 0;
  padding: 0;
}

/* General styles */
body {
  font-family: Arial, sans-serif;
  background-color: #f8f9fa;
  margin: 0;
  padding: 0;
}


/* Additional Info Section */
.additional-info {
  background-color: #f0f0f0;
  padding: 20px;
  text-align: center;
}

.additional-info h2 {
  font-size: 24px;
  margin-bottom: 10px;
}

.additional-info p {
  font-size: 16px;
}

/* Circle Image Container */
.circle-container {
  width: 200px;
  height: 200px;
  border-radius: 50%;
  overflow: hidden;
  float: left;
  margin-right: 20px;
}

.circle-container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Main Content */
.main-content {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  text-align: right;
}

.container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.text {
  width: 60%;
  text-align: right;
  direction: rtl;
  padding: 20px;
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
}

/* Form and Buttons */
textarea, input[type="submit"] {
  width: 100%;
  margin-bottom: 10px;
  padding: 12px;
  box-sizing: border-box;
  border: 1px solid #ced4da;
  border-radius: 5px;
  font-size: 16px;
}

input[type="submit"] {
  background-color: #4CAF50;
  color: #fff;
  border: none;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

input[type="submit"]:hover {
  background-color: #00e64d;
}

.request-lesson-button {
  background-color: #5bc0de;
  color: #fff;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
}

.request-lesson-button:hover {
  background-color: #46b8da;
}

/* Replies Section */
.replies {
  margin-top: 20px;
  border-top: 1px solid #ddd;
  padding-top: 20px;
}

.reply {
  background-color: #f9f9f9;
  border: 1px solid #ddd;
  border-radius: 5px;
  padding: 10px;
  margin-top: 10px;
}

.reply p {
  margin: 0;
}

.reply small {
  display: block;
  color: #777;
  margin-top: 5px;
}

/* Sidebar */
.sidebar {
  width: 200px;
  background-color: #f5f5f5;
  padding-top: 10px;
  position: fixed;
  top: 0;
  left: 0;
  height: 100%;
  overflow-x: hidden;
  z-index: 100;
  border-right: 1px solid #ddd;
  
}

.sidebar img {
  display: block;
  margin: 0 auto;
  width: 80%;
  padding-bottom: 20px;
}

.sidebar a {
  padding: 15px 20px;
  text-decoration: none;
  font-size: 18px;
  color: #333;
  display: block;
}

.sidebar a:hover {
  background-color: #ddd;
}

/* Search Form */
.search-form {
  text-align: right;
  margin-bottom: 10px;
}

.search-form input[type="text"] {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  margin-left: 5px;
  width: 200px;
}

.search-form input[type="submit"] {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 8px 12px;
  border-radius: 4px;
  cursor: pointer;
  width: 100px;
}

.search-form input[type="submit"]:hover {
  background-color: #0056b3;
}

/* Post Alignment */
.container {
  display: flex;
  justify-content: center;
  max-width: 1000px;
  margin: 20px auto;
  background-color: #fff;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  flex-wrap: wrap;
}

.left-section, .right-section {
  width: 48%;
}

h1 {
  font-size: 20px;
  margin-bottom: 20px;
}

label {
  display: block;
  margin-bottom: 5px;
}

input[type="text"], textarea, select {
  width: calc(100% - 16px);
  padding: 8px;
  margin-bottom: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

input[type="file"] {
  padding: 5px;
  margin-bottom: 10px;
}

.message {
  margin-top: 15px;
  font-size: 14px;
  color: green;
}

.ads {
  margin-top: 20px;
}

.ad {
  border-bottom: 1px solid #ccc;
  padding: 10px 0;
}

.ad h2 {
  margin: 0;
  font-size: 16px;
}

.ad p {
  margin: 5px 0;
}

.ad small {
  color: #666;
}

/* Left-aligned text */
.left-aligned {
  direction: ltr;
  text-align:left;
}
     </style>
</head>

<body>

 <div class="search-form">
    <form method="get" action="">
        <input type="text" name="search" placeholder="Search ads...">
        <input type="submit" value="Search">
    </form>
</div>

<?php
// Establish database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mydatabase";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create table if not exists
$sql_create_table = "CREATE TABLE IF NOT EXISTS replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad_id INT NOT NULL,
    user_id INT NOT NULL,
    user_type ENUM('interested', 'student') NOT NULL,
    reply_message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ad_id) REFERENCES ads(id),
    FOREIGN KEY (user_id) REFERENCES Users(id)
)";

if ($conn->query($sql_create_table) === TRUE) {
    echo "Table 'replies' exists.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Receive and sanitize the form data
    $ad_id = isset($_POST['ad_id']) ? intval($_POST['ad_id']) : 0;
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $user_type = isset($_POST['user_type']) ? $conn->real_escape_string($_POST['user_type']) : '';
    $reply_message = isset($_POST['reply_message']) ? $conn->real_escape_string($_POST['reply_message']) : '';

    // Prepare the SQL statement
    $sql = "INSERT INTO replies (ad_id, user_id, user_type, reply_message) VALUES (?, ?, ?, ?)";

    // Bind parameters and execute
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("iiss", $ad_id, $user_id, $user_type, $reply_message);
        if ($stmt->execute()) {
            echo "Reply saved successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>

<div class="container">
    <div class="text">
	<div class="left-aligned">

<?php
// Define the database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mydatabase";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define $search where it should be used
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Main query to fetch ads
$sql = "SELECT ads.*, COALESCE(SUM(lr.rating), 0) AS total_rating
        FROM ads
        LEFT JOIN lesson_requests lr ON ads.id = lr.ad_id";

// Add search filter if present
if (!empty($search)) {
    $sql .= " WHERE ads.title LIKE '%$search%' OR ads.description LIKE '%$search%' OR ads.student_date LIKE '%$search%' OR ads.created_at LIKE '%$search%'";
}

$sql .= " GROUP BY ads.id";
$result = $conn->query($sql);

if ($result === false) {
    die("Error: " . $sql . "<br>" . $conn->error);
}

$ads = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ads[] = $row;
    }
}

// מיון המערך לפי דירוג גבוה לנמוך בעזרת usort
usort($ads, function($a, $b) {
    return $b['total_rating'] - $a['total_rating'];
});

foreach ($ads as $ad) {
    echo '<div class="ad">';
    echo '<h2>' . htmlspecialchars($ad['title']) . '</h2>';
    echo '<p>' . htmlspecialchars($ad['description']) . '</p>';

    $user_id = $ad['user_id'];
    $sql_user = "SELECT username FROM Users WHERE id = $user_id";
    $result_user = $conn->query($sql_user);

    if ($result_user && $result_user->num_rows > 0) {
        $user = $result_user->fetch_assoc();
        echo '<p><strong>Username:</strong> ' . htmlspecialchars($user['username']) . '</p>';
    } else {
        echo '<p><strong>Username:</strong> Not found</p>';
    }

    if ($ad['user_type'] === 'employer') {
        echo '<p><strong>Employer Name:</strong> ' . htmlspecialchars($ad['employer_name']) . '</p>';
        if (!empty($ad['employer_certificate'])) {
            echo '<p><strong>Employer Certificate:</strong> <a href="' . htmlspecialchars($ad['employer_certificate']) . '" target="_blank">View</a></p>';
        }
    } elseif ($ad['user_type'] === 'student') {
        echo '<p><strong>Offer Private Lessons:</strong> ' . ($ad['offer_lessons'] === 'yes' ? 'Yes' : 'No') . '</p>';
        if ($ad['offer_lessons'] === 'yes' && !empty($ad['lesson_subject'])) {
            echo '<p><strong>Lesson Subject:</strong> ' . htmlspecialchars($ad['lesson_subject']) . '</p>';
            echo '<p><strong>Available Hours:</strong> ' . htmlspecialchars($ad['available_hours']) . '</p>';
            echo '<p><strong>Student Date:</strong> ' . htmlspecialchars($ad['student_date']) . '</p>';
            echo '<form method="post" action="lesson.php">';
            echo '<input type="hidden" name="ad_id" value="' . $ad['id'] . '">';
            echo '<input type="submit" value="בקשת שיעור" class="request-lesson-button">';
            echo '</form>';
        }
    }
    echo '<small>Posted on: ' . htmlspecialchars($ad['created_at']) . '</small>';

    echo '<form method="post" action="">';
    echo '<input type="hidden" name="ad_id" value="' . $ad['id'] . '">';
    echo '<input type="hidden" name="user_id" value="' . $user_id . '">';
    echo '<input type="hidden" name="user_type" value="' . $ad['user_type'] . '">';
    echo '<textarea name="reply_message" placeholder="Write your reply here..." required></textarea>';
    echo '<input type="submit" value="Reply">';
    echo '</form>';

    $ad_id = $ad['id'];
    $sql_replies = "
        SELECT replies.*, Users.username
        FROM replies
        JOIN Users ON replies.user_id = Users.id
        WHERE replies.ad_id = $ad_id
    ";
    $result_replies = $conn->query($sql_replies);

    if ($result_replies !== false && $result_replies->num_rows > 0) {
        echo '<div class="replies">';
        while ($reply = $result_replies->fetch_assoc()) {
            echo '<div class="reply">';
            echo '<p><strong>' . htmlspecialchars($reply['username']) . ':</strong> ' . htmlspecialchars($reply['reply_message']) . '</p>';
            echo '<p><small>' . htmlspecialchars($reply['created_at']) . '</small></p>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p>No replies found.</p>';
    }

    echo '</div>';
}

$conn->close();

?>
</div>


    </div>
</div>
<div class="sidebar">

<a href="mainstudentwork.php">
		<img src="s1.png" alt="לוגו" style="width: 120px; height: 80px; margin-left:20px;">
		</a>
       <a href="studentswork.php">upload post</a>
       <a href="massege.php"> lesson_requests</a>
	   <a href="allmasssge.php"> massege</a>


  </div>

 
     

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybBogGzOg+t8KA4pHs0UB6B0A1F2YxPfGfadEywG8W/adScBZ" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93q5B2ZKNtj0OeMv8KEFgP1pZcG8usvFLRPZ5lgSRJ5L06L/MB4Ik8wGhxl5t3" crossorigin="anonymous"></script>
</body>
</html>