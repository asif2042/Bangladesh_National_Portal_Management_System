<?php





// Database configuration
$host = 'localhost';
$username = 'root'; // Replace with your MySQL username
$password = '';     // Replace with your MySQL password
$dbname = 'bangladesh_national_portal';     // Database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
