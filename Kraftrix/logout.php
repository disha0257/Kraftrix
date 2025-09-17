<?php
session_start();

// Clear all session data
$_SESSION = [];

// Destroy session
session_destroy();

// Redirect to login with message
header("Location: login.php?message=loggedout");
exit;
