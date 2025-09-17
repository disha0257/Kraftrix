<?php
$servername = "localhost";
$username   = "root";   // default XAMPP user
$password   = "";       // default XAMPP password is empty
$database   = "db";     // your database name
$port       = 3307;     // your MySQL port

// Create connection
$conn = new mysqli($servername, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
