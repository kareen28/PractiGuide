<?php
include 'sidebadmin.php';
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mahat</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color:#ccccff;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
			      margin-right: 20px; /* מרווח מימין */
        }
        h2 {
            text-align: center;
            color: #343a40;
        }
        form {
            margin-bottom: 20px;
        }
        textarea, input[type="submit"] {
            width: 100%;
            margin-bottom: 10px;
            padding: 12px;
            box-sizing: border-box;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 16px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .product-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .product-item {
            width: 300px;
            margin: 10px;
            padding: 20px;
            background-color: #f1f1f1;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .product-item p {
            margin-bottom: 15px;
            font-size: 18px;
            color: #495057;
        }
        .product-item form {
            margin-top: 10px;
        }
        .product-item form input[type="submit"] {
            background-color: #dc3545;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .product-item form input[type="submit"]:hover {
            background-color: #c82333;
        }
        .product-item form input[type="hidden"] {
            display: none;
        }

 
    </style>
		
</head>
<body>



<div class="container">
    <h2>Upload information</h2>
    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" enctype="multipart/form-data">
        <textarea name="product_description" placeholder="information Description" required></textarea><br>
        <input type="submit" value="Upload information" name="submit">
    </form>

    <h2>information</h2>
    <div class="product-container">
        <?php
        // Establish connection to MySQL database
        $conn = new mysqli("localhost", "root", "", "mydatabase");
        if($conn->connect_error){
            die("Connection failed: " . $conn->connect_error);
        }

        // Create table for products if not exists
        $createTableQuery = "CREATE TABLE IF NOT EXISTS mahat (
            id INT AUTO_INCREMENT PRIMARY KEY,
            description TEXT
        )";
        if ($conn->query($createTableQuery) === FALSE) {
            echo "Error creating table: " . $conn->error;
        }

        // Check if the delete button is clicked
        if(isset($_POST['delete_image'])) {
            $product_id = $_POST['product_id'];
            // Delete product from database
            $delete_query = "DELETE FROM mahat WHERE id = '$product_id'";
            if ($conn->query($delete_query) === TRUE) {
                echo "information deleted successfully.";
            } else {
                echo "Error deleting product: " . $conn->error;
            }
        }

        // Check if the edit button is clicked
        if(isset($_POST['edit_submit'])) {
            $product_id = $_POST['product_id'];
            $edit_product_description = $_POST['edit_product_description'];

            // Construct the update query based on the provided fields
            $update_query = "UPDATE mahat SET description = '$edit_product_description' WHERE id = '$product_id'";

            // Execute the update query
            if ($conn->query($update_query) === TRUE) {
                echo "Product updated successfully.";
            } else {
                echo "Error updating information: " . $conn->error;
            }
        }

        // Check if the form was submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
            // Get product description
            $product_description = $_POST['product_description'];

            // Insert product data into database
            $insert = $conn->prepare("INSERT INTO mahat (description) VALUES (?)");
            if ($insert) {
                $insert->bind_param("s", $product_description);
                if ($insert->execute()) {
                    // Product uploaded successfully, reload the page to display the new product
                    echo '<script>window.location = window.location.href;</script>';
                } else {
                    echo "Error inserting data into database: " . $conn->error;
                }
            } else {
                echo "Error preparing statement: " . $conn->error;
            }
        }

        // Retrieve products from the database
        $result = $conn->query("SELECT * FROM mahat");
        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                echo '<div class="product-item">';
                echo '<p> ' . $row['description'] . '</p>';
                echo '<form method="post">';
                echo '<input type="hidden" name="product_id" value="' . $row['id'] . '">';
                echo '<input type="submit" name="delete_image" value="Delete">';
                echo '</form>';
                echo '<form method="post">';
                echo '<input type="hidden" name="product_id" value="' . $row['id'] . '">';
                echo '<textarea name="edit_product_description" placeholder="New information Description" required></textarea><br>';
                echo '<input type="submit" value="Edit information" name="edit_submit">';
                echo '</form>';
                echo '</div>';
            }
        } else {
            echo "No products found.";
        }

        $conn->close();
        ?>
    </div>
</div>
</body>
</html>
