<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'student_project_db');

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch projects for the logged-in user
// Fetch the user ID from the session
$user_id = $_SESSION['user_id'];

// Update the query to use 'user_id'
$sql = "SELECT * FROM projects WHERE user_id='$user_id'";
$result = $conn->query($sql);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Status</title>
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
            max-width: 800px;
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }
        button {
            padding: 12px 24px;
            background-color: #26a69a;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            margin: 10px 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            transition: background-color 0.3s, box-shadow 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Project Status</h2>

        <table>
            <tr>
                <th>Project Title</th>
                <th>Team Members</th>
                <th>Description</th>
                <th>External Remarks</th>
                <th>Assign Date</th>
                <th>Deadline Date</th>
            </tr>

            <?php
            // Display projects
            if ($result->num_rows > 0) {
                while ($project = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($project['title']) . "</td>
                            <td>" . htmlspecialchars($project['team_members']) . "</td>
                            <td>" . htmlspecialchars($project['description']) . "</td>
                            <td>" . htmlspecialchars($project['external_remarks'] ?? 'N/A') . "</td>
                            <td>" . htmlspecialchars($project['assign_date'] ?? 'N/A') . "</td>
                            <td>" . htmlspecialchars($project['deadline_date'] ?? 'N/A') . "</td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No projects submitted yet.</td></tr>";
            }
            ?>
        </table>
        <form action="report_submission.php" method="post">
            <button type="submit" class="reportSubmission">Sumbit Your Project</button>
        </form>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
