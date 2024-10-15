<?php
session_start();

$error = "";
$adminUsername = "admin";
$adminPassword = "admin123"; // You should store passwords securely in a real application

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection and error handling
    $conn = new mysqli("localhost", "root", "", "mydatabase");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $input_username = $_POST['username'];
    $input_password = $_POST['password'];
    
    // בדיקת האם המשתמש הוא מנהל
    if ($input_username == $adminUsername && $input_password == $adminPassword) {
        // אם זהו מנהל, מעבר לדף הניהול
        $_SESSION['admin_logged_in'] = true;
        header("Location: MainAdmin.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }

    // Prepare a statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM Users WHERE username=?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $input_username);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if ($result === false) {
        die("Get result failed: " . $stmt->error);
    }

    if ($result->num_rows > 0) {
        // User exists, check password
        $row = $result->fetch_assoc();
        if (password_verify($input_password, $row['password'])) {
            // Password correct, create session and redirect
            $_SESSION['username'] = $input_username;
            $_SESSION['show_modal'] = !$row['feedback_given'];
            header("Location: index.php");
            exit();
        } else {
            // Incorrect password, display error
            $error = "שם משתמש או סיסמה שגויים";
        }
    } else {
        // User doesn't exist, prompt for signup
        $error = "משתמש לא נמצא. אנא הירשם.";
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="he">
<head>
  <title>Glassmorphism login Form Tutorial in html css</title>
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
  <style media="screen">
    *,
    *:before,
    *:after {
      padding: 0;
      margin: 0;
      box-sizing: border-box;
    }

    body {
       background-image: url('background.jpg');
       background-size: cover;
       background-repeat: no-repeat;
       background-attachment: fixed;
       background-position: center;
    }

    .background {
      width: 430px;
      height: 520px;
      position: absolute;
      transform: translate(-50%,-50%);
      left: 50%;
      top: 50%;
    }

    .content {
      text-align: center;
      padding: 50px;
      color: black;
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
      height: 600px;
      width: 400px;
      background-color: rgba(255,255,255,0.13);
      position: absolute;
      transform: translate(-50%,-50%);
      top: 50%;
      left: 50%;
      border-radius: 10px;
      backdrop-filter: blur(10px);
      border: 2px solid rgba(255,255,255,0.1);
      box-shadow: 0 0 40px rgba(8,7,16,0.6);
      padding: 50px 35px;
      color: black;
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
    }

    label {
      display: block;
      margin-top: 30px;
      font-size: 16px;
      font-weight: 500;
    }

    input {
      display: block;
      height: 50px;
      width: 100%;
      background-color: rgba(255,255,255,0.07);
      border-radius: 3px;
      padding: 0 10px;
      margin-top: 8px;
      font-size: 14px;
      font-weight: 300;
      color: black;
    }

    ::placeholder {
      color: black;
    }

    button {
      margin-top: 50px;
      width: 100%;
      background-color: black;
      color: black;
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
      background-color: rgba(255,255,255,0.27);
      color: #eaf0fb;
      text-align: center;
    }

    .social div:hover {
      background-color: rgba(255,255,255,0.47);
    }

    .social .go {
      margin-left: 25px;
      color: black;
    }

    .social i {
      margin-right: 4px;
    }
  </style>
</head>

<body style="background-image: url('p4.jpg');">
  <div class="background">
    <div class="shape"></div>
    <div class="shape"></div>
  </div>
  
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="content">
      <h3>Login Here</h3>

      <center>
        <?php
          if (!empty($error)) {
              echo "<p class='error-message'>$error</p>";
          }
        ?>
      </center>
  
      <label for="username">Username</label>
      <input type="text" placeholder="Username" id="username" name="username">

      <label for="password">Password</label>
      <input type="password" placeholder="Password" id="password" name="password">

      <input type="submit" value="Log In" class="form-button">
      <center>
      
        <a href="Signup.php" class="signup-link">signup</a>
      </center>
    </div>
    <br><br><br>
  </form>
</body>
</html>
