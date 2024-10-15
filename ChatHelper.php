<?php
include 'nav.php';
session_start();
// בדיקת שם המשתמש מהסשן
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

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

// יצירת טבלת הודעות אם היא לא קיימת
$createTableSQL = "CREATE TABLE IF NOT EXISTS Messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL,
    message TEXT NOT NULL,
    reply_to INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($createTableSQL) === FALSE) {
    echo "Error creating table: " . $conn->error;
}

// אם הוזנה הודעה חדשה או בקשה למחיקה, נבצע פעולה מתאימה
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['message'])) {
        // הוספת הודעה חדשה למסד הנתונים
        $message = $_POST['message'];
        $reply_to = isset($_POST['reply_to']) ? $_POST['reply_to'] : null;

        $insertSQL = "INSERT INTO Messages (username, message, reply_to) VALUES ('$username', '$message', " . ($reply_to ? $reply_to : "NULL") . ")";
        if ($conn->query($insertSQL) === FALSE) {
            echo "Error: " . $conn->error;
        }
    } elseif (isset($_POST['delete'])) {
        // מחיקת הודעה מהמסד הנתונים
        $messageId = $_POST['messageId'];
        $deleteSQL = "DELETE FROM Messages WHERE id = $messageId";
        if ($conn->query($deleteSQL) === FALSE) {
            echo "Error: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>מערכת צ'אט</title>
    <style>
        /* סגנון כללי */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
           
			background-color:#e6f9ff;			

        }

        .sidebar {
            width: 200px;
            background-color: #333;
            color: white;
            padding: 15px;
            height: 100vh;
            position: fixed;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #444;
            text-align: center;
        }

        .sidebar a:hover {
            background-color: #555;
        }

        .main-content {
    margin-left: 200px; /* הקטנת המרווח לסיידבר */
    padding: 15px; /* הקטנת ה-padding של התוכן המרכזי */
    width: calc(100% - 200px); /* התאמת ה-width בעקבות הקטנת הסיידבר */
}

.chat-container {
    background-color: #fff;
    box-shadow: 0 0 8px rgba(0,0,0,0.1); /* הקטנת הצללה */
    border-radius: 6px; /* הקטנת ה-radius */
    overflow: hidden;
    width: 80%; /* הקטנת רוחב הצ'אט */
    margin: 0 auto; /* מרכז את הצ'אט באמצע */
}

.chat-header {
    background-color: #4dd5ff;
    color: #fff;
    padding: 8px; /* הקטנת ה-padding */
    text-align: center;
    font-weight: bold;
    border-bottom: 1px solid #ddd;
}

.chat-messages {
    height: 400px; /* הקטנת גובה הצ'אט */
    overflow-y: auto;
    padding: 10px; /* הקטנת ה-padding */
}

.chat-box {
    padding: 8px; /* הקטנת ה-padding */
    border-bottom: 1px solid #ddd;
    position: relative;
}

.chat-input {
    width: calc(100% - 16px); /* התאמת ה-width */
    padding: 8px; /* הקטנת ה-padding */
    border: 1px solid #ddd;
    border-radius: 4px;
    margin: 8px; /* הקטנת ה-margin */
    box-sizing: border-box;
}

.chat-submit {
    width: calc(100% - 16px); /* התאמת ה-width */
    padding: 8px; /* הקטנת ה-padding */
    background-color: #4dd5ff;
    color: #fff;
    border: none;
    border-radius: 4px;
    margin: 8px; /* הקטנת ה-margin */
    cursor: pointer;
    box-sizing: border-box;
}

.reply {
    background-color: #f0f0f0;
    padding: 8px; /* הקטנת ה-padding */
    margin-top: 4px; /* הקטנת ה-margin */
    border-left: 3px solid #4dd5ff;
}

button.reply-button {
    margin-top: 4px; /* הקטנת ה-margin */
    background-color: #4dd5ff;
    color: white;
    border: none;
    padding: 6px 10px; /* הקטנת ה-padding */
    cursor: pointer;
    border-radius: 4px;
}

button.delete-button {
    background-color: #f44336;
    color: white;
    border: none;
    padding: 6px 10px; /* הקטנת ה-padding */
    cursor: pointer;
    border-radius: 4px;
    position: absolute;
    top: 8px; /* התאמת מיקום הכפתור */
    right: 8px; /* התאמת מיקום הכפתור */
}

    </style>
</head>
<body>
     <div class="sidebar">
        <h2>אפשרויות</h2>
        <a href="ChatHelper.php">חזור לצ'אט</a>
        <a href="groups.php">קבוצות</a>
		 <a href="addgroup.php">צור קבוצה</a>
    </div>
  
 

   
    <div class="main-content">

<div class="chat-container">
    <div class="chat-header">
        <h2>מערכת צ'אט</h2>
    </div>
    <div class="chat-messages" id="chat-messages">
        <?php
        // קבלת הודעות מהמסד הנתונים
        $conn = new mysqli($servername, $username_db, $password_db, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $getMessagesSQL = "SELECT * FROM Messages WHERE reply_to IS NULL ORDER BY created_at DESC";
        $result = $conn->query($getMessagesSQL);

        if ($result) {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='chat-box'>";
                    echo "<div><strong>שם משתמש:</strong> " . htmlspecialchars($row['username']) . "</div>";
                    echo "<div><strong>הודעה:</strong> " . htmlspecialchars($row['message']) . "</div>";
                    echo "<div><strong>זמן:</strong> " . htmlspecialchars($row['created_at']) . "</div>";

                    if ($row['username'] === $username) {
                        echo "<form method='POST' style='display: inline;'>";
                        echo "<input type='hidden' name='messageId' value='{$row['id']}'>";
                        echo "<button type='submit' name='delete' class='delete-button'>מחק</button>";
                        echo "</form>";
                    }

                    displayReplies($row['id'], $conn);

                    echo "<button class='reply-button' onclick='replyToMessage({$row['id']})'>השב</button>";
                    echo "</div>";
                    echo "<hr>";
                }
            } else {
                echo "לא נמצאו הודעות.";
            }
        } else {
            echo "שגיאה בביצוע השאילתה: " . $conn->error;
        }

        function displayReplies($messageId, $conn) {
            $getRepliesSQL = "SELECT * FROM Messages WHERE reply_to = $messageId ORDER BY created_at DESC";
            $repliesResult = $conn->query($getRepliesSQL);

            if ($repliesResult) {
                if ($repliesResult->num_rows > 0) {
                    while ($replyRow = $repliesResult->fetch_assoc()) {
                        echo "<div class='reply'>";
                        echo "<div><strong>שם משתמש:</strong> " . htmlspecialchars($replyRow['username']) . "</div>";
                        echo "<div><strong>תגובה:</strong> " . htmlspecialchars($replyRow['message']) . "</div>";
                        echo "<div><strong>זמן:</strong> " . htmlspecialchars($replyRow['created_at']) . "</div>";
                        echo "</div>";
                    }
                }
            } else {
                echo "שגיאה בביצוע השאילתה לתגובות: " . $conn->error;
            }
        }

        $conn->close();
        ?>
		
    </div>
    <form id="chat-form" method="POST">
        <input type="text" name="message" class="chat-input" placeholder="הקלד הודעה חדשה">
        <input type="hidden" name="reply_to" id="reply_to" value="">
        <button type="submit" class="chat-submit">שלח הודעה</button>
    </form>
</div>
        </div>

<script>
    function replyToMessage(messageId) {
        document.getElementById("reply_to").value = messageId;
        document.querySelector(".chat-input").focus();
    }
</script>
 
</body>
</html>
