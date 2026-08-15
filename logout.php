<?php
session_start();
session_unset();    // Clears the session data
session_destroy();  // Destroys the session completely

// Redirects back to the login page
header("Location: index.php");
exit();
?>