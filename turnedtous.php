
	<?php
// התחברות למסד הנתונים
$conn = new mysqli("localhost", "root", "", "mydatabase");

// בדיקת חיבור
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// פונקציה לבדיקת קיום משתמש
function userExists($conn, $username) {
    $stmt = $conn->prepare("SELECT id FROM Users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

// בדיקת קיום משתמש
session_start();
if (!isset($_SESSION['username']) || !userExists($conn, $_SESSION['username'])) {
    // הפנייה לדף ההתחברות אם המשתמש לא קיים או לא מחובר
    header("Location: login.php");
    exit();
}

// יצירת טבלה במסד הנתונים אם איננה קיימת
$tblUser = "CREATE TABLE IF NOT EXISTS ContactForm (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    message TEXT NOT NULL
)";
if ($conn->query($tblUser) === FALSE) {
    die("Error creating table: " . $conn->error);
}

// משתנה לאחסון הודעת מצב
$statusMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // קבלת הנתונים מהטופס
    $email = $_POST['email'];
    $name = $_POST['name'];
    $message = $_POST['message'];

    // הכנסת הנתונים למסד הנתונים
    $sql = "INSERT INTO ContactForm (email, name, message) VALUES ('$email', '$name', '$message')";

    if ($conn->query($sql) === TRUE) {
        $statusMessage = "הנתונים נשמרו בהצלחה";
    } else {
        $statusMessage = "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="utf-8" />
    <title>פנה אלינו</title>
    <style>
        .glow-on-hover {
            width: 130px;
            height: 50px;
            border: none;
            outline: none;
            color: #fff;
            background: rgb(17, 17, 17);
            cursor: pointer;
            position: relative;
            z-index: 0;
            border-radius: 10px;
            font-weight: 600;
        }

        .glow-on-hover:before {
            content: '';
            background: linear-gradient(45deg, #ff0000, #ff7300, #fffb00, #48ff00, #00ffd5, #002bff, #7a00ff, #ff00c8, #ff0000);
            position: absolute;
            top: -2px;
            left: -2px;
            background-size: 400%;
            z-index: -1;
            filter: blur(5px);
            width: calc(100% + 4px);
            height: calc(100% + 4px);
            animation: glowing_54134 20s linear infinite;
            opacity: 0;
            transition: opacity .3s ease-in-out;
            border-radius: 10px;
        }

        .glow-on-hover:active {
            color: #000
        }

        .glow-on-hover:active:after {
            background: transparent;
        }

        .glow-on-hover:hover:before {
            opacity: 1;
        }

        .glow-on-hover:after {
            z-index: -1;
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: #111;
            left: 0;
            top: 0;
            border-radius: 10px;
        }

        @keyframes glowing_54134 {
            0% {
                background-position: 0 0;
            }

            50% {
                background-position: 400% 0;
            }

            100% {
                background-position: 0 0;
            }
        }

        .card {
            background-color: #fff;
            border-radius: 15px;
            padding: 30px;
            width: 350px;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0 auto;
            box-shadow: 0 6px 10px rgb(0, 0, 0);
        }

        h1 {
            text-align: center;
            color: #333;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS';
            font-size: 32px;
            font-weight: bold;
        }

        p {
            text-align: center;
            color: #800000;
            font-size: 18px;
            font-family: Arial, sans-serif;
            margin: 0;
        }

        .inputs {
            width: 200px;
            margin: 0 auto;
        }

        .input {
            max-width: 190px;
            height: 45px;
            width: 100%;
            outline: none;
            font-size: 16px;
            border-radius: 5px;
            padding-left: 15px;
            border: 1px solid #ccc;
            border-bottom-width: 2px;
            transition: all 0.3s ease;
            position: relative;
        }

        .input:valid {
            border-color: #00ff2a;
            color: #00ff2a;
            box-shadow: 2px 2px 8px 1px #00ff2a;
        }

        .input:invalid {
            border-color: #ff0000;
            color: #ff0000;
            box-shadow: 2px 2px 8px 1px #ff0000;
        }

        .text {
            margin-top: 10px;
            color: black;
        }

        body {
            background-image: url('p3.jpg');
            background-size: cover;
            background-position: center;
        }
    </style>
	<style>
	.top-right-button {
    position: absolute;
    top: 10px;
    right: 10px;
}

button {
    padding: 10px 20px;
    background-color: #000000;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background-color:#4dff4d;
}
	 </style>
</head>
<body>
 <div class="top-right-button">
        <button onclick="window.location.href='index.php'">go back</button>
    </div>
    <h1>פנה אלינו</h1>
    <p>
        אנו מחויבים לשירות הכי טוב ולספק חווית משתמש מעולה , במידת הצורך נשמח לעזור ולענות על כל שאלה שתעלה. תודה על ביקורך, ונשמח לראותך באתר שוב
    </p>
    <p>* כל השדות חייבים להיות מלאים</p>
    <br />
    <div class="card">
	
        <form method="POST" action="">
            <div class="inputs">
                <input type="email" name="email" class="input" placeholder="כתובת אימייל" required="">
            </div>
            <br />
            <br />
            <div class="inputs">
                <input type="text" name="name" class="input" placeholder="שם" required="">
            </div>
            <br />
            <br />
            <div class="inputs">
                <input type="text" name="message" class="input" placeholder="הפניה" required="">
            </div>
					
            <br />
            <br />
		
			<center>
		
            <button type="submit" class="glow-on-hover">Submit</button>
			</center>
			
        </form>
	 <?php
        // הצגת הודעת מצב
        if (!empty($statusMessage)) {
            echo "<p>" . $statusMessage . "</p>";
        }
        ?>
        <br />
        <img src="p1.jpg" alt="Description of the image" width="230" height="130">
    </div>
</body>
</html>
