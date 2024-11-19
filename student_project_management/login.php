<?php
session_start();

// Check if the form is submitted
if (isset($_POST['login'])) {
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'student_project_db');

    // Check for connection error
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve and sanitize form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to fetch user data
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Store user info in session
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_owner'] = $user['role_owner'];
            $_SESSION['roles'] = json_decode($user['roles'], true);
            $_SESSION['user_id'] = $user['id']; // Add this line

            // Redirect to a dashboard or welcome page
            header('Location: dashboard.php');
            exit();
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "No user found with that username!";
    }

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* Reset */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* General body styling */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #e0f7fa, #80deea);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        /* Form container */
        .login-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .login-container:hover {
            transform: translateY(-5px);
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        /* Input styling */
        label {
            font-size: 14px;
            color: #555;
            margin-top: 10px;
            display: inline-block;
            width: 100%;
            text-align: left;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background-color: #f9f9f9;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        input:focus {
            border-color: #26a69a;
            outline: none;
            box-shadow: 0 0 8px rgba(38, 166, 154, 0.2);
        }

        /* Button styling */
        button {
            padding: 12px;
            background-color: #26a69a;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            margin-top: 15px;
            transition: background-color 0.3s, transform 0.2s;
        }

        button:hover {
            background-color: #2bbbad;
        }

        button:active {
            transform: scale(0.98);
        }

        /* Responsive adjustments */
        @media (max-width: 500px) {
            .login-container {
                padding: 20px;
            }

            h2 {
                font-size: 20px;
            }

            button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>
