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

// Fetch user role
$username = $_SESSION['username'];
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Check role
$role_owner = $user['role_owner'];
$roles = json_decode($user['roles'], true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        /* Reset */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* Body styling */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #e0f7fa, #80deea);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        /* Container for content */
        .dashboard-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 600px;
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        h3 {
            color: #444;
            margin: 15px 0;
        }

        /* List styling */
        ul {
            list-style-type: none;
            padding: 0;
            margin: 10px 0;
        }

        li {
            padding: 10px;
            background-color: #f1f1f1;
            border-radius: 5px;
            margin-bottom: 10px;
            transition: background-color 0.3s;
        }

        li:hover {
            background-color: #e0e0e0;
        }

        /* Form table styling */
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f1f1f1;
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

        /* Link styling */
        a {
            text-decoration: none;
            color: #26a69a;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Logout button styling */
        .logout {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .logout:hover {
            background-color: #d32f2f;
        }

        /* Responsive adjustments */
        @media (max-width: 500px) {
            .dashboard-container {
                padding: 20px;
            }

            h2 {
                font-size: 24px;
            }

            h3 {
                font-size: 20px;
            }

            .logout {
                padding: 8px 16px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
        
        <h3>Your Roles:</h3>
        <ul>
            <?php foreach ($roles as $role): ?>
                <li><?php echo htmlspecialchars($role); ?></li>
            <?php endforeach; ?>
        </ul>

        <h3>Actions:</h3>
        <ul>
            <?php if ($role_owner === 'student'): ?>
                <li><a href="project_submission.php">Submit Project</a></li>
            <?php elseif ($role_owner === 'faculty'): ?>
                <li><a href="project_review.php">Review Projects</a></li>
            <?php endif; ?>
        </ul>

        <!-- Project Submission Form Table -->
 

        <form action="logout.php" method="post">
            <button type="submit" class="logout">Logout</button>
        </form>

        <form action="login.php" method="post">
            <button type="submit" class="login">Back</button>
        </form>
       
        <ul>
            <?php if ($role_owner === 'student'): ?>
                <li><a href="project_status.php"> Project Status</a></li>
            <?php elseif ($role_owner === 'faculty'): ?>
                <li><a href="review_projects.php">Review Projects</a></li>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>
