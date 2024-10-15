<?php
session_start();

// Destroy the session
session_destroy();

// Redirect to login.php
header("Location: login.php");
exit(); // Ensure that no further code is executed after the redirect
?>
