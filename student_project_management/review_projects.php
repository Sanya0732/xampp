<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['username']) || $_SESSION['role_owner'] !== 'faculty') {
    header('Location: login.php');
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'student_project_db');

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle review submission
if (isset($_POST['submit_review'])) {
    $project_id = $_POST['project_id'];
    $internal_remarks = $conn->real_escape_string($_POST['internal_remarks']);
    $external_remarks = $conn->real_escape_string($_POST['external_remarks']);
    $grade = (int)$_POST['grade'];

    // Update the project with review details
    $update_sql = "UPDATE projects SET internal_remarks='$internal_remarks', 
                                        external_remarks='$external_remarks', 
                                        grade='$grade', 
                                        status='evaluated' 
                   WHERE id='$project_id'";

    if ($conn->query($update_sql) === TRUE) {
        echo "<div class='alert success'>Review submitted successfully!</div>";
    } else {
        echo "<div class='alert error'>Error: " . $conn->error . "</div>";
    }
}

// Fetch all projects submitted for review
$sql = "SELECT * FROM projects WHERE status='submitted'";
$result = $conn->query($sql);

if (!$result) {
    die("Error executing query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Projects</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            display: flex;
            justify-content: center;
            padding-top: 40px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        table {
            width: 90%;
            border-collapse: collapse;
            margin: 0 auto 20px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: #fff;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 10px;
        }

        textarea, input[type="number"] {
            width: 90%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical;
        }

        button[type="submit"] {
            padding: 10px 15px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #218838;
        }

        .alert {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            display: none;
        }

        .alert.success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        a[href="dashboard.php"] {
            display: block;
            text-align: center;
            color: #4CAF50;
            font-weight: bold;
            margin-top: 20px;
            text-decoration: none;
        }

        a[href="dashboard.php"]:hover {
            color: #218838;
        }

        @media (max-width: 768px) {
            table {
                width: 100%;
            }

            textarea, input[type="number"] {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <h2>Review Submitted Projects</h2>

    <table>
        <tr>
            <th>Project Title</th>
            <th>Team Members</th>
            <th>Report</th>
            <th>README</th>
            <th>PPT</th>
            <th>Actions</th>
        </tr>

        <?php
        // Display submitted projects for review
        if ($result->num_rows > 0) {
            while ($project = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($project['title']) . "</td>
                        <td>" . htmlspecialchars($project['team_members']) . "</td>
                        <td><a href='" . htmlspecialchars($project['report_file']) . "' target='_blank'>View</a></td>
                        <td><a href='" . htmlspecialchars($project['readme_file']) . "' target='_blank'>View</a></td>
                        <td><a href='" . htmlspecialchars($project['ppt_file']) . "' target='_blank'>View</a></td>
                        <td>
                            <form method='POST'>
                                <input type='hidden' name='project_id' value='" . $project['id'] . "'>
                                <textarea name='internal_remarks' placeholder='Internal Remarks' required></textarea>
                                <textarea name='external_remarks' placeholder='External Remarks' required></textarea>
                                <input type='number' name='grade' placeholder='Grade' required min='0' max='100'>
                                <button type='submit' name='submit_review'>Submit Review</button>
                            </form>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No projects available for review.</td></tr>";
        }
        ?>
    </table>

    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
