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

// Fetch projects for review along with username
$sql = "SELECT p.*, u.username 
        FROM projects p 
        JOIN users u ON p.user_id = u.id 
        WHERE p.status = 'submitted'"; // Only get projects that are not approved yet
$result = $conn->query($sql);

// Handle project approval
if (isset($_POST['approve'])) {
    $project_id = $_POST['project_id'];
    $internal_remarks = mysqli_real_escape_string($conn, $_POST['internal_remarks']);
    $external_remarks = mysqli_real_escape_string($conn, $_POST['external_remarks']);
    $assign_date = $_POST['assign_date'];
    $deadline_date = $_POST['deadline_date'];

    // Update project status
    $update_sql = "UPDATE projects SET status='approved', 
                                       internal_remarks='$internal_remarks', 
                                       external_remarks='$external_remarks', 
                                       assign_date='$assign_date', 
                                       deadline_date='$deadline_date' 
                   WHERE id='$project_id'";

    if ($conn->query($update_sql) === TRUE) {
        echo "Project approved successfully!";
    } else {
        echo "Error: " . $update_sql . "<br>" . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Review</title>
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
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        label {
            font-weight: bold;
            color: #333;
            margin-top: 10px;
        }

        input[type="text"], input[type="date"] {
            padding: 8px;
            width: 100%;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }

        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 20px;
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
        <h2>Review Submitted Projects</h2>

        <table>
    <tr>
        <th>Name</th>
        <th>Project Title</th>
        <th>Team Members</th>
        <th>Description</th>
        <th>Actions</th>
    </tr>

    <?php
    if ($result->num_rows > 0) {
        while ($project = $result->fetch_assoc()) {
            echo "<tr>
                       <td>" . htmlspecialchars($project['username']) . "</td>
                    <td>" . htmlspecialchars($project['title']) . "</td>
                    <td>" . htmlspecialchars($project['team_members']) . "</td>
                    <td>" . htmlspecialchars($project['description']) . "</td>
                    <td>";
            // Check if the project is still submitted
            if ($project['status'] === 'submitted') {
                echo "<form method='POST'>
                        <input type='hidden' name='project_id' value='" . $project['id'] . "'>
                        <label for='internal_remarks'>Internal Remarks:</label>
                        <input type='text' name='internal_remarks' required>
                        <label for='external_remarks'>External Remarks:</label>
                        <input type='text' name='external_remarks' required>
                        <label for='assign_date'>Assign Date:</label>
                        <input type='date' name='assign_date' required>
                        <label for='deadline_date'>Deadline Date:</label>
                        <input type='date' name='deadline_date' required>
                        <button type='submit' name='approve'>Approve</button>
                    </form>";
            } else {
                echo "Approved"; // Show status if already approved
            }
            echo "</td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No projects to review.</td></tr>";
    }
    ?>
</table>


        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
