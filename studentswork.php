<?php
include 'nav.php';
// פרמטרי חיבור למסד הנתונים
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mydatabase";

session_start(); // ודא שזה מופיע בתחילת הקובץ

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function userExists($conn, $username) {
    $stmt = $conn->prepare("SELECT id FROM Users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

// בדיקת קיום משתמש
if (!isset($_SESSION['username'])) {
    echo "User not logged in.";
    header("Location: login.php");
    exit();
}

if (!userExists($conn, $_SESSION['username'])) {
    echo "User does not exist in database.";
    header("Location: login.php");
    exit();
}

$user_username = $_SESSION['username'];
$stmt_user = $conn->prepare("SELECT id FROM Users WHERE username = ?");
$stmt_user->bind_param("s", $user_username);
$stmt_user->execute();
$stmt_user->bind_result($user_id);
$stmt_user->fetch();
$stmt_user->close();

// יצירת טבלת ads אם אינה קיימת
$sql_create_table = "
CREATE TABLE IF NOT EXISTS ads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_type ENUM('employer', 'student', 'interested') NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    employer_name VARCHAR(255) DEFAULT NULL,
    employer_certificate VARCHAR(255) DEFAULT NULL,
    offer_lessons ENUM('yes', 'no') DEFAULT 'no',
    lesson_subject VARCHAR(255) DEFAULT NULL,
    available_hours VARCHAR(255) DEFAULT NULL,
    student_date DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   
    FOREIGN KEY (user_id) REFERENCES Users(id)
)";

if (!$conn->query($sql_create_table)) {
    die("Error creating table: " . $conn->error);
}

// טיפול בהוספת מודעה
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'share_ad') {
    $user_type = $_POST['user_type'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    $employer_name = null;
    $employer_certificate = null;
    $offer_lessons = 'no';
    $lesson_subject = null;
    $available_hours = null;
    $student_date = null;

    if ($user_type === 'employer') {
        $employer_name = $_POST['employer_name'];

        // בדיקה שהועלה קובץ תמונה
        if (empty($_FILES['employer_certificate']['name'])) {
            $message = "Please upload a certificate.";
        } else {
            $uploadDir = 'uploads/';
            $uploadFile = $uploadDir . basename($_FILES['employer_certificate']['name']);

            if (move_uploaded_file($_FILES['employer_certificate']['tmp_name'], $uploadFile)) {
                $employer_certificate = $uploadFile;
            } else {
                die("Error uploading file");
            }
        }
    }  elseif ($user_type === 'student') {
        $offer_lessons = $_POST['offer_lessons'];

        if ($offer_lessons === 'yes' && !empty($_POST['lesson_subject'])) {
            $lesson_subject = $_POST['lesson_subject'];
        }

        if ($offer_lessons === 'yes' && !empty($_POST['available_hours'])) {
            $available_hours = $_POST['available_hours'];
        }

        if ($offer_lessons === 'yes' && !empty($_POST['student_date'])) {
            $student_date = $_POST['student_date'];
        }
    }

    $sql_insert_ad = "INSERT INTO ads (user_id, user_type, title, description, employer_name, employer_certificate, offer_lessons, lesson_subject, available_hours, student_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert_ad = $conn->prepare($sql_insert_ad);
    $stmt_insert_ad->bind_param("isssssssss", $user_id, $user_type, $title, $description, $employer_name, $employer_certificate, $offer_lessons, $lesson_subject, $available_hours, $student_date);

    if ($stmt_insert_ad->execute()) {
        $message = "Ad shared successfully!";
    } else {
        $message = "Error sharing ad: " . $conn->error;
    }

    $stmt_insert_ad->close();
}

// טיפול בעדכון מודעה או מחיקה
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'update_post') {
        $post_id = $_POST['post_id'];
        $new_title = $_POST['new_title'];
        $new_description = $_POST['new_description'];
        $new_available_hours = isset($_POST['new_available_hours']) ? $_POST['new_available_hours'] : null;
        $new_student_date = isset($_POST['new_student_date']) ? $_POST['new_student_date'] : null;
        $sql_update_post = "UPDATE ads SET title = ?, description = ?, available_hours = ?, student_date = ? WHERE id = ? AND user_id = ?";
        $stmt_update_post = $conn->prepare($sql_update_post);
        $stmt_update_post->bind_param("ssssii", $new_title, $new_description, $new_available_hours, $new_student_date, $post_id, $user_id);

        if ($stmt_update_post->execute()) {
            $message = "Post updated successfully!";
        } else {
            $message = "Error updating post: " . $conn->error;
        }

        $stmt_update_post->close();
    } elseif ($action === 'delete_post') {
        $post_id = $_POST['post_id'];

        $sql_delete_post = "DELETE FROM ads WHERE id = ? AND user_id = ?";
        $stmt_delete_post = $conn->prepare($sql_delete_post);
        $stmt_delete_post->bind_param("ii", $post_id, $user_id);

        if ($stmt_delete_post->execute()) {
            $message = "Post deleted successfully!";
        } else {
            $message = "Error deleting post: " . $conn->error;
        }

        $stmt_delete_post->close();
    }
}
// Function to fetch ratings for each lesson request


// שליפת מודעות של המשתמש
$sql_select_ads = "SELECT * FROM ads WHERE user_id = ?";
$stmt_select_ads = $conn->prepare($sql_select_ads);
$stmt_select_ads->bind_param("i", $user_id);
$stmt_select_ads->execute();
$result = $stmt_select_ads->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
 <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f0f0;
        margin: 0;
        padding: 0;
    }

    h1 {
        color: #333;
        text-align: center;
    }

    form {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        max-width: 600px;
        margin: auto;
    }

    label {
        display: block;
        margin-bottom: 8px;
        color: #555;
    }

    input[type=text], textarea, select {
        width: 100%;
        padding: 8px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 14px;
    }

    input[type=file] {
        margin-top: 5px;
    }

    select {
        width: 100%;
        padding: 8px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 14px;
    }

    textarea {
        resize: vertical;
        min-height: 100px;
    }

    input[type=submit] {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    input[type=submit]:hover {
        background-color: #45a049;
    }

    .ad {
        background-color: #fff;
        padding: 9px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .ad h2 {
        margin-top: 0;
        color: #333;
    }

    .ad p {
        margin: 5px 0;
        color: #666;
    }

    .ad small {
        color: #999;
        font-size: 12px;
    }

    .ad form {
        margin-top: 10px;
    }

    .ad form input[type=submit] {
        background-color: #f44336;
        color: white;
        padding: 5px 10px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        font-size: 12px;
    }

    .ad form input[type=submit]:hover {
        background-color: #e53935;
    }

    .sidebar {
        width: 200px; /* Width of the sidebar */
        background-color: #f5f5f5; /* Light Gray */
        padding-top: 20px;
        position: absolute;
        top: 0;
        left: 0;
        height: 50%; /* Adjust this value to control the height */
        overflow-x: hidden;
        z-index: 100;
        border-right: 1px solid #ddd;
    }

    .sidebar img {
        display: block;
        margin: 0 auto;
        width: 80%; /* Adjust the logo width as needed */
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

    .main-content {
        padding: 20px;
        /* No margin-left needed, as sidebar won't affect the content flow */
}
</style>

    <meta charset="UTF-8">
    <title>Students Work</title>
    <script>
        // פונקציה לבדיקת המשתמש והצגת תחזוקת השדות
        function toggleFields() {
            var userType = document.getElementById('user_type').value;
            var employerFields = document.getElementById('employer_fields');
            var studentFields = document.getElementById('student_fields');
            var lessonSubject = document.getElementById('lesson_subject');
            var availableHours = document.getElementById('available_hours');
            var studentDateField = document.getElementById('student_date_field');

            employerFields.style.display = (userType === 'employer') ? 'block' : 'none';
            studentFields.style.display = (userType === 'student') ? 'block' : 'none';

            if (userType === 'student') {
                toggleLessonSubject();
            } else {
                lessonSubject.style.display = 'none';
                availableHours.style.display = 'none';
                studentDateField.style.display = 'none';
            }
        }

        // פונקציה לבדיקת תחזוקת השדה על המשתמש
        function toggleLessonSubject() {
            var offerLessons = document.getElementById('offer_lessons').value;
            var lessonSubject = document.getElementById('lesson_subject');
            var availableHours = document.getElementById('available_hours');
            var studentDateField = document.getElementById('student_date_field');

            lessonSubject.style.display = (offerLessons === 'yes') ? 'block' : 'none';
            availableHours.style.display = (offerLessons === 'yes') ? 'block' : 'none';
            studentDateField.style.display = (offerLessons === 'yes') ? 'block' : 'none';
        }

        // הפעלת פונקצית toggleFields בעת טעינת הדף
        document.addEventListener('DOMContentLoaded', function() {
            toggleFields();
        });
    </script>
</head>
<body>
  <div class="sidebar">
<a href="mainstudentwork.php">
		<img src="s1.png" alt="לוגו" style="width: 120px; height: 80px; margin-left:20px;">
		</a>
       <a href="studentswork.php">upload post</a>
       <a href="massege.php"> lesson_requests</a>
	   <a href="allmasssge.php"> massege</a>


  </div>
            <h1>Share an Ad</h1>
            <?php if (isset($message)) { echo "<p class='message'>$message</p>"; } ?>
            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="share_ad">
                <label for="user_type">User Type:</label>
                <select id="user_type" name="user_type" onchange="toggleFields()" required>
                    <option value="">Select...</option>
                    <option value="employer">recruiter</option>
                    <option value="student">Student</option>
                    <option value="interested">Interested</option>
                </select>

                <div id="employer_fields" style="display: none;">
                    <label for="employer_name">recruiter Name:</label>
                    <input type="text" id="employer_name" name="employer_name">
                    
                    <label for="employer_certificate">recruiter Certificate:</label>
                    <input type="file" id="employer_certificate" name="employer_certificate" accept="image/*">
                </div>

                <div id="student_fields" style="display: none;">
    <label for="offer_lessons">Offer Private Lessons:</label>
    <select id="offer_lessons" name="offer_lessons" onchange="toggleLessonSubject()" required>
        <option value="no">No</option>
        <option value="yes">Yes</option>
    </select>

    <div id="lesson_subject" style="display: none;">
        <label for="lesson_subject_text">Lesson Subject Write lesson price in Description :</label>
        <input type="text" id="lesson_subject_text" name="lesson_subject">
    </div>

    <div id="available_hours" style="display: none;">
        <label for="available_hours_text">Available Hours:</label>
        <input type="text" id="available_hours_text" name="available_hours">
    </div>

    <!-- הוספת שדה התאריך עבור סטודנטים -->
    <div id="student_date_field" style="display: none;">
        <label for="student_date">Date:</label>
        <input type="date" id="student_date" name="student_date">
    </div>
</div>


                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
                
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>
                
                <input type="submit" value="Share Ad">
            </form>
        
    <h2>Your Ads</h2>
    <?php while($row = $result->fetch_assoc()) { ?>
        <div class="ad">
            <h2><?php echo htmlspecialchars($row['title']); ?></h2>
            <p><?php echo htmlspecialchars($row['description']); ?></p>
            <p><strong>User Type:</strong> <?php echo htmlspecialchars($row['user_type']); ?></p>
            <?php if ($row['user_type'] === 'student') { ?>
                <p><strong>Offer Private Lessons:</strong> <?php echo $row['offer_lessons'] === 'yes' ? 'Yes' : 'No'; ?></p>
                <?php if (!empty($row['lesson_subject'])) { ?>
                    <p><strong>Lesson Subject:</strong> <?php echo htmlspecialchars($row['lesson_subject']); ?></p>
                <?php } ?>
                <?php if (!empty($row['available_hours'])) { ?>
                    <p><strong>Available Hours:</strong> <?php echo htmlspecialchars($row['available_hours']); ?></p>
                <?php } ?>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($row['student_date']); ?></p>
            <?php } ?>
            <small>Posted on: <?php echo htmlspecialchars($row['created_at']); ?></small>

            <!-- Update post form -->
            <form method="post" action="">
                <input type="hidden" name="action" value="update_post">
                <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
                <label for="new_title">New Title:</label>
                <input type="text" id="new_title" name="new_title" required>
                <br>
                <label for="new_description">New Description:</label>
                <textarea id="new_description" name="new_description" rows="4" required></textarea>
                <br>

                <?php if ($row['user_type'] === 'student'): ?>
                    <label for="new_available_hours">New Available Hours:</label>
                    <input type="text" id="new_available_hours" name="new_available_hours">
                    <br>
                    <label for="new_student_date">New Student Date:</label>
                    <input type="date" id="new_student_date" name="new_student_date">
                    <br>
                <?php endif; ?>

                <input type="submit" value="Update Post">
            </form>

            <!-- Delete post form -->
            <form method="post" action="">
                <input type="hidden" name="action" value="delete_post">
                <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
                <br>
                <input type="submit" value="Delete Post">
            </form>
            <br>
        </div>
    <?php } ?>
</body>
</html>
<?php
$sql_delete_expired_posts = "DELETE FROM ads WHERE student_date < CURDATE()";
if ($conn->query($sql_delete_expired_posts) === TRUE) {
    echo "";
} else {
    echo "Error deleting expired posts: " . $conn->error;
}


$stmt_select_ads->close();
$conn->close();
?>