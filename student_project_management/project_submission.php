<?php
// Database connection
$servername = "localhost";
$username = "root"; // Change if you have a different username
$password = ""; // Change if you have a different password
$dbname = "student_project_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session to retrieve user_id
session_start();
$user_id = $_SESSION['user_id']; // Ensure this is set during login

// Check if form data is set
if (isset($_POST['project_title'], $_POST['team_members'], $_POST['description'])) {
    $project_title = $_POST['project_title'];
    $team_members = $_POST['team_members'];
    $description = $_POST['description'];

    // Ensure user_id is available and not null
    if (!$user_id) {
        echo "User ID is not set!";
        exit();
    }

    // Prepare and bind
    $sql_insert = "INSERT INTO projects (title, team_members, description, user_id) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);

    if ($stmt_insert) { // Check if preparation was successful
        $stmt_insert->bind_param("sssi", $project_title, $team_members, $description, $user_id);

        if ($stmt_insert->execute()) {
            echo "Project submitted successfully!";
        } else {
            echo "Error: " . $stmt_insert->error; // Show detailed error information
        }

        $stmt_insert->close(); // Close the statement
    } else {
        echo "Error preparing the statement: " . $conn->error; // Show error preparing statement
    }
} else {
    echo "Form data is missing!";
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Project</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        
        .container {
            background-color: #fff;
            padding: 20px;
            max-width: 500px;
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        h2 {
            text-align: center;
            color: #333;
        }
        
        label {
            font-weight: bold;
            color: #333;
            display: block;
            margin-top: 10px;
        }
        
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 1rem;
        }
        
        textarea {
            resize: vertical;
            height: 100px;
        }
        
        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            margin-top: 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 1rem;
        }
        
        button[type="submit"]:hover {
            background-color: #45a049;
        }
        
        a {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #007BFF;
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Submit Your Project</h2>
        <form action="project_submission.php" method="POST">
            <label for="project_title">Project Title:</label>
            <input type="text" id="project_title" name="project_title" required>

            <label for="team_members">Project Team Members:</label>
            <input type="text" id="team_members" name="team_members" required>

            <label for="description">Project Description:</label>
            <textarea id="description" name="description" required></textarea>

            <button type="submit" name="submit_project">Submit Project</button>
        </form>

        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
