<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "exam_attendance";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roll_no = $_POST['roll_no'];

    // Update the attendance status to 'Present'
    $sql = "UPDATE students SET attendance_status = 'Present' WHERE roll_no = '$roll_no'";

    if ($conn->query($sql) === TRUE) {
        // Redirect back to index.html with success status
        header("Location: index.html?status=success&roll_no=$roll_no");
        exit;
    } else {
        // Redirect back to index.html with error status
        header("Location: index.html?status=error");
        exit;
    }
}

$conn->close();
?>
