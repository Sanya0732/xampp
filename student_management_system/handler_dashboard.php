<?php
// Database connection
include('db_connection.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Handler') {
    echo "Access denied.";
    exit;
}

// Fetch all pending correction requests
try {
    $stmt = $pdo->prepare("
        SELECT 
            r.request_id, 
            r.field_name, 
            r.requested_value, 
            r.status, 
            r.date_requested, 
            s.roll_no, 
            s.name 
        FROM 
            update_request r 
        JOIN 
            student s 
        ON 
            r.student_id = s.student_id 
        WHERE 
            r.status = 'Pending'
    ");
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Handler Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }

        h2 {
            text-align: center;
            color: #555;
            margin: 20px 0;
        }

        table {
            width: 90%;
            margin: 0 auto 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e8f5e9;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        form {
            display: inline-block;
        }

        p {
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>
    <h1>Handler Dashboard</h1>
    <h2>Pending Correction Requests</h2>

    <?php if (!empty($requests)) : ?>
        <table>
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Roll Number</th>
                    <th>Student Name</th>
                    <th>Field Name</th>
                    <th>Requested Value</th>
                    <th>Date Requested</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request) : ?>
                    <tr>
                        <td><?= htmlspecialchars($request['request_id']) ?></td>
                        <td><?= htmlspecialchars($request['roll_no']) ?></td>
                        <td><?= htmlspecialchars($request['name']) ?></td>
                        <td><?= htmlspecialchars($request['field_name']) ?></td>
                        <td><?= htmlspecialchars($request['requested_value']) ?></td>
                        <td><?= htmlspecialchars($request['date_requested']) ?></td>
                        <td>
                            <form method="POST" action="process_request.php">
                                <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
                                <button type="submit" name="action" value="approve">Approve</button>
                                <button type="submit" name="action" value="reject" style="background-color: #f44336;">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No pending requests.</p>
    <?php endif; ?>
</body>
</html>
