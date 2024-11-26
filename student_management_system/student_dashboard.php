<?php
session_start();
require 'db_connection.php';

if ($_SESSION['role'] !== 'Student') {
    header('Location: login.php');
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM student WHERE student_id = ?");
$stmt->execute([$user_id]);
$student = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $field_name = $_POST['field_name'];
    $requested_value = $_POST['requested_value'];

    $stmt = $pdo->prepare("INSERT INTO update_request (student_id, field_name, requested_value) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $field_name, $requested_value]);
    echo "<div class='success-message'>Request submitted.</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f5;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 900px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .student-info {
            margin: 20px 0;
        }

        .student-info p {
            font-size: 16px;
            color: #555;
        }

        form {
            margin-top: 30px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #555;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .success-message {
            text-align: center;
            color: green;
            font-size: 16px;
            margin-top: 20px;
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($student['name']); ?></h1>

        <div class="student-info">
            <p>Roll Number: <?php echo htmlspecialchars($student['roll_no']); ?></p>
            <p>Email: <?php echo htmlspecialchars($student['email']); ?></p>
            <!-- Add other fields as needed -->
        </div>

        <h2>Request Correction</h2>
        <form method="POST">
            <label for="field_name">Field Name:</label>
            <input type="text" name="field_name" id="field_name" required>
            
            <label for="requested_value">Requested Value:</label>
            <input type="text" name="requested_value" id="requested_value" required>
            
            <button type="submit">Submit</button>
        </form>

        <div class="footer">
            <p>&copy; 2024 Student Management System</p>
        </div>
    </div>
</body>
</html>
