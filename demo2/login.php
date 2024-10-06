<?php
session_start();
$connection = new mysqli("localhost", "root", "", "students_db");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $connection->real_escape_string($_POST['username']);
    $password = $connection->real_escape_string($_POST['password']);

    // Hardcoded credentials
    $credentials = [
        'level1' => 'level1',
        'level2' => 'level2',
        'level3' => 'level3',
        'level4' => 'level4'
    ];

    if (isset($credentials[$username]) && $credentials[$username] == $password) {
        $_SESSION['role'] = $username;
        $_SESSION['username'] = $username;

        // Redirect based on user level
        switch ($username) {
            case 'level1':
                header("Location: level1.php");
                break;
            case 'level2':
                header("Location: level2.php");
                break;
            case 'level3':
                header("Location: level3.php");
                break;
            case 'level4':
                header("Location: level4.php");
                break;
            default:
                echo "Invalid Level!";
                break;
        }
        exit();
    } else {
        $error_message = "Invalid Username or Password!";
    }
}

$connection->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
  body {
            font-family: Arial, sans-serif;
            background-color: #f4efe8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        form {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        h2 {
            color: #8b7d6b;
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #8b7d6b;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #726352;
        }    </style>
</head>
<body>
    <form method="POST" action="login.php">
        <h2>Login</h2>
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
    </form>
</body>
</html>
