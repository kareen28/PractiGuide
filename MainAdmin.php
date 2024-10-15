<?php
include 'sidebadmin.php';

session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}


// Establish database connection
$conn = new mysqli("localhost", "root", "", "mydatabase");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check and create Feedback table if not exists
$createFeedbackTable = "
    CREATE TABLE IF NOT EXISTS Feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        yes_count INT DEFAULT 0,
        no_count INT DEFAULT 0
    )";

if ($conn->query($createFeedbackTable) === FALSE) {
    die("Error creating table: " . $conn->error);
}

// Fetch feedback data
$sql = "SELECT yes_count, no_count FROM Feedback WHERE id = 1"; // Assuming there is only one row in Feedback table

$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}

$row = $result->fetch_assoc();
$yesCount = $row['yes_count'];
$noCount = $row['no_count'];

$conn->close();
?>
<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Statistics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-image: url('ps5.jpg');
            background-size: cover;
            background-position: center;
        }

     

        #chartContainer {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
        }

        .chart {
            width: 100%;
            max-width: 600px;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .canvas-container {
            margin-bottom: 20px;
        }

    </style>
</head>
<body>


<!-- Sidebar -->

 <div id="chartContainer">
 <center><h1> מספר הנרשמים לאתר </h1> </center>
        <div class="chart">
            <canvas id="loginChart"></canvas>
        </div>
<center><h1>כמה אנשים נהנו מהשירות </h1> </center>
        <div class="chart">
            <canvas id="feedbackChart"></canvas>
        </div>
    </div>

    <script>
        // Fetching data from the server for loginChart
        fetch('get_login_statistics.php')
            .then(response => response.json())
            .then(data => {
                const labels = data.map(entry => entry.date);
                const counts = data.map(entry => entry.count);

                const ctx = document.getElementById('loginChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'מספר התחברויות',
                            data: counts,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1,
                            fill: true
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching login data:', error);
            });

        // Fetching data from the server for feedbackChart
        const ctxFeedback = document.getElementById('feedbackChart').getContext('2d');
        new Chart(ctxFeedback, {
            type: 'bar',
            data: {
                labels: ['Yes', 'No'],
                datasets: [{
                    label: '# of Votes',
                    data: [<?php echo $yesCount; ?>, <?php echo $noCount; ?>],
                    backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(255, 99, 132, 0.2)'],
                    borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>












