<?php
include 'nav.php';
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mydatabase";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// יצירת טבלת lesson_requests אם לא קיימת
$sql_create_table = "
CREATE TABLE IF NOT EXISTS lesson_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad_id INT NOT NULL,
    student_id INT NOT NULL,
    lesson_date DATE NOT NULL,
    lesson_time VARCHAR(50) NOT NULL,
    status ENUM('pending', 'approved', 'declined') DEFAULT 'pending',
    rating INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    feedback TEXT,
    FOREIGN KEY (ad_id) REFERENCES ads(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES Users(id) ON DELETE CASCADE
)";

if (!$conn->query($sql_create_table)) {
    die("Error creating table: " . $conn->error);
}

// בדיקת קיום משתמש מחובר
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// קבלת מזהה המשתמש המחובר
$stmt_user = $conn->prepare("SELECT id FROM Users WHERE username = ?");
$stmt_user->bind_param("s", $_SESSION['username']);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows == 0) {
    die("User not found.");
}

$user = $result_user->fetch_assoc();
$user_id = $user['id'];
$stmt_user->close();

// קבלת מזהה המודעה
if (isset($_POST['ad_id'])) {
    $ad_id = $_POST['ad_id'];
} else {
    die("Ad ID is missing.");
}

// קבלת פרטי המודעה מהמסד הנתונים
$stmt_ad = $conn->prepare("SELECT * FROM ads WHERE id = ?");
$stmt_ad->bind_param("i", $ad_id);
$stmt_ad->execute();
$result_ad = $stmt_ad->get_result();

if ($result_ad->num_rows == 0) {
    die("Ad not found.");
}

$ad = $result_ad->fetch_assoc();
$stmt_ad->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_lesson'])) {
    $lesson_date = $ad['student_date']; // קבל את התאריך מהמודעה
    $lesson_time = $_POST['lesson_time'];

    // Validate lesson date
    $current_date = date('Y-m-d'); // Get current date in SQL format
    if ($lesson_date < $current_date) {
        echo "Error: Lesson date cannot be in the past.";
        exit();
    }

    // הכנס את הבקשה לטבלה
    $stmt_request = $conn->prepare("INSERT INTO lesson_requests (ad_id, student_id, lesson_date, lesson_time) VALUES (?, ?, ?, ?)");
    $stmt_request->bind_param("iiss", $ad_id, $user_id, $lesson_date, $lesson_time);

    if ($stmt_request->execute()) {
        echo "<center>Lesson request submitted successfully!</center>";
    } else {
        echo "Error submitting lesson request: " . $conn->error;
    }

    $stmt_request->close();
}

// שליפת השעות שכבר הוזמנו לתאריך המבוקש
$reserved_times = [];
if (!empty($ad['student_date'])) {
    $stmt_reserved = $conn->prepare("SELECT lesson_time FROM lesson_requests WHERE ad_id = ? AND lesson_date = ? AND status != 'declined'");
    $stmt_reserved->bind_param("is", $ad_id, $ad['student_date']);
    $stmt_reserved->execute();
    $result_reserved = $stmt_reserved->get_result();

    while ($row_reserved = $result_reserved->fetch_assoc()) {
        $reserved_times[] = $row_reserved['lesson_time'];
    }
    $stmt_reserved->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.8;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
			margin-top: 20px; 
        }
        h1 {
            font-size: 24px;
            color: #333;
        }
        label {
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }
        select, input[type="submit"] {
            padding: 8px;
            margin-bottom: 10px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }
        p {
            margin-bottom: 12px;
        }
        strong {
            font-weight: bold;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .btn-submit {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
        }
        .btn-submit:hover {
            background-color: #45a049;
        }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Lesson</title>
</head>
<body>
    <div class="container">
        <h1>Request Lesson for: <?php echo htmlspecialchars($ad['title']); ?></h1>
        <p><strong>Lesson Subject:</strong> <?php echo htmlspecialchars($ad['lesson_subject']); ?></p>
        <?php if (!empty($ad['available_hours'])): ?>
            <p><strong>Available Hours:</strong> <?php echo htmlspecialchars($ad['available_hours']); ?></p>
        <?php endif; ?>
        
        <p><strong>Date:</strong> <?php echo htmlspecialchars($ad['student_date']); ?></p>

        <form method="post" action="">
            <input type="hidden" name="ad_id" value="<?php echo $ad_id; ?>">
            
            <label for="lesson_time">Choose a time:</label>
            <select id="lesson_time" name="lesson_time" required>
                <?php
                if (!empty($ad['available_hours'])) {
                    // Split available_hours string into an array of hours
                    $available_hours = explode(",", $ad['available_hours']);

                    // Iterate through the array and print each hour as an option if not reserved
                    foreach ($available_hours as $hour_range) {
                        // Check if the hour_range is in the format of "start-end"
                        if (strpos($hour_range, '-') !== false) {
                            list($start_hour, $end_hour) = explode('-', $hour_range);
                            // Generate options for each hour within the range
                            foreach (range($start_hour, $end_hour) as $hour) {
                                if (!in_array($hour, $reserved_times)) {
                                    echo '<option value="' . htmlspecialchars($hour) . '">' . htmlspecialchars($hour) . '</option>';
                                }
                            }
                        } else {
                            // Otherwise, just print the single hour as an option if not reserved
                            if (!in_array($hour_range, $reserved_times)) {
                                echo '<option value="' . htmlspecialchars($hour_range) . '">' . htmlspecialchars($hour_range) . '</option>';
                            }
                        }
                    }
                }
                ?>
            </select><br>

            <input type="submit" name="request_lesson" value="Request Lesson" class="btn-submit">
        </form>
    </div>
</body>
</html>