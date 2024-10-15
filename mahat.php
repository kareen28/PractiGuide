<?php

function calculateFinalGrade($examScore, $protectionScore) {
    $averageScore = ($examScore + $protectionScore) / 2;

    if ($examScore >= 40 && $examScore <= 54) {
        if (abs($examScore - $protectionScore) > 20) {
            if ($averageScore >= 53) {
                return 55;
            }
        }
    }
    
    return $averageScore;
}

$finalResult = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $examScore = $_POST["exam_score"];
    $protectionScore = $_POST["protection_score"];
    
    if (is_numeric($examScore) && is_numeric($protectionScore)) {
        if ($examScore >= 0 && $examScore <= 100 && $protectionScore >= 0 && $protectionScore <= 100) {
            $finalGrade = calculateFinalGrade($examScore, $protectionScore);
            $finalResult = "הציון הסופי שלך הוא: " . $finalGrade;
        } else {
            $finalResult = "נא להכניס ציונים בין 0 ל-100.";
        }
    } else {
        $finalResult = "נא להכניס ציונים מספריים חוקיים.";
    }
}
?>
<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>מה"ט</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        /* Reset default margin and padding */
        * {
            margin: 0;
            padding: 0;
        }

        /* General styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            position: relative;
            overflow-x: hidden;
        }

        /* Styling for video background */
        .background-video {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            z-index: -1;
            object-fit: cover;
            height: 100%;
            width: 100%;
        }

        /* Styling for the content */
        .container {
            position: relative;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent background */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2, h3, p {
            margin-bottom: 20px;
            color: #343a40;
        }

        form {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="number"], input[type="submit"] {
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .result {
            font-size: 18px;
            font-weight: bold;
            color: #cc2900;
        }

        /* Styling for products */
        .product-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }

        .product-item {
            width: 80%;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .product-details {
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Include Navbar -->
    <?php include 'nav.php'; ?>

    <!-- Content -->
    <video autoplay muted loop class="background-video">
        <source src="v1.mp4" type="video/mp4">
        Your browser does not support HTML5 video.
    </video>

    <div class="container">
        <h2>מה"ט</h2>

        <div class="product-container">
            <?php
            $conn = new mysqli("localhost", "root", "", "mydatabase");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $result = $conn->query("SELECT * FROM mahat");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="product-item">';
                    echo '<div class="product-details">';
                    echo '<p>' . $row['description'] . '</p>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p>No products found.</p>";
            }

            $conn->close();
            ?>
        </div>

        <h2>סטודנטים עשיתים בחינת מה"ט !! זה יעזור לכם</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="exam_score">ציון בחינה:</label>
            <input type="number" id="exam_score" name="exam_score" min="0" max="100" required>
            <label for="protection_score">ציון מגן:</label>
            <input type="number" id="protection_score" name="protection_score" min="0" max="100" required>
            <input type="submit" value="חשב ציון סופי">
        </form>
        <?php if ($finalResult): ?>
            <div class="result"><?php echo $finalResult; ?></div>
        <?php endif; ?>
        <p>הערה</p>
        <p>ציון סופי = ממוצע של ציון בחינה וציון מגן</p>
        <p>ציון בחינה 40-54, ופער של מעל 20 נקודות בין ציון הבחינה לציון המגן: ציון סופי = 55 בתנאי שהציון שיתקבל על-ידי שקלול ציון המגן וציון הבחינה הוא 53 לפחות</p>
        <br>
        <h3>קביעת ציון סופי לנבחן בפרויקט גמר</h3>
        <p>* 15% מהציון יינתן ע"י מרכז המגמה.</p>
        <p>* 15% מהציון יינתן ע"י המנחה האישי.</p>
        <p>%70 מהציון יינתן ע"י הבוחנים החיצוניים</p>
    </div>
</body>
</html>
