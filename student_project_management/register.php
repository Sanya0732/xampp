<?php
session_start(); // Start the session at the top of the file

// Check if the form is submitted
if (isset($_POST['register'])) {
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'student_project_db');

    // Check for connection error
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve and sanitize form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role_owner = mysqli_real_escape_string($conn, $_POST['role_owner']);
    $roles = isset($_POST['roles']) ? $_POST['roles'] : [];

    // Check for additional roles entered by the user
    if (!empty($_POST['additional_roles'])) {
        $additional_roles = explode(',', $_POST['additional_roles']);
        $roles = array_merge($roles, array_map('trim', $additional_roles));
    }

    // Convert roles array to JSON for storing in the database
    $roles_json = json_encode($roles);

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into the database
    $sql = "INSERT INTO users (username, password, role_owner, roles) 
            VALUES ('$username', '$hashed_password', '$role_owner', '$roles_json')";

    if ($conn->query($sql) === TRUE) {
        echo "Registration successful! You can now <a href='login.php'>login</a>.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the database connection
    $conn->close();
}

// Logout functionality
if (isset($_POST['login'])) {
    session_unset(); // Clear session variables
    session_destroy(); // Destroy the session
    header("Location: login.php"); // Redirect to login page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
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
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        /* Form container */
        .form-container {
            background-color: #ffffff;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .form-container:hover {
            transform: translateY(-5px);
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        /* Input and select styling */
        label {
            font-size: 14px;
            color: #555;
            margin-top: 10px;
            display: inline-block;
            text-align: left;
            width: 100%;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="password"],
        input[type="submit"],
        select,
        #additional_roles {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            background-color: #f9f9f9;
            transition: border 0.3s;
        }

        input:focus {
            border: 1px solid #4CAF50;
            outline: none;
            box-shadow: 0 0 8px rgba(76, 175, 80, 0.2);
        }

        /* Checkbox styling */
        .checkbox-label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
        }

        /* Button styling */
        button {
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            margin-top: 10px;
            transition: background-color 0.3s, transform 0.2s;
        }

        button[name="logout"] {
            background-color: #f44336; /* Red for the logout button */
        }

        button:hover {
            background-color: #45a049;
        }

        button:active {
            transform: scale(0.98);
        }

        /* Link styling */
        a {
            color: #4CAF50;
            text-decoration: none;
        }

        /* Additional roles input styling */
        #additional_roles {
            font-size: 15px;
            color: #555;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
            background-color: #f9f9f9;
        }

        /* Responsive adjustments */
        @media (max-width: 500px) {
            .form-container {
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
    <div class="form-container">
        <h2>User Registration</h2>
        <form action="register.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" >

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" >

            <label for="role_owner">Role Owner:</label>
            <select name="role_owner" id="role_owner" >
                <option value="student">Student</option>
                <option value="faculty">Faculty</option>
            </select>

            <label class="checkbox-label">Select Roles:</label>
            <input type="checkbox" name="roles[]" value="student_coordinator"> Student Coordinator<br>
            <input type="checkbox" name="roles[]" value="project_invigilator"> Project Invigilator<br>
            <input type="checkbox" name="roles[]" value="dean"> Dean<br>

            <p>If these are not your roles, enter additional roles separated by commas:</p>
            <input type="text" id="additional_roles" name="additional_roles" placeholder="e.g., Librarian, Counselor">

            <button type="submit" name="register">Register</button>
            <p>If already have a account</p>
            <button type="submit" name="login">Log In</button>


        </form>
    </div>
</body>
</html>
