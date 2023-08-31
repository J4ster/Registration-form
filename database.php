<?php
// database connection
$hostname = "localhost"; // Enter your hostname
$username = "root";     // Enter your database username
$password = "";         // Enter your database password
$databasename = "registration"; // Enter your database name

// Create connection
$conn = new mysqli($hostname, $username, $password, $databasename);

// Check connection
if ($conn->connect_error) {
    die("Unable to connect to the database: " . $conn->connect_error);
}
?>

