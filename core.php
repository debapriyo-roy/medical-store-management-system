<?php 

session_start();

// Link to the PHP connection script
require_once 'connection.php'; 

// If there is no active user session, redirect to the login page
if(!isset($_SESSION['userId'])) {
    header('location: index.php'); 
    exit(); // Always add exit() after a header redirect to stop the script completely
} 

?>