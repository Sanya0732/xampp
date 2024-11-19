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

// Handle file upload
if (isset($_POST['submit_report'])) {
    $project_id = $_POST['project_id'];
    $report_file = $_FILES['report_file'];
    $readme_file = $_FILES['readme_file'];
    $ppt_file = $_FILES['ppt_file'];

    // Define target directories
    $target_dir = "uploads/";
    $report_target = $target_dir . basename($report_file["name"]);
    $readme_target = $target_dir . basename($readme_file["name"]);
    $ppt_target = $target_dir . basename($ppt_file["name"]);

    // Move uploaded files to target directory
    move_uploaded_file($report_file["tmp_name"], $report_target);
    move_uploaded_file($readme_file["tmp_name"], $readme_target);
    move_uploaded_file($ppt_file["tmp_name"], $ppt_target);

    // Update the database with file paths and set status to 'submitted'
    $update_sql = "UPDATE projects SET report_file='$report_target', 
                                        readme_file='$readme_target', 
                                        ppt_file='$ppt_target', 
                                        status='submitted' 
                   WHERE id='$project_id'";

    if ($conn->query($update_sql) === TRUE) {
        echo "Report submitted successfully!";
    } else {
        echo "Error: " . $update_sql . "<br>" . $conn->error;
    }
}

// Fetch projects for the logged-in user where status is not 'submitted'
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM projects WHERE user_id='$user_id' AND status != 'submitted'";
$result = $conn->query($sql);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Submission</title>
    <!-- Styles remain the same -->
     <style>
/* Style for the submission container */
.submission-container {
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background-color: #f4f4f9;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    font-family: Arial, sans-serif;
}

/* Heading style */
.submission-container h2 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}

/* Table styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table, th, td {
    border: 1px solid #ddd;
}

th, td {
    padding: 10px;
    text-align: left;
}

th {
    background-color: #f2f2f2;
    color: #333;
}

tr:nth-child(even) {
    background-color: #fafafa;
}

/* Form and file input styling */
form {
    display: flex;
    flex-direction: column;
}

label {
    font-weight: bold;
    margin-top: 10px;
    color: #555;
}

input[type="file"] {
    margin-top: 5px;
    margin-bottom: 10px;
}

/* Button styling */
button[type="submit"] {
    padding: 8px 12px;
    background-color: #4CAF50;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

button[type="submit"]:hover {
    background-color: #45a049;
}

/* Link styling */
.back-link {
    display: inline-block;
    margin-top: 20px;
    color: #007bff;
    text-decoration: none;
    font-weight: bold;
    transition: color 0.3s;
}

.back-link:hover {
    color: #0056b3;
}

/* Responsive adjustments */
@media (max-width: 600px) {
    .submission-container {
        width: 90%;
        padding: 15px;
    }
    
    table, th, td {
        font-size: 14px;
    }
}


        </style>
</head>
<body>
    <div class="submission-container">
        <h2>Submit Project Report</h2>

        <table>
            <tr>
                <th>Project Title</th>
                <th>Actions</th>
            </tr>

            <?php
            // Display projects for report submission
            if ($result->num_rows > 0) {
                while ($project = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($project['title']) . "</td>
                            <td>
                                <form method='POST' enctype='multipart/form-data'>
                                    <input type='hidden' name='project_id' value='" . $project['id'] . "'>
                                    <label for='report_file'>Upload Report:</label>
                                    <input type='file' name='report_file' required>
                                    <label for='readme_file'>Upload README:</label>
                                    <input type='file' name='readme_file' required>
                                    <label for='ppt_file'>Upload PPT:</label>
                                    <input type='file' name='ppt_file' required>
                                    <button type='submit' name='submit_report'>Submit</button>
                                     
                                </form>
                            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No projects available for report submission.</td></tr>";
            }
            ?>
        </table>

        <a href="dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>


