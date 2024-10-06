<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != "level3") {
    header("Location: login.php");
    exit();
}

$success_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['student_id'];
    $comment = $_POST['comment'];

    // Sanitize inputs to prevent SQL injection
    $id = intval($id);
    $comment = $conn->real_escape_string($comment);

    // Update the students table with the Deputy Registrar's comments
    $query = "UPDATE students SET level3_comment='$comment' WHERE id=$id";
    if ($conn->query($query)) {
        $success_message = "Comment added successfully!";
    } else {
        $success_message = "Error: " . $conn->error;
    }
}

// Fetch all student data with previous comments
$result = $conn->query("SELECT * FROM students");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Deputy Registrar - Add Comments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .alert {
            padding: 10px;
            background-color: #d4edda;
            color: #155724;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        textarea {
            width: 100%;
            height: 60px;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button,input[type="submit"], button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .logout-btn {
    background-color: #FF6F61; /* Coral color for a more prominent button */
    color: white;
    border: none;
    padding: 10px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 10px 0;
    cursor: pointer;
    border-radius: 4px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s, transform 0.3s;
}

.logout-btn:hover {
    background-color: #FF4D4D; /* Slightly darker shade on hover */
    transform: scale(1.05); /* Slight zoom effect on hover */
}

.logout-btn:active {
    background-color: #FF2D2D; /* Even darker shade when button is pressed */
    transform: scale(0.95); /* Slight shrink effect on press */
}
    </style>
</head>
<body>
    <div class="container">
        <h2>Deputy Registrar - Add Comments</h2>

        <?php if ($success_message): ?>
            <div class="alert"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
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
                <th>Level 3 Comment</th>
            </tr>

            <?php
            $result = $conn->query("SELECT * FROM students");
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row['roll_number'] . "</td><td>" . $row['level1_comment'] . "</td><td>" . $row['level2_comment'] . "</td><td>" . $row['level3_comment'] . "</td></tr>";
            }
            ?>
        </table>

        <br>
        <a href="login.php" class="logout-btn">Logout</a>
    </div>
</body>
</html>
