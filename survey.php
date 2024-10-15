<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mydatabase";

// יצירת חיבור
$conn = new mysqli($servername, $username, $password, $dbname);

// בדיקת חיבור
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql_create_table = "CREATE TABLE IF NOT EXISTS user_answers (
    user_id VARCHAR(255) PRIMARY KEY,
    q1 VARCHAR(255) NOT NULL,
    q2 VARCHAR(255) NOT NULL,
    q3 VARCHAR(255) NOT NULL,
    q4 VARCHAR(255) NOT NULL,
    q5 VARCHAR(255) NOT NULL,
    q6 VARCHAR(255) NOT NULL,
    q7 VARCHAR(255) NOT NULL,
    q8 VARCHAR(255) NOT NULL,
    math_grade INT(3) NOT NULL,
    physics_grade INT(3) NOT NULL,
    Math_unit INT(1) NOT NULL DEFAULT 3,
    Physics_unit INT(1) NOT NULL DEFAULT 3
)";

if ($conn->query($sql_create_table) !== TRUE) {
    echo "Error creating table: " . $conn->error;
}

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['username'];

// Handle answer update
if (isset($_POST['updateAnswer'])) {
    $questionId = $_POST['question_id'];
    $newAnswer = htmlspecialchars($_POST['new_answer']);

    // Check if values are set
    if (!empty($questionId) && !empty($newAnswer)) {
        $sql = "UPDATE user_answers SET $questionId=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("ss", $newAnswer, $user_id);
        if ($stmt->execute()) {
            echo "התשובה עודכנה בהצלחה.";
        } else {
            echo "Error updating answer: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error: ערכים לא מוגדרים לעדכון התשובה.";
    }
}

// Save form answers to Database
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['updateAnswer'])) {
    $q1 = isset($_POST['q1']) ? htmlspecialchars($_POST['q1']) : '';
    $q2 = isset($_POST['q2']) ? htmlspecialchars($_POST['q2']) : '';
    $q3 = isset($_POST['q3']) ? htmlspecialchars($_POST['q3']) : '';
    $q4 = isset($_POST['q4']) ? htmlspecialchars($_POST['q4']) : '';
    $q5 = isset($_POST['q5']) ? htmlspecialchars($_POST['q5']) : '';
    $q6 = isset($_POST['q6']) ? htmlspecialchars($_POST['q6']) : '';
    $q7 = isset($_POST['q7']) ? htmlspecialchars($_POST['q7']) : '';
    $q8 = isset($_POST['q8']) ? htmlspecialchars($_POST['q8']) : '';
    $math_grade = isset($_POST['math_grade']) ? intval($_POST['math_grade']) : 0;
    $physics_grade = isset($_POST['physics_grade']) ? intval($_POST['physics_grade']) : 0;
    $Math_unit = isset($_POST['Math_unit']) ? intval($_POST['Math_unit']) : 3;
    $Physics_unit = isset($_POST['Physics_unit']) ? intval($_POST['Physics_unit']) : 3;

    // Check if all mandatory questions are answered
    $mandatoryQuestions = ['q1', 'q2', 'q3', 'q4', 'q5', 'q6', 'q7', 'q8'];
    $allAnswered = true;
    foreach ($mandatoryQuestions as $question) {
        if (empty($_POST[$question])) {
            $allAnswered = false;
            break;
        }
    }

    if (!$allAnswered) {
        echo "Please answer all questions.";
    } else {
        // Check if user already has answers in database
        $sql_check_user = "SELECT * FROM user_answers WHERE user_id=?";
        $stmt = $conn->prepare($sql_check_user);
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update existing answers
            $sql_update = "UPDATE user_answers SET
                q1=?, q2=?, q3=?, q4=?, q5=?, q6=?, q7=?, q8=?,
                math_grade=?, physics_grade=?, Math_unit=?, Physics_unit=?
                WHERE user_id=?";
            $stmt = $conn->prepare($sql_update);
            if ($stmt === false) {
                die("Error preparing statement: " . $conn->error);
            }
            $stmt->bind_param("sssssssssiiss", $q1, $q2, $q3, $q4, $q5, $q6, $q7, $q8,
                $math_grade, $physics_grade, $Math_unit, $Physics_unit, $user_id);
            if ($stmt->execute()) {
                echo "התשובות עודכנו בהצלחה.";
            } else {
                echo "Error updating record: " . $stmt->error;
            }
            $stmt->close();
        } else {
            // Insert new answers
            $sql_insert = "INSERT INTO user_answers (user_id, q1, q2, q3, q4, q5, q6, q7, q8,
                math_grade, physics_grade, Math_unit, Physics_unit)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql_insert);
            if ($stmt === false) {
                die("Error preparing statement: " . $conn->error);
            }
            $stmt->bind_param("sssssssssiiss", $user_id, $q1, $q2, $q3, $q4, $q5, $q6, $q7, $q8,
                $math_grade, $physics_grade, $Math_unit, $Physics_unit);
            if ($stmt->execute()) {
                echo "התשובות נשמרו בהצלחה.";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

$sql = "SELECT * FROM user_answers WHERE user_id=?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $q1 = $row['q1'];
    $q2 = $row['q2'];
    $q3 = $row['q3'];
    $q4 = $row['q4'];
    $q5 = $row['q5'];
    $q6 = $row['q6'];
    $q7 = $row['q7'];
    $q8 = $row['q8'];
    $math_grade = $row['math_grade'];
    $physics_grade = $row['physics_grade'];
    $Math_unit = $row['Math_unit'];
    $Physics_unit = $row['Physics_unit'];
} else {
    $q1 = $q2 = $q3 = $q4 = $q5 = $q6 = $q7 = $q8 = '';
    $math_grade = $physics_grade = $Math_unit = $Physics_unit = 0;
}

$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="he">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical Engineer Survey</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            direction: rtl;
			background-image: url('psurveygreen.jpg');
            background-size: cover; /* ממלא את כל השטח של האלמנט */
            background-position: center; /* מרכז את התמונה בתוך האלמנט */
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
        }
        p {
            color: #333;
            margin-bottom: 20px;
        }
        .question {
            margin-bottom: 20px;
        }

        .question p {
            margin-bottom: 10px;
        }

        .question label {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .question input[type="radio"] {
            margin-left: 10px;
            margin-right: 0;
        }

        input[type="submit"] {
            background-color: #00cccc;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            display: block;
            margin: auto;
        }

        input[type="submit"]:hover {
            background-color: #00e6e6;
        }

        .error {
            color: black;
            text-shadow: 1px 1px 2px red;
           
            font-weight: bold;
            font-family: verdana;
            font-size: 20px;
        }

        .image-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 10px; /* מרווח בין התמונות */
}

        .image-container img {
            width: 100%; /* רוחב התמונה בתוך הגריד */
            height: 100px; /* גובה התמונה */
            object-fit: cover; /* לשמור על יחס התמונה בתוך הרוחב והגובה */
            margin: 10px; /* מרווח מסביב לתמונה */
            border: 2px solid black; /* גבול סביב לתמונה */
}

        input[type="submit"] {
            background-color:#00cccc;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            display: block;
            margin: auto;
        }

        input[type="submit"]:hover {
            background-color:#00e6e6;
        }

<style>
/*כפתור חזרה אחורה*/
    .home-button {
        background-color:#00cccc; /* Blue background */
        color: white; /* White text */
        border: none; /* No border */
        padding: 10px 20px; /* Padding around the text */
        font-size: 16px; /* Font size */
        border-radius: 5px; /* Rounded corners */
        display: flex; /* Flexbox for better alignment */
        align-items: center; /* Vertically center */
        gap: 10px; /* Space between text and icon */
        cursor: pointer; /* Pointer cursor on hover */
        transition: background-color 0.3s ease; /* Smooth background color change */
    }

    .home-button:hover {
        background-color:#00cccc; /* Darker blue on hover */
    }

    .home-button svg {
        margin-left: 10px; /* Space between text and icon */
    }

    .home-button:focus {
        outline: none; /* Remove focus outline */
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.5); /* Add focus shadow */
    }

    .home-button:active {
        background-color: #004085; /* Even darker blue when clicked */
    }
</style>


    </style>
</head>
<body>
<form action="index.php" method="get">
    <button type="submit" class="home-button">
        BACK TO HOME
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
            <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
        </svg>
    </button>
</form>

<div class="container">
    <h1>Practical Engineer Survey</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="question">
            <p>1. מהם התפקידים המעניינים ביותר בתחום ההנדסאים בעיניך?</p>
            <label><input type="radio" name="q1" value="a" <?php if ($q1 == 'a') echo 'checked'; ?>> א) פיתוח מוצרים חדשים</label>
            <label><input type="radio" name="q1" value="b" <?php if ($q1 == 'b') echo 'checked'; ?>> ב) תכנון ובנייה של תשתיות</label>
            <label><input type="radio" name="q1" value="c" <?php if ($q1 == 'c') echo 'checked'; ?>> ג) מחקר ופיתוח בתחום טכנולוגי</label>


        </div>
        
        <div class="question">
            <p>2. למה אתה מעוניין ללמוד הנדסאים?</p>
            <label><input type="radio" name="q2" value="a" <?php if ($q2 == 'a') echo 'checked'; ?>> א) תשוקה לפתרון בעיות וחדשנות</label>
            <label><input type="radio" name="q2" value="b" <?php if ($q2 == 'b') echo 'checked'; ?>> ב) עניין ביצירת טכנולוגיה שמשפרת חיים</label>
            <label><input type="radio" name="q2" value="c" <?php if ($q2 == 'c') echo 'checked'; ?>> ג) רצון לקריירה יציבה ומרתקת</label>

        </div>

        <div class="question">
            <p>3. מה מעורב בתחום ההנדסאים שמעניין אותך הכי?</p>
            <label><input type="radio" name="q3" value="a" <?php if ($q3 == 'a') echo 'checked'; ?>> א) פיתוח טכנולוגיות חדשות</label>
            <label><input type="radio" name="q3" value="b" <?php if ($q3 == 'b') echo 'checked'; ?>> ב) פיתוח תשתיות חשמל, אנרגיה ותעשייה</label>
            <label><input type="radio" name="q3" value="c" <?php if ($q3 == 'c') echo 'checked'; ?>> ג) פיתוח תוכנה ואפליקציות מתקדמות</label>

        </div>

        <div class="question">
            <p>4. מה גורם לך לבחור בתחום ההנדסאים ?</p>
            <label><input type="radio" name="q4" value="a" <?php if ($q4 == 'a') echo 'checked'; ?>> א) הפוטנציאל לשיפור תנאי החיים והסביבה</label>
            <label><input type="radio" name="q4" value="b" <?php if ($q4 == 'b') echo 'checked'; ?>> ב) האפשרות ליצירתיות טכנולוגית</label>
            <label><input type="radio" name="q4" value="c" <?php if ($q4 == 'c') echo 'checked'; ?>> ג) האתגרים המתמידים וההתקדמות הטכנולוגית המתמשכת</label>

        </div>

        <div class="question">
            <p>5. לאיזו ענף בתחום ההנדסאים אתה מעוניין להתמחות?</p>
            <label><input type="radio" name="q5" value="a" <?php if ($q5 == 'a') echo 'checked'; ?>> א) הנדסאי מכונות ורכב</label>
            <label><input type="radio" name="q5" value="b" <?php if ($q5 == 'b') echo 'checked'; ?>> ב) הנדסאי תוכנה ומערכות מידע</label>
            <label><input type="radio" name="q5" value="c" <?php if ($q5 == 'c') echo 'checked'; ?>> ג) הנדסאי עיצוב פנים או נוף</label>

        </div>

        <div class="question">
            <p>6. מהם המידות החשובות ביותר בהנדסאי המוצלח?</p>
            <label><input type="radio" name="q6" value="a" <?php if ($q6 == 'a') echo 'checked'; ?>> א) יכולת יצירתית וחשיבה מחוץ לקופסה</label>
            <label><input type="radio" name="q6" value="b" <?php if ($q6 == 'b') echo 'checked'; ?>> ב) יכולת עבודת צוות ותקשורת טובה</label>
            <label><input type="radio" name="q6" value="c" <?php if ($q6 == 'c') echo 'checked'; ?>> ג) יכולת פתרון בעיות וניהול פרויקטים</label>

        </div>

        <div class="question">
            <p>7. האם יש לך מטרות קריירה ספציפיות שאתה מקווה להשיג דרך הלימודים בהנדסאים ?</p>
            <label><input type="radio" name="q7" value="a" <?php if ($q7 == 'a') echo 'checked'; ?>> א) המשך התקדמות בתחום האנרגיה המתחדשת והסביבה</label>
            <label><input type="radio" name="q7" value="b" <?php if ($q7 == 'b') echo 'checked'; ?>> ב) עיצוב תשתית לתמיכה בפיתוח ערים ויישובים</label>
            <label><input type="radio" name="q7" value="c" <?php if ($q7 == 'c') echo 'checked'; ?>> ג) חדשנות בתחומים כמו אווירונאוטיקה או רובוטיקה</label>

        </div>
        <div class="question">
            <p>8. מה הרקע הקודם שלך בתחום הנדסאים?</p>
            <label><input type="radio" name="q8" value="b" <?php if ($q8 == 'a') echo 'checked'; ?>> א) קורסים מקצועיים</label>
            <label><input type="radio" name="q8" value="c" <?php if ($q8 == 'b') echo 'checked'; ?>> ב) ניסיון עבודה</label>
            <label><input type="radio" name="q8" value="d" <?php if ($q8 == 'c') echo 'checked'; ?>> ג) אין רקע קודם</label>

        </div>
        <div class="question">
            <p>9. מה הציונים שלך בקורסים קודמים או במבחנים רלוונטיים?</p>
           <label>א) מתמטיקה:</label>
<input type="number" name="math_grade" min="55" max="100" value="<?php echo $math_grade; ?>">
<label>ב) פיזיקה:</label>
<input type="number" name="physics_grade" min="55" max="100" value="<?php echo $physics_grade; ?>">
			<br>
		
            <br>
			<p>מספר יחידות </p>
			 <label>א) מתמטיקה:</label>
            <input type="number" name="Math_unit" min="3" max="5" value="<?php echo $Math_unit; ?>">
            <label>ב) פיזיקה:</label>
            <input type="number" name="Physics_unit" min="3" max="10" value="<?php echo $Physics_unit; ?>">
			<p>הערה: כדי להתקבל למסלול הנדסאים צריך ציון עובר עם 3 יחידות לפחות במתמטיקה<p>
        </div>
        <input type="submit" value="שלח">
    </form>
</div>
<?php
// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve values from form
    $q1 = $_POST['q1'] ?? '';
    $q2 = $_POST['q2'] ?? '';
    $q3 = $_POST['q3'] ?? '';
    $q4 = $_POST['q4'] ?? '';
    $q5 = $_POST['q5'] ?? '';
    $q6 = $_POST['q6'] ?? '';
    $q7 = $_POST['q7'] ?? '';
    $q8 = $_POST['q8'] ?? '';
    $Math_unit = isset($_POST['Math_unit']) ? $_POST['Math_unit'] : '';
    $Physics_unit = isset($_POST['Physics_unit']) ? $_POST['Physics_unit'] : '';
    $math_grade = isset($_POST['math_grade']) ? $_POST['math_grade'] : '';
    $physics_grade = isset($_POST['physics_grade']) ? $_POST['physics_grade'] : '';

    // Define response messages corresponding to each type of answer and their associated images
    $responses = array(
        "a" => array(
            "message" => "תודה רבה על השתתפותך בסקר שלנו. אנו מעריכים את הזמן שהקדשת למלא את השאלון. על פי התשובות שסיפקת, ניתן לראות שאתה מגלה עניין רב בפתרון בעיות ובחדשנות. נושא זה חשוב מאוד בעולם המודרני, שבו הטכנולוגיה והמדע מתפתחים במהירות. 
כנרה שאת מתחבר ל הנדסאי תוכנה , הנדסאי מכונות , הנדסאי מכשור רפואי ,הנדסאי רכב ,הנדסאי מים ,הנדסאי כימיה ,הנדסאי מכשור ובקרה
",
            "images" => array(
                "תוכנה.jpg",
                "מכונות.jpg",
                "מכשור רפואי.jpg",
                "רכב.jpg",
                "מים.jpg",
                "כימיה.jpg",
                "מכשור ובקרה.jpg"
            )
        ),
        "b" => array(
            "message" => "תודה רבה על השתתפותך בסקר שלנו. אנו מעריכים את הזמן והמאמץ שהקדשת למלא את השאלון. מהתשובות שסיפקת, ניתן להסיק שאתה מגלה עניין רב ביצירת טכנולוגיות חדשניות שמטרתן לשפר את איכות החיים של אנשים. נושא זה הוא קריטי בעולם שבו הטכנולוגיה ממלאת תפקיד מרכזי ומשפיעה על כל תחומי החיים,
כנרה שאת מתחבר ל הנדסאי בניין, הנדסאי מכונות , הנדסאי תוכנה ,הנדסאי חשמל ,הנדסאי בטכנולוגיה 

",
            "images" => array(
                "בניין.jpg",
                "מכונות.jpg",
                "תוכנה.jpg",
                "חשמל.jpg",
                "בטכנולוגיה.jpg",
            )
        ),
        "c" => array(
            "message" => "
תודה רבה על השתתפותך בסקר שלנו. אנו מעריכים את הזמן והמאמץ שהשקעת במענה על השאלות. מהתשובות שסיפקת ניתן לראות שאתה מחפש קריירה יציבה ומרתקת שתוכל לספק לך הן ביטחון כלכלי.
,נראה כי אתה מחפש מסלול מקצועי שיספק לך גם יציבות וגם עניין, בו תוכל להתמודד עם אתגרים חדשים ולפתח מיומנויות וכישורים נוספים לאורך הזמן. 
כנרה שאת מתחבר ל הנדסאי עצוב פנים, הנדסאי נוף , הנדסאי תעשייה,הנדסאיקירור",
            "images" => array(
                "עצוב פנים.jpg",
                "נוף.jpg",
                "תעשייה.jpg",
                "קירור.jpg"
            )
        )
    );

    // Example answers array (replace with actual user answers)
    $answers = array($q1, $q2, $q3, $q4, $q5, $q6, $q7, $q8);

    // Call function to generate response with images based on answers
    generate_response_with_images($answers, $responses, $Math_unit, $Physics_unit, $math_grade, $physics_grade);
}

// Function to generate response with images based on user answers
function generate_response_with_images($answers, $responses, $Math_unit, $Physics_unit, $math_grade, $physics_grade) {
    // Counting the number of responses for each type
    $count_a = $count_b = $count_c = 0;

    foreach ($answers as $answer) {
        if ($answer == "a") {
            $count_a++;
        } elseif ($answer == "b") {
            $count_b++;
        } elseif ($answer == "c") {
            $count_c++;
        }
    }

    // Checking which response type has the highest count and displaying corresponding message and images
    if ($count_a > $count_b && $count_a > $count_c && $math_grade >= 65 && $math_grade <= 85 && $physics_grade >= 65 && $physics_grade <= 85 && $Math_unit >= 3) {
        $response_type = "a";
    } elseif ($count_b > $count_a && $count_b > $count_c && $math_grade >= 85 && $math_grade <= 100 && $physics_grade >= 85 && $physics_grade <= 100 && $Physics_unit >= 3) {
        $response_type = "b";
    } elseif ($count_c > $count_a && $count_c > $count_b && $math_grade >= 55 && $math_grade <= 65 && $physics_grade >= 55 && $physics_grade <= 65) {
        $response_type = "c";
    } else {
        $response_type = "a"; // Default response if no specific conditions met
    }

    // Displaying the response message and images
    echo "<div class='response'>" . $responses[$response_type]["message"] . "</div>";
    echo "<div class='image-container  grid-container'>";
    foreach ($responses[$response_type]["images"] as $image) {
        echo "<div class='grid-item'><img src='$image' alt='תמונה מתאימה'></div>";
    }
    echo "</div>";
}
?>


</body>
</html>