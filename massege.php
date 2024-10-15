<?php
include 'nav.php';
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mydatabase";

// התחברות לבסיס הנתונים
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// בדיקת קיום משתמש מחובר
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// קבלת שם המשתמש המחובר
$user_username = $_SESSION['username'];

// קבלת הנתונים מהטבלה lesson_requests עבור הפוסטים של המשתמש המחובר
$query = "
    SELECT lr.*, a.title AS ad_title, u.username AS requester_username 
    FROM lesson_requests lr
    JOIN ads a ON lr.ad_id = a.id
    JOIN users u ON lr.student_id = u.id
    WHERE a.user_id = (SELECT id FROM users WHERE username = ?)
";
$stmt = $conn->prepare($query);

// בדיקת תקינות השאילתה
if (!$stmt) {
    die("Query failed: " . $conn->error);
}

$stmt->bind_param("s", $user_username);
$stmt->execute();
$result = $stmt->get_result();

// Function to update request status
function updateStatus($conn, $request_id, $new_status) {
    $stmt = $conn->prepare("
        UPDATE lesson_requests
        SET status = ?
        WHERE id = ?
    ");
    $stmt->bind_param("si", $new_status, $request_id);
    $stmt->execute();
    $stmt->close();
}

// Handle approve button click
if (isset($_POST['approve'])) {
    $request_id = $_POST['request_id'];
    updateStatus($conn, $request_id, 'approved');
}

// Handle decline button click
if (isset($_POST['decline'])) {
    $request_id = $_POST['request_id'];
    updateStatus($conn, $request_id, 'declined');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Lesson Requests</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
			
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
			 margin-top: 20px
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        h1 {
            font-size: 24px;
            color: #333;
        }
        .btn {
            padding: 5px 10px;
            cursor: pointer;
        }
        .btn-approve {
            background-color: #4CAF50;
            color: white;
        }
        .btn-decline {
            background-color: #f44336;
            color: white;
        }
		.sidebar {
      width: 200px; /* Width of the sidebar */
      background-color: #f5f5f5; /* Light Gray */
      padding-top: 20px;
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      overflow-x: hidden;
      z-index: 100;
      border-right: 1px solid #ddd;
    }

    .sidebar img {
      display: block;
      margin: 0 auto;
      width: 80%; /* Adjust the logo width as needed */
      padding-bottom: 20px;
    }

    .sidebar a {
      padding: 15px 20px;
      text-decoration: none;
      font-size: 18px;
      color: #333;
      display: block;
    }

    .sidebar a:hover {
      background-color: #ddd;
    }
    </style>
</head>
<body>
  <div class="sidebar">
<a href="mainstudentwork.php">
		<img src="s1.png" alt="לוגו" style="width: 120px; height: 80px; margin-left:20px;">
		</a>
       <a href="studentswork.php">upload post</a>
       <a href="massege.php"> lesson_requests</a>
	   <a href="allmasssge.php"> massege</a>



  </div>
    <div class="container">
        <h1>Your Lesson Requests</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ad Title</th>
                    <th>Requester Username</th>
                    <th>Lesson Date</th>
                    <th>Lesson Time</th>
                    <th>Status</th>
                    <th>Requested At</th>
                    <th>Action</th>
					 <th>feedback</th>

					
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['ad_title']); ?></td>
                            <td><?php echo htmlspecialchars($row['requester_username']); ?></td>
                            <td><?php echo htmlspecialchars($row['lesson_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['lesson_time']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <?php if ($row['status'] === 'pending'): ?>
                                    <form method="post">
                                        <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="approve" class="btn btn-approve">Approve</button>
                                        <button type="submit" name="decline" class="btn btn-decline">Decline</button>
                                    </form>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
							                            <td>

							                                <span><?php echo htmlspecialchars($row['feedback']); ?></span>
															                            </td>


                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No lesson requests found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>