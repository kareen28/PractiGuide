<?php
include 'nav.php';
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mydatabase";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get the logged-in user's ID
$stmt_user = $conn->prepare("SELECT id FROM Users WHERE username = ?");
if (!$stmt_user) {
    die("Prepare failed: " . $conn->error);
}
$stmt_user->bind_param("s", $_SESSION['username']);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows == 0) {
    die("User not found.");
}

$user = $result_user->fetch_assoc();
$user_id = $user['id'];
$stmt_user->close();

// Handle POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = intval($_POST['request_id']);

    if (isset($_POST['save_feedback'])) {
        $feedback = $_POST['feedback'];

        // Validate feedback
        if (!empty($feedback)) {
            $stmt = $conn->prepare("UPDATE lesson_requests SET feedback = ? WHERE id = ? AND student_id = ?");
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("sii", $feedback, $request_id, $user_id);

            if ($stmt->execute()) {
                echo "Feedback updated successfully.";
            } else {
                echo "Error updating feedback: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Feedback cannot be empty.";
        }
    }

    if (isset($_POST['save_rating'])) {
        $rating = $_POST['rating'];

        // Validate rating
        if (is_numeric($rating) && $rating >= 1 && $rating <= 5) {
            $stmt = $conn->prepare("UPDATE lesson_requests SET rating = ? WHERE id = ? AND student_id = ?");
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("iii", $rating, $request_id, $user_id);

            if ($stmt->execute()) {
                echo "Rating updated successfully.";
            } else {
                echo "Error updating rating: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Rating must be a number between 1 and 5.";
        }
    }

    if (isset($_POST['save_notes'])) {
        $notes = $_POST['notes'];

        // Validate notes
        if (!empty($notes)) {
            $stmt = $conn->prepare("UPDATE lesson_requests SET notes = ? WHERE id = ? AND student_id = ?");
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("sii", $notes, $request_id, $user_id);

            if ($stmt->execute()) {
                echo "Notes updated successfully.";
            } else {
                echo "Error updating notes: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Notes cannot be empty.";
        }
    }

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// Fetch user's lesson requests
$stmt_requests = $conn->prepare("
    SELECT 
        lr.id,
        a.title AS ad_title,
        u.username AS requester_username,
        lr.lesson_date,
        lr.lesson_time,
        lr.status,
        lr.created_at,
        lr.feedback,
        lr.rating
    FROM lesson_requests lr
    JOIN ads a ON lr.ad_id = a.id
    JOIN Users u ON lr.student_id = u.id
    WHERE lr.student_id = ?
    ORDER BY lr.created_at DESC
");
if (!$stmt_requests) {
    die("Prepare failed: " . $conn->error);
}
$stmt_requests->bind_param("i", $user_id);
$stmt_requests->execute();
$result_requests = $stmt_requests->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 990px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
			margin-top: 20px;
        }
        h1 {
            font-size: 24px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn {
            padding: 8px 16px;
            text-decoration: none;
            color: white;
            border-radius: 4px;
            display: inline-block;
            margin: 4px;
            font-size: 14px;
        }
        .btn-primary {
            background-color: #007bff;
        }
        .btn-danger {
            background-color: #dc3545;
        }
        .btn-primary:hover, .btn-danger:hover {
            opacity: 0.8;
        }
        .status {
            text-transform: capitalize;
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Lesson Requests</title>
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
                    <th>Feedback</th>
                    <th>Rating</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_requests->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['ad_title']); ?></td>
                        <td><?php echo htmlspecialchars($row['requester_username']); ?></td>
                        <td><?php echo htmlspecialchars($row['lesson_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['lesson_time']); ?></td>
                        <td class="status"><?php echo htmlspecialchars($row['status']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($row['feedback']); ?></td>
                        <td><?php echo htmlspecialchars($row['rating']); ?></td>
                        <td>
                            <?php if ($row['status'] == 'approved'): ?>
                                <form method="post" action="">
                                    <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                    <textarea name="feedback" placeholder="Leave your feedback..."><?php echo htmlspecialchars($row['feedback']); ?></textarea><br>
                                    <input type="submit" name="save_feedback" value="Save Feedback" class="btn btn-primary">
                                </form>
                                <form method="post" action="" style="margin-top: 8px;">
                                    <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                    <input type="number" name="rating" min="1" max="5" value="<?php echo htmlspecialchars($row['rating']); ?>" required placeholder="Rate 1-5">
                                    <input type="submit" name="save_rating" value="Save Rating" class="btn btn-primary">
                                </form>
                                <form method="post" action="" style="margin-top: 8px;">
                                    <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                
                                  
                                </form>
                            <?php else: ?>
                                <span>Feedback: <?php echo htmlspecialchars($row['feedback']); ?></span><br>
                                <span>Rating: <?php echo htmlspecialchars($row['rating']); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$stmt_requests->close();
$conn->close();
?>