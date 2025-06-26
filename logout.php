<?php
session_start(); // Start the session

// Remove all session variables
session_unset();

// Destroy the session
session_destroy();

// Set a session variable for logout message
$_SESSION['logout_message'] = "You have been logged out successfully!";


// Redirect the user to the login page
header("Location: login.php");
exit();
?>
