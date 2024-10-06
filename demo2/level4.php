<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] != "level4") {
    header("Location: login.php");
    exit();
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['approve'])) {
    $roll_number = $_POST['roll_number'];

    // Sanitize inputs to prevent SQL injection
    $roll_number = $conn->real_escape_string($roll_number);

    // Approve the stuck-off request
    $sql = "UPDATE students SET dean_approval='Approved' WHERE roll_number='$roll_number'";
    if ($conn->query($sql) === TRUE) {
        $success_message = "Student approved successfully!";
    } else {
        $error_message = "Error: " . $conn->error;
    }
}

// Fetch all student data with comments
$sql = "SELECT * FROM students";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dean Academic - Approval and Comments</title>
    <style>
        /* Existing styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h2 {
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        form {
            margin-top: 10px;
        }
        input[type="submit"] {
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
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .alert {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-weight: bold;
            text-align: center;
        }
        .alert.success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2>Dean Academic - Approval and Comments</h2>

    <?php if ($success_message): ?>
        <div class="alert success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <table>
        <tr>
            <th>Roll Number</th>
            <th>Dealing Hand Comment</th>
            <th>Section Incharge Comment</th>
            <th>Deputy Registrar Comment</th>
            <th>Dean Approval</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['roll_number']); ?></td>
                <td><?php echo htmlspecialchars($row['level1_comment']); ?></td>
                <td><?php echo htmlspecialchars($row['level2_comment']); ?></td>
                <td><?php echo htmlspecialchars($row['level3_comment']); ?></td>
                <td>
                    <?php if (isset($row['dean_approval']) && $row['dean_approval'] == 'Approved') {
                        echo "Approved";
                    } else { ?>
                        <form method="POST" action="level4.php">
                            <input type="hidden" name="roll_number" value="<?php echo htmlspecialchars($row['roll_number']); ?>">
                            <input type="submit" name="approve" value="Approve">
                        </form>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>

    <form method="POST" action="generate_pdf.php">
        <input type="submit" value="Download as PDF">
    </form>

    <a href="login.php" class="logout-btn">Logout</a>
</body>
</html>
