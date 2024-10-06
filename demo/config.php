<?php
$host = 'localhost';
$db = 'expense_tracker';
$user = 'root';  // Default XAMPP user
$pass = '';      // Default XAMPP password is empty

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
