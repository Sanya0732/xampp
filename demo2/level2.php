<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != "level2") {
    header("Location: login.php");
    exit();
}

$success_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $conn->real_escape_string($_POST['student_id']);
    $comment = $conn->real_escape_string($_POST['comment']);

    $query = "UPDATE students SET level2_comment='$comment' WHERE id=$student_id";
    if ($conn->query($query)) {
        $success_message = "Comment added successfully!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Level 2 - Section Incharge</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        h2 {
            color: #4a4a4a;
            text-align: center;
        }

        form {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
            margin: 20px;
            position: relative;
        }

        select,
        textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #5c6bc0;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #3949ab;
        }

        .alert {
            display: none;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            color: #fff;
            background-color: #4caf50;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }

        .alert.show {
            display: block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #5c6bc0;
            color: white;
        }

        td {
            color: #4a4a4a;
        }

        a {
            text-decoration: none;
            color: white;
            background-color: #f44336;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            transition: background-color 0.3s ease;
            text-align: center;
        }

        a:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Level 2 - Section Incharge</h2>
        <?php if ($success_message): ?>
            <div class="alert show"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <form method="POST" action="level2.php">
            <select name="student_id" required>
                <?php
                $result = $conn->query("SELECT * FROM students");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['roll_number'] . "</option>";
                }
                ?>
            </select><br>
            <textarea name="comment" placeholder="Comment" required></textarea><br>
            <button type="submit">Submit</button>
        </form>
        <br>
        <h3>Stored Data</h3>
        <table>
            <tr>
                <th>Roll Number</th>
                <th>Level 1 Comment</th>
                <th>Level 2 Comment</th>
            </tr>
            <?php
            $result = $conn->query("SELECT * FROM students");
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row['roll_number'] . "</td><td>" . $row['level1_comment'] . "</td><td>" . $row['level2_comment'] . "</td></tr>";
            }
            ?>
        </table>
        <br>
        <a href="login.php">Logout</a>
    </div>
</body>
</html>
