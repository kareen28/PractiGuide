 <!DOCTYPE html>
<html lang="en">
<head>
 <style>
		
        /* Sidebar styling */
        .sidebar {
            width: 175px; /* Width of the sidebar */
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

        .logout-button-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 20px;
        }

        .logout-button-container button {
            background-color: #004d40; /* Dark Green */
            color: #fff;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .logout-button-container button:hover {
            background-color: #00796b; /* Darker Green on hover */
        }

	   </style>
</head>
<body>
<!-- Sidebar -->
    <div class="sidebar">
        <a href="mainadmin.php">
            <img src="p1.jpg" alt="לוגו" style="width:125px; height:75px; margin-left:10px;">
        </a>
        <a href="admin.php" class="w3-bar-item w3-button">גלריה קורסים</a>
        <a href="adminmahat.php" class="w3-bar-item w3-button">עריכת mahat</a>
        <a href="adminturnedtous.php" class="w3-bar-item w3-button">פניות המשתמשים</a>
        <a href="adminusers.php" class="w3-bar-item w3-button">חסימת משתמשים</a>
        
        <div class="logout-button-container">
            <form action="adminlogout.php" method="post">
                <button type="submit" name="logout" class="btn btn-secondary">התנתק</button>
            </form>
        </div>
    </div>