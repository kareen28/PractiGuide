<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $conn = new mysqli("localhost", "root", "", "mydatabase");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Create Feedback table if it doesn't exist
    $createFeedbackTable = "
    CREATE TABLE IF NOT EXISTS Feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        yes_count INT DEFAULT 0,
        no_count INT DEFAULT 0
    )";

    if ($conn->query($createFeedbackTable) === FALSE) {
        die("Error creating table: " . $conn->error);
    }

    // Insert initial row into Feedback table if it doesn't exist
    $insertInitialRow = "
    INSERT INTO Feedback (yes_count, no_count)
    SELECT 0, 0
    WHERE NOT EXISTS (SELECT * FROM Feedback WHERE id = 1)";

    if ($conn->query($insertInitialRow) === FALSE) {
        die("Error inserting initial row: " . $conn->error);
    }

    // Handle feedback submission
    if (isset($_POST['feedback'])) {
        $feedback = $_POST['feedback'];
        $username = $_SESSION['username'];

        if ($feedback === 'yes') {
            $updateFeedback = "UPDATE Feedback SET yes_count = yes_count + 1 WHERE id = 1";
        } elseif ($feedback === 'no') {
            $updateFeedback = "UPDATE Feedback SET no_count = no_count + 1 WHERE id = 1";
        }

        if ($conn->query($updateFeedback) === TRUE) {
            $updateUserFeedback = "UPDATE Users SET feedback_given = TRUE WHERE username = ?";
            $stmt = $conn->prepare($updateUserFeedback);
            $stmt->bind_param("s", $username);
            if ($stmt->execute() === TRUE) {
                echo "Feedback submitted successfully.";
            } else {
                echo "Error updating user feedback status: " . $conn->error;
            }
            $stmt->close();
        } else {
            echo "Error updating feedback: " . $conn->error;
        }
    }

    $conn->close();
}
?>
