<?php include 'sidebadmin.php';
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// התחברות למסד הנתונים
$conn = new mysqli("localhost", "root", "", "mydatabase");

// בדיקת חיבור
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// קבלת כל הפניות מהטבלה
$sql = "SELECT id, email, name, message FROM ContactForm";
$result = $conn->query($sql);

// טיפול במחיקה
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $delete_sql = "DELETE FROM ContactForm WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    // רענון הדף לאחר מחיקה
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="utf-8" />
    <title>דף מנהל - פניות</title>
    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
            background-color:#ccccff;
        }
        .content {
            flex: 1;
            padding: 10px;
        }
        table {
            width: 85%;
            border-collapse: collapse;
            margin-left: 220px; /* מרווח משמאל */
            margin-right: 20px; /* מרווח מימין */
        }
        th, td {
            padding: 8px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            text-align: center;
            color: #343a40;
        }
        form {
            margin-bottom: 20px;
        }
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
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .email-button, .delete-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            margin: 2px;
        }
        .email-button:hover, .delete-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="content">
       <center><h1>פניות המשתמשים</h1></center>
        <?php
        if ($result->num_rows > 0) {
            echo "<table class='table table-bordered'>";
            echo "<tr><th>כתובת אימייל</th><th>שם</th><th>הודעה</th><th>פעולות</th></tr>";
            // פלט של כל שורה בטבלה
            while($row = $result->fetch_assoc()) {
                $id = $row["id"];
                $email = $row["email"];
                echo "<tr>
                        <td>" . $email . "</td>
                        <td>" . $row["name"] . "</td>
                        <td>" . $row["message"] . "</td>
                        <td>
                            <a href='https://mail.google.com/mail/?view=cm&fs=1&to=$email' target='_blank'>
                                <button class='email-button'>פנה</button>
                            </a>
                            <form method='post' action='' style='display:inline-block;'>
                                <input type='hidden' name='delete_id' value='$id'>
                                <button type='submit' class='delete-button'>מחק</button>
                            </form>
                        </td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "אין פניות להצגה";
        }
        $conn->close();
        ?>
    </div>
</body>
</html>
