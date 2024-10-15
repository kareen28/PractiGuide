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

// יצירת טבלה חדשה להודעות
$createMessagesTableSQL = "CREATE TABLE IF NOT EXISTS MessagesG (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT,
    user_id INT,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES Groups(id),
    FOREIGN KEY (user_id) REFERENCES Users(id)
)";

if ($conn->query($createMessagesTableSQL) === FALSE) {
    echo "Error creating table: " . $conn->error;
}

// קבלת מזהה המשתמש הנוכחי
$currentUserIdSQL = "SELECT id FROM Users WHERE username = '$username'";
$currentUserIdResult = $conn->query($currentUserIdSQL);
$currentUserIdRow = $currentUserIdResult->fetch_assoc();
$currentUserId = $currentUserIdRow['id'];

// הוספת הודעה חדשה
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message']) && isset($_POST['group_id'])) {
    $message = $conn->real_escape_string($_POST['message']);
    $group_id = (int)$_POST['group_id']; // המרת ה-group_id למספר שלם

    $addMessageSQL = "INSERT INTO MessagesG (group_id, user_id, message) VALUES ($group_id, $currentUserId, '$message')";
    if ($conn->query($addMessageSQL) === FALSE) {
        echo "Error adding message: " . $conn->error;
    }
    exit();
}

// מחיקת קבוצה
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_group_id'])) {
    $group_id = (int)$_POST['delete_group_id']; // המרת ה-group_id למספר שלם

    // מחיקת חברי הקבוצה
    $deleteMembersSQL = "DELETE FROM GroupMembers WHERE group_id = $group_id";
    $conn->query($deleteMembersSQL);

    // מחיקת הודעות הקבוצה
    $deleteMessagesSQL = "DELETE FROM MessagesG WHERE group_id = $group_id";
    $conn->query($deleteMessagesSQL);

    // מחיקת הקבוצה
    $deleteGroupSQL = "DELETE FROM Groups WHERE id = $group_id";
    $conn->query($deleteGroupSQL);

   
}

// מחיקת הודעה
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_message_id'])) {
    $message_id = (int)$_POST['delete_message_id']; // המרת ה-message_id למספר שלם
    $group_id = (int)$_POST['group_id']; // המרת ה-group_id למספר שלם

    $deleteMessageSQL = "DELETE FROM MessagesG WHERE id = $message_id";
    $conn->query($deleteMessageSQL);
    exit();
}

// יציאת משתמש מקבוצה
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['leave_group_id'])) {
    $group_id = (int)$_POST['leave_group_id']; // המרת ה-group_id למספר שלם

    // מחיקת החבר מהקבוצה
    $leaveGroupSQL = "DELETE FROM GroupMembers WHERE group_id = $group_id AND user_id = $currentUserId";
    if ($conn->query($leaveGroupSQL) === FALSE) {
        echo "Error leaving group: " . $conn->error;
    } else {
        // הוספת הודעה שהמשתמש עזב את הקבוצה
        $leaveMessage = $conn->real_escape_string($username . " עזב את הקבוצה");
        $addLeaveMessageSQL = "INSERT INTO MessagesG (group_id, user_id, message) VALUES ($group_id, $currentUserId, '$leaveMessage')";
        if ($conn->query($addLeaveMessageSQL) === FALSE) {
            echo "Error adding leave message: " . $conn->error;
        }

        header("Location: groups.php");
        exit();
    }
}

// קבלת רשימת הקבוצות מהמסד הנתונים שבהן המשתמש הנוכחי חבר
$getGroupsSQL = "SELECT Groups.id, Groups.name, Groups.created_by
                 FROM Groups
                 JOIN GroupMembers ON Groups.id = GroupMembers.group_id
                 WHERE GroupMembers.user_id = $currentUserId
                 ORDER BY Groups.created_at DESC";
$result = $conn->query($getGroupsSQL);
?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>קבוצות צ'אט</title>
    <style>
        /* סגנון כללי */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
			background: #f0f0f0;
        }
        .sidebar {
            width: 250px;
            background-color: #333;
            color: white;
            padding: 15px;
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
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: #e6f9ff;
        }
        .groups-container {
            flex: 1;
            margin: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        .groups-header {
            background-color: #4dd5ff;
            color: #fff;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
        }
        .groups-list {
            padding: 15px;
        }
        .group-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
        }
        .group-item a {
            text-decoration: none;
            color:  #4dd5ff;
        }
        .delete-button, .leave-button {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 3px;
        }
        .delete-button:hover, .leave-button:hover {
            background-color: #e53935;
        }
        .chat-container {
            margin-top: 20px;
        }
        .message {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .submit-button {
            background-color: #4dd5ff;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
        }
        .submit-button:hover {
            background-color: #66dbff;
        }
    </style>
    <script>
    function toggleChat(groupId) {
        var chatContainer = document.getElementById('chat-container-' + groupId);
        var openLink = document.getElementById('open-chat-' + groupId);
        var closeLink = document.getElementById('close-chat-' + groupId);

        if (chatContainer.style.display === 'none') {
            chatContainer.style.display = 'block';
            openLink.style.display = 'none';
            closeLink.style.display = 'inline';
        } else {
            chatContainer.style.display = 'none';
            openLink.style.display = 'inline';
            closeLink.style.display = 'none';
        }
    }

    function closeChat(groupId) {
        var chatContainer = document.getElementById('chat-container-' + groupId);
        var openLink = document.getElementById('open-chat-' + groupId);
        var closeLink = document.getElementById('close-chat-' + groupId);

        chatContainer.style.display = 'none';
        openLink.style.display = 'inline';
        closeLink.style.display = 'none';
    }

    function sendMessage(event, groupId) {
        event.preventDefault();
        var message = document.getElementById('message-input-' + groupId).value;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // עדכון תיבת הצ'אט עם ההודעה החדשה
                var chatContainer = document.getElementById('chat-container-' + groupId);
                chatContainer.innerHTML += "<div class='message'><strong>אני:</strong> " + message + "<small>(עכשיו)</small></div>";
                document.getElementById('message-input-' + groupId).value = '';
            }
        };
        xhr.send('message=' + encodeURIComponent(message) + '&group_id=' + groupId);
    }
    </script>
</head>
<body>
    <div class="sidebar">
        <h2>אפשרויות</h2>
        <a href="ChatHelper.php">חזור לצ'אט</a>
        <a href="groups.php">קבוצות</a>
        <a href="addgroup.php">צור קבוצה</a>
    </div>
    <div class="main-content">
        <div class="groups-container">
            <div class="groups-header">
                <h2>קבוצות צ'אט</h2>
            </div>
            <div class="groups-list">
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='group-item'>";
                        echo "<a id='open-chat-" . $row['id'] . "' href='javascript:void(0);' onclick='toggleChat(" . $row['id'] . ")'>" . htmlspecialchars($row['name']) . "</a>";
                        echo "<a id='close-chat-" . $row['id'] . "' href='javascript:void(0);' onclick='closeChat(" . $row['id'] . ")' style='display:none;'>סגור צ'אט</a>";
                        echo "<form method='POST' style='display:inline;'>";
                        echo "<input type='hidden' name='leave_group_id' value='" . $row['id'] . "'>";
                        echo "<button type='submit' class='leave-button'>עזוב קבוצה</button>";
                        echo "</form>";
                        if ($row['created_by'] == $currentUserId) {
                            echo "<form method='POST' style='display:inline;'>";
                            echo "<input type='hidden' name='delete_group_id' value='" . $row['id'] . "'>";
                            echo "<button type='submit' class='delete-button'>מחק</button>";
                            echo "</form>";
                        }
                        echo "</div>";

                        // הצגת הודעות וטופס קלט אם נבחרה הקבוצה
                        echo "<div id='chat-container-" . $row['id'] . "' class='chat-container' style='display:none;'>";
                        $groupId = $row['id'];
                        $getMessagesSQL = "SELECT MessagesG.id, MessagesG.message, MessagesG.created_at, Users.username
                                           FROM MessagesG
                                           JOIN Users ON MessagesG.user_id = Users.id
                                           WHERE MessagesG.group_id = $groupId
                                           ORDER BY MessagesG.created_at ASC";
                        $messagesResult = $conn->query($getMessagesSQL);
                        if ($messagesResult && $messagesResult->num_rows > 0) {
                            while ($messageRow = $messagesResult->fetch_assoc()) {
                                echo "<div class='message'>";
                                echo "<strong>" . htmlspecialchars($messageRow['username']) . ":</strong> " . htmlspecialchars($messageRow['message']);
                                echo "<small>(" . $messageRow['created_at'] . ")</small>";
                                if ($messageRow['username'] == $username) {
                                    echo "<form method='POST' style='display:inline;'>";
                                    echo "<input type='hidden' name='delete_message_id' value='" . $messageRow['id'] . "'>";
                                    echo "<input type='hidden' name='group_id' value='$groupId'>";
                                    echo "<button type='submit' class='delete-button'>מחק</button>";
                                    echo "</form>";
                                }
                                echo "</div>";
                            }
                        } else {
                            echo "אין הודעות בקבוצה זו.";
                        }

                        // טופס להוספת הודעה חדשה
                        echo "<form method='POST' onsubmit='sendMessage(event, $groupId)'>";
                        echo "<input type='hidden' name='group_id' value='$groupId'>";
                        echo "<textarea id='message-input-$groupId' name='message' required></textarea>";
                        echo "<button type='submit' class='submit-button'>שלח</button>";
                        echo "</form>";
                        echo "</div>"; // סיום תיבת הצ'אט
                    }
                } else {
                    echo "אין קבוצות זמינות.";
                }
                $conn->close();
                ?>
            </div>
        </div>
    </div>
</body>
</html> 