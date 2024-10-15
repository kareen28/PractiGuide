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
    <title>courses Gallery</title>
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color:#ccccff;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        h2 {
            text-align: center;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="file"], input[type="text"], textarea, input[type="submit"] {
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
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
            width: 250px;
            margin: 10px;
            padding: 10px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .product-item img {
            width: 100%;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .product-item p {
            margin-bottom: 5px;
        }
        .product-item form {
            margin-top: 10px;
        }
        .product-item form input[type="submit"] {
            background-color: #dc3545;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .product-item form input[type="submit"]:hover {
            background-color: #c82333;
        }


  
    </style>

</head>
<body>


    <div class="container">
        <h2>Upload courses</h2>
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" enctype="multipart/form-data">
            <input type="file" name="product_image" required><br>
            <input type="text" name="product_name" placeholder="Product Name" required><br>
            <textarea name="product_description" placeholder="Product Description"></textarea><br>
            <input type="submit" value="Upload Product" name="submit">
        </form>

        <h2>courses Gallery</h2>
        <div class="product-container">
            <?php
// Establish connection to MySQL database
$conn = new mysqli("localhost", "root", "", "mydatabase");
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

// Create table for products if not exists
$createTableQuery = "CREATE TABLE IF NOT EXISTS info (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    image_url VARCHAR(255)
)";
if ($conn->query($createTableQuery) === FALSE) {
    echo "Error creating table: " . $conn->error;
}

// Check if the delete button is clicked
if(isset($_POST['delete_image'])) {
    $product_id = $_POST['product_id'];
    // Delete product from database
    $delete_query = "DELETE FROM info WHERE id = '$product_id'";
    if ($conn->query($delete_query) === TRUE) {
        echo "Product deleted successfully.";
    } else {
        echo "Error deleting product: " . $conn->error;
    }
}

// Check if the edit button is clicked
if(isset($_POST['edit_submit'])) {
    $product_id = $_POST['product_id'];
    $edit_product_name = $_POST['edit_product_name'];
    $edit_product_description = $_POST['edit_product_description'];

    // Construct the update query based on the provided fields
    $update_query = "UPDATE info SET ";
    $update_fields = array();
    if (!empty($edit_product_name)) {
        $update_fields[] = "name = '$edit_product_name'";
    }
    if (!empty($edit_product_description)) {
        $update_fields[] = "description = '$edit_product_description'";
    }
    // Add the fields to the update query
    $update_query .= implode(", ", $update_fields);
    $update_query .= " WHERE id = '$product_id'";

    // Execute the update query
    if ($conn->query($update_query) === TRUE) {
        echo "Product updated successfully.";
    } else {
        echo "Error updating product: " . $conn->error;
    }
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["product_image"])) {
    // Check if file was uploaded without errors
    if ($_FILES["product_image"]["error"] == 0) {
        // Get temporary location of the file
        $product_image_tmp = $_FILES['product_image']['tmp_name'];
        $product_name = $_POST['product_name'];
        $product_description = $_POST['product_description'];

        // Move uploaded file to a directory within the project
        $target_directory = "uploads/";
        $target_file = $target_directory . basename($_FILES["product_image"]["name"]);
        $file_extension = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
        } else {
            // Check if the file is a valid image file
            $check = getimagesize($product_image_tmp);
            if($check !== false) {
                // Check file size (limit 5MB)
                if ($_FILES["product_image"]["size"] > 5000000) {
                    echo "Sorry, your file is too large.";
                } else {
                    // Allow only certain file formats
                    if($file_extension != "jpg" && $file_extension != "png" && $file_extension != "jpeg"
                    && $file_extension != "gif" ) {
                        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                    } else {
                        // Attempt to move the uploaded file
                        if (move_uploaded_file($product_image_tmp, $target_file)) {
                            // Insert product data into database
                            $insert = $conn->prepare("INSERT INTO info (name, description, image_url) VALUES (?, ?, ?)");
                            if ($insert) {
                                $insert->bind_param("sss", $product_name, $product_description, $target_file);
                                if ($insert->execute()) {
                                    // Product uploaded successfully, reload the page to display the new product
                                    echo '<script>window.location = window.location.href;</script>';
                                } else {
                                    echo "Error inserting data into database: " . $conn->error;
                                }
                            } else {
                                echo "Error preparing statement: " . $conn->error;
                            }
                        } else {
                            echo "Sorry, there was an error uploading your file.";
                        }
                    }
                }
            } else {
                echo "File is not an image.";
            }
        }
    } else {
        echo "File upload error: " . $_FILES["product_image"]["error"];
    }
}

// Retrieve products from the database
$result = $conn->query("SELECT * FROM info");
if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        $product_id = $row['id'];
        echo '<div class="product-item">';
        echo '<img src="' . $row['image_url'] . '" width="200" /><br>';
        echo '<p>Name: ' . $row['name'] . '</p>';
        echo '<p>Description: ' . $row['description'] . '</p>';
        echo '<form method="post">';
        echo '<input type="hidden" name="product_id" value="'.$product_id.'">';
        echo '<input type="submit" name="delete_image" value="Delete">';
        echo '</form>';
        echo '<form method="post">';
        echo '<input type="hidden" name="product_id" value="'.$product_id.'">';
        echo '<input type="text" name="edit_product_name" placeholder="New Product Name"><br>';
        echo '<textarea name="edit_product_description" placeholder="New Product Description"></textarea><br>';
        echo '<input type="submit" value="Edit Product" name="edit_submit">';
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
