<?php
// Database connection
include('db_connection.php');
session_start();

// Ensure the user is a Handler
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Handler') {
    echo "Access denied.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['action'])) {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    try {
        // Fetch the requested correction details
        $stmt = $pdo->prepare("
            SELECT 
                r.request_id, 
                r.student_id, 
                r.field_name, 
                r.requested_value 
            FROM 
                update_request r 
            WHERE 
                r.request_id = :request_id
        ");
        $stmt->execute(['request_id' => $request_id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$request) {
            echo "Invalid request.";
            exit;
        }

        if ($action === 'approve') {
            // Update the student's data
            $field_name = $request['field_name'];
            $requested_value = $request['requested_value'];
            $student_id = $request['student_id'];

            // Dynamic field update query
            $update_stmt = $pdo->prepare("
                UPDATE student 
                SET $field_name = :requested_value 
                WHERE student_id = :student_id
            ");
            $update_stmt->execute([
                'requested_value' => $requested_value,
                'student_id' => $student_id
            ]);

            // Mark the request as resolved
            $resolve_stmt = $pdo->prepare("
                UPDATE update_request 
                SET status = 'Resolved', date_resolved = NOW() 
                WHERE request_id = :request_id
            ");
            $resolve_stmt->execute(['request_id' => $request_id]);

            echo "<p>Request approved and student data updated successfully.</p>";
        } elseif ($action === 'reject') {
            // Mark the request as resolved with no changes
            $reject_stmt = $pdo->prepare("
                UPDATE update_request 
                SET status = 'Resolved', date_resolved = NOW() 
                WHERE request_id = :request_id
            ");
            $reject_stmt->execute(['request_id' => $request_id]);

            echo "<p>Request rejected successfully.</p>";
        } else {
            echo "<p>Invalid action.</p>";
            exit;
        }
    } catch (PDOException $e) {
        echo "<p>Error: " . $e->getMessage() . "</p>";
        exit;
    }
}

// Fetch pending requests for display
try {
    $stmt = $pdo->prepare("
        SELECT 
            r.request_id, 
            s.name AS student_name, 
            r.field_name, 
            r.requested_value, 
            r.date_requested 
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
    $pending_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Error fetching requests: " . $e->getMessage() . "</p>";
    $pending_requests = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Requests</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f9f9f9;
        }
        button {
            padding: 8px 15px;
            margin: 5px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
        }
        .approve {
            background-color: #4CAF50;
            color: white;
        }
        .reject {
            background-color: #f44336;
            color: white;
        }
    </style>
</head>
<body>
    <h1>Handler Dashboard - Process Requests</h1>

    <?php if (count($pending_requests) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Request ID</th>
                    <th>Student Name</th>
                    <th>Field</th>
                    <th>Requested Value</th>
                    <th>Date Requested</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending_requests as $request): ?>
                    <tr>
                        <td><?= htmlspecialchars($request['request_id']) ?></td>
                        <td><?= htmlspecialchars($request['student_name']) ?></td>
                        <td><?= htmlspecialchars($request['field_name']) ?></td>
                        <td><?= htmlspecialchars($request['requested_value']) ?></td>
                        <td><?= htmlspecialchars($request['date_requested']) ?></td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
                                <button type="submit" name="action" value="approve" class="approve">Approve</button>
                            </form>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
                                <button type="submit" name="action" value="reject" class="reject">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No pending requests.</p>
    <?php endif; ?>
</body>
</html>
