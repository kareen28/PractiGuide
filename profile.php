<?php
// התחלת קוד PHP
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// קישור למסד הנתונים
$conn = new mysqli("localhost", "root", "", "mydatabase");

// בדיקת חיבור
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// יצירת טבלה במסד הנתונים אם איננה קיימת
$tblUser = "CREATE TABLE IF NOT EXISTS UserImages (
    id INT(9) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL,
    image_path VARCHAR(255),
    FOREIGN KEY (username) REFERENCES Users(username),
    UNIQUE(username)
);";

if ($conn->query($tblUser) === FALSE) {
    die("Error creating table: " . $conn->error);
}

// בדיקה האם המשתמש מחובר
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // שאילתה למסד הנתונים לשליפת התמונה של המשתמש הנוכחי
    $sql = "SELECT image_path FROM UserImages WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // שליפת הנתונים מהשאילתה
        $row = $result->fetch_assoc();
        $image_path = $row['image_path'];
        $_SESSION['image_path'] = $image_path;
    } else {
        $image_path = "default_image.jpg"; // תמונה ברירת מחדל במקרה ולא נמצאה תמונה
        $_SESSION['image_path'] = $image_path;
    }

    // שאילתה למסד הנתונים לשליפת הנתונים של המשתמש הנוכחי
    $sql = "SELECT Email, age FROM Users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // שליפת הנתונים מהשאילתה
        $row = $result->fetch_assoc();
        $email = $row['Email'];
        $age = $row['age'];
    } else {
        echo "לא נמצאו נתונים.";
    }
} else {
    // אם המשתמש לא מחובר, הפנה אותו לדף התחברות
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // טיפול בהעלאת תמונה
    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profileImage']['tmp_name'];
        $fileName = $_FILES['profileImage']['name'];
        $fileSize = $_FILES['profileImage']['size'];
        $fileType = $_FILES['profileImage']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDir = 'uploads/';
            $dest_path = $uploadFileDir . $username . '.' . $fileExtension;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // עדכון מסד הנתונים עם הנתיב של התמונה
                $sql = "INSERT INTO UserImages (username, image_path) VALUES ('$username', '$dest_path')
                        ON DUPLICATE KEY UPDATE image_path='$dest_path'";
                if ($conn->query($sql) === TRUE) {
                    echo "";
                    $image_path = $dest_path; // עדכון הנתיב להצגת התמונה החדשה
                    $_SESSION['image_path'] = $image_path;
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error . "<br>";
                }
            } else {
                echo "הייתה בעיה בהעלאת התמונה.<br>";
            }
        } else {
            echo "סוג הקובץ לא נתמך.<br>";
        }
    }

    // טיפול במחיקת תמונה
    if (isset($_POST['deleteProfilePicture'])) {
        $sql = "SELECT image_path FROM UserImages WHERE username='$username'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $image_path = $row['image_path'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        $sql = "DELETE FROM UserImages WHERE username='$username'";
        if ($conn->query($sql) === TRUE) {
            echo "";
            $image_path = "default_image.jpg"; // החזרת תמונת ברירת מחדל לאחר מחיקה
            $_SESSION['image_path'] = $image_path;
        } else {
            echo "Error deleting record: " . $conn->error . "<br>";
        }
    }

   

    // טיפול במחיקת חשבון
    if (isset($_POST["delete_account"])) {
        // מחיקת תמונת הפרופיל של המשתמש
        $sql = "SELECT image_path FROM UserImages WHERE username='$username'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $image_path = $row['image_path'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        // מחיקת פרטי המשתמש ממסד הנתונים
        $sql = "DELETE FROM Users WHERE username='$username'";
        if ($conn->query($sql) === TRUE) {
            echo "החשבון נמחק בהצלחה.<br>";
            session_destroy();
            header("Location: login.php");
            exit();
        } else {
            echo "Error deleting account: " . $conn->error . "<br>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: url('p16.jpg');
            background-size: cover;
        }
        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .profile-picture {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            margin-bottom: 20px;
            border: 5px solid #ff6347;
            object-fit: cover;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        input[type="file"] {
            display: none;
        }
        .file-upload, .delete-profile-picture, .logout, .btn {
            margin: 5px;
            font-size: 18px;
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .file-upload { background-color: #607d8b; color: #fff; }
        .file-upload:hover { background-color: #e55035; }
        .delete-profile-picture { background-color: #607d8b; color: #fff; }
        .delete-profile-picture:hover { background-color: #e55035; }
        .logout { background-color: #607d8b; color: #fff; }
        .logout:hover { background-color: #455a64; }
        .btn-primary { background-color: #607d8b; color: #fff; }
        .btn-primary:hover { background-color: #0056b3; }
        .btn-danger { background-color: #607d8b; color: #fff; }
        .btn-danger:hover { background-color: #e55035; }
        .btn-secondary { background-color: #607d8b; color: #fff; }
        .btn-secondary:hover { background-color: #0056b3; }
		.home-button {
    position: absolute;
    top: 20px;
    right: 20px;
    background-color: #607d8b;
    color: #fff;
    padding: 10px 20px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s;
}

.home-button:hover {
    background-color: #455a64;
}

    </style>

</head>
<body>
    <div class="container">
	   <form action="index.php" method="get">
            <button type="submit" class="home-button">
			BACK TO HOME
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
        
		<path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
        </svg> 			
</button>
		
        </form>
        <?php
        if (isset($_SESSION['image_path'])) {
            echo "<img src='{$_SESSION['image_path']}'  class='profile-picture'>";
        }
        ?>
        <h3>Welcome, <?php echo $username; ?></h3>
        <p>Email: <?php echo $email; ?></p>
        <p>Age: <?php echo $age; ?></p>
       
        <form action="" method="post" enctype="multipart/form-data">
            <label for="profileImage" class="file-upload">בחר תמונה להעלאה</label>
            <input type="file" name="profileImage" id="profileImage" required>
            <button type="submit" class="file-upload">העלה תמונה</button>
        </form>
        <form action="" method="post">
            <button type="submit" name="deleteProfilePicture" class="delete-profile-picture">מחק תמונה</button>
        </form>
        <form action="" method="post">
		<?php
		 // טיפול בעדכון סיסמה
    if (isset($_POST['updatePassword'])) {
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];

        if ($newPassword === $confirmPassword) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE Users SET password='$hashedPassword' WHERE username='$username'";
            if ($conn->query($sql) === TRUE) {
                echo "הסיסמה עודכנה בהצלחה.<br>";
            } else {
                echo "Error updating password: " . $conn->error . "<br>";
            }
        } else {
            echo "הסיסמאות אינן תואמות.<br>";
        }
    }
		?>
            <input type="password" name="newPassword" placeholder="סיסמה חדשה" required class="btn">
            <input type="password" name="confirmPassword" placeholder="אשר סיסמה חדשה" required class="btn">
            <button type="submit" name="updatePassword" class="btn-primary">עדכן סיסמה</button>
        </form>
        <form method="post">
            <button type="submit" name="delete_account" class="btn btn-danger delete-account">Delete Account</button>
        </form>
        <form action="logout.php" method="post">
            <button type="submit" class="logout">התנתק</button>
        </form>
    </div>
</body>
</html>