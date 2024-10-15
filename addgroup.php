<?php
session_start();

// בדיקת שם המשתמש מהסשן
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'אנונימי';

// פרטי חיבור למסד הנתונים
$servername = "localhost";
$username_db = "root";
$password_db = ""; // עדכן את הסיסמה שלך
$dbname = "mydatabase"; // עדכן את שם מסד הנתונים שלך

// יצירת חיבור
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// בדיקת חיבור
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// יצירת טבלת קבוצות אם היא לא קיימת
$createGroupsTableSQL = "CREATE TABLE IF NOT EXISTS Groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($createGroupsTableSQL);

// יצירת טבלת חברי קבוצות אם היא לא קיימת
$createGroupMembersTableSQL = "CREATE TABLE IF NOT EXISTS GroupMembers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (group_id) REFERENCES Groups(id),
    FOREIGN KEY (user_id) REFERENCES Users(id)
)";
$conn->query($createGroupMembersTableSQL);

// קבלת מזהה המשתמש הנוכחי
$currentUserIdSQL = "SELECT id FROM Users WHERE username = '$username'";
$currentUserIdResult = $conn->query($currentUserIdSQL);
$currentUserIdRow = $currentUserIdResult->fetch_assoc();
$currentUserId = $currentUserIdRow['id'];

// יצירת קבוצה חדשה
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['group_name'])) {
    $group_name = $_POST['group_name'];
    $user_ids = $_POST['user_ids'];
    
    // הוספת הקבוצה
    $createGroupSQL = "INSERT INTO Groups (name, created_by) VALUES ('$group_name', $currentUserId)";
    if ($conn->query($createGroupSQL) === TRUE) {
        $group_id = $conn->insert_id;

        // הוספת המשתמשים לקבוצה
        foreach ($user_ids as $user_id) {
            $addMemberSQL = "INSERT INTO GroupMembers (group_id, user_id) VALUES ($group_id, $user_id)";
            $conn->query($addMemberSQL);
        }

        echo "קבוצה נוצרה בהצלחה!";
    } else {
        echo "Error: " . $conn->error;
    }
}

// סינון המשתמשים לפי שם משתמש שהוזן
$searchQuery = '';
if (isset($_GET['search_username'])) {
    $searchQuery = $_GET['search_username'];
}

// קבלת רשימת משתמשים מסוננת
$getUsersSQL = "SELECT id, username FROM Users WHERE username LIKE '%$searchQuery%'";
$result = $conn->query($getUsersSQL);
?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>צור קבוצה חדשה</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            direction: rtl;
            background-image: url('c5.jpg');
            background-size: cover;
            background-position: center;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 40%;
            margin: auto;
            margin-top: 50px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        h2, h3 {
            text-align: center;
            color:  #1ac6ff;
        }
        label {
            font-weight: bold;
        }
        input[type="text"], .submit-button, input[type="checkbox"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .user-list {
            margin-bottom: 20px;
        }
        .user-item {
            margin-bottom: 10px;
            padding: 5px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .submit-button {
            background-color: #1ac6ff;
            color: white;
            padding: 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }
        .submit-button:hover {
            background-color: #4dd2ff;
        }
        .top-right-button {
            position: absolute;
            top: 30px;
            right: 10px;
        }
        button {
            padding: 10px 20px;
            background-color: #0099cc;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #4dd2ff;
        }
        .search-bar {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="top-right-button">
        <button onclick="window.location.href='ChatHelper.php'">חזור</button>
    </div>
    <div class="container">
        <h2>צור קבוצה חדשה</h2>
        <div class="search-bar">
            <form method="GET">
                <input type="text" name="search_username" placeholder="חפש משתמש..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button type="submit">חפש</button>
            </form>
        </div>
        <form method="POST">
            <div>
                <label for="group_name">שם הקבוצה:</label>
                <input type="text" id="group_name" name="group_name" required>
            </div>
            <div class="user-list">
                <h3>בחר משתמשים:</h3>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $checked = ($row['username'] == $username) ? 'checked' : '';
                        echo "<div class='user-item'>";
                        echo "<input type='checkbox' name='user_ids[]' value='{$row['id']}' $checked> {$row['username']}";
                        echo "</div>";
                    }
                } else {
                    echo "<div class='user-item'>אין משתמשים.</div>";
                }
                ?>
            </div>
            <button type="submit" class="submit-button">צור קבוצה</button>
        </form>
    </div>
</body>
</html>
