<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mydatabase";

// התחברות למסד הנתונים
$conn = new mysqli($servername, $username, $password, $dbname);

// בדיקת חיבור
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// שליפת נתוני התחברויות לפי תאריכים
$query = "SELECT DATE(last_login) as date, COUNT(*) as count FROM Users GROUP BY DATE(last_login)";
$result = $conn->query($query);

// בדיקת שגיאה בשאילתה
if (!$result) {
    die("Query failed: " . $conn->error);
}

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// החזרת הנתונים בפורמט JSON
echo json_encode($data);

$conn->close();
?>
