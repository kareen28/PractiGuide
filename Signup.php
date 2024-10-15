<?php
session_start();

// קישור למסד הנתונים
$conn = new mysqli("localhost", "root", "", "mydatabase");

// בדיקת חיבור
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// יצירת טבלה במסד הנתונים אם איננה קיימת
$tblUser = "CREATE TABLE IF NOT EXISTS Users (
    id INT(9) PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(30) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    Email VARCHAR(50) NOT NULL UNIQUE,
    age INT(3) NOT NULL,
    feedback_given BOOLEAN DEFAULT FALSE,
    last_login DATETIME DEFAULT NULL
)";

if ($conn->query($tblUser) === FALSE) {
    die("Error creating table: " . $conn->error);
}

// בדיקת הרשמה למערכת
if(isset($_POST['signup'])) {
    $newUser = array(
        "username" => $_POST['username'],
        "Email" => $_POST['Email'],
        "password" => $_POST['password'],
        "age" => $_POST['age']
    );

    // בדיקה אם כתובת המייל חסומה
    $blockedEmailCheck = "SELECT * FROM tblBannedEmails WHERE email='{$newUser['Email']}'";
    $resultBlockedEmail = $conn->query($blockedEmailCheck);
    if ($resultBlockedEmail->num_rows > 0) {
        $error = "אין אפשרות להירשם באמצעות כתובת המייל הנוכחית";
    } else {
        // בדיקה אם המשתמש כבר קיים במסד הנתונים
        $checkUserQuery = "SELECT * FROM Users WHERE username='{$newUser['username']}' OR Email='{$newUser['Email']}'";
        $result = $conn->query($checkUserQuery);

        if ($result->num_rows > 0) {
            $error = "שם משתמש או כתובת מייל כבר קיימים במערכת";
        } else if ($newUser['age'] < 15) {
            $error = "גיל המשתמש חייב להיות 15 ומעלה";
        } else {
            // הוספת משתמש חדש למסד הנתונים
            $hashedPassword = password_hash($newUser['password'], PASSWORD_DEFAULT);
$insertUserQuery = "INSERT INTO Users (username, Email, age, password, last_login) VALUES ('{$newUser['username']}', '{$newUser['Email']}', '{$newUser['age']}', '$hashedPassword',NOW())";            if ($conn->query($insertUserQuery) === TRUE) {
                $_SESSION['username'] = $newUser['username'];
                header("Location: index.php");
                exit();
            } else {
                echo "Error: " . $insertUserQuery . "<br>" . $conn->error;
            }
    }
}
}
?>

<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>הרשמה</title>
	<html lang="he">
			  	
	<link rel="stylesheet"
		href="style.css">
	<style media="screen">
        *,
        *:before,
        *:after {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
             background-image: url('background.jpg'); /* שינוי background.jpg לשם הקובץ של התמונה שלך */
            background-size: cover; /* התאמת התמונה לגודל החלון */
            background-repeat: no-repeat; /* אין חזרה של התמונה */
            background-attachment: fixed; /* קיבוע התמונה במקום גם כאשר משתנה גודל החלון */
            background-position: center; /* התמונה ממוקמת במרכז הדף */
    
        }
 .content {
            text-align: center; /* מרכז התוכן באמצעות יישור טקסט למרכז */
            padding: 50px; /* מרווח מסביב לתוכן */
            color: black; /* צבע טקסט ללבן */
        }
        .background {
            width: 430px;
            height: 520px;
            position: absolute;
            transform: translate(-50%, -50%);
            left: 50%;
            top: 50%;
        }

        .background .shape {
            height: 200px;
            width: 200px;
            position: absolute;
            border-radius: 50%;
        }

        .shape:first-child {
            background: linear-gradient(#1845ad, #23a2f6);
            left: -80px;
            top: -80px;
        }

        .shape:last-child {
            background: linear-gradient(to right, #ff512f, #f09819);
            right: -30px;
            bottom: -80px;
        }

        form {
            height: 800px;
            width: 500px;
            background-color: rgba(255, 255, 255, 0.13);
            position: absolute;
            transform: translate(-50%, -50%);
            top: 50%;
            left: 50%;
            border-radius: 10px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 40px rgba(8, 7, 16, 0.6);
            padding: 40px 45px;
			color:black;
        }

        form * {
            font-family: 'Poppins', sans-serif;
            color: black;
            letter-spacing: 0.5px;
            outline: none;
            border: none;
        }

        form h3 {
            font-size: 32px;
            font-weight: 500;
            line-height: 42px;
            text-align: center;
			 color: black;
        }

        label {
            display: block;
            margin-top: 30px;
            font-size: 16px;
            font-weight: 500;
        }

        input {
            display: block;
            height: 30px;
            width: 100%;
            background-color: rgba(255, 255, 255, 0.07);
            border-radius: 3px;
            padding: 0 10px;
            margin-top: 5px;
            font-size: 14px;
            font-weight: 300;
        }

        ::placeholder {
            color: #e5e5e5;
        }

        button {
            margin-top: 50px;
            width: 100%;
            background-color: black;
            color: #080710;
            padding: 15px 0;
            font-size: 18px;
            font-weight: 600;
            border-radius: 5px;
            cursor: pointer;
        }

        .social {
            margin-top: 30px;
            display: flex;
        }

        .social div {
            background: red;
            width: 150px;
            border-radius: 3px;
            padding: 5px 10px 10px 5px;
            background-color: rgba(255, 255, 255, 0.27);
            color: #eaf0fb;
            text-align: center;
        }

        .social div:hover {
            background-color: rgba(255, 255, 255, 0.47);
        }

        .social .go {
            margin-left: 25px;
			color:black;
        }

        .social i {
            margin-right: 4px;
        }
	
    </style>
</head>
</head>
<body style="background-image: url('p4.jpg');">

<br>
  <div class="background">
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

        <form action="" method="post">
<br>
<br>		    
		<center>	<h2>singup</h2>
			    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
</center>

        <label for="username">username:</label>
        <input type="text" id="username" name="username" required>
		<label for="age">age:</label>
        <input type="text" id="age" name="age" required><br>
		
		
        <label for="Email">Email:</label><br>
        <input type="Email" id="Email" name="Email" required><br>
        <label for="password">password:</label><br>
		
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" name="signup" value="singup">
    </form>

</body>
</html>
