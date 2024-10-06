<?php
session_start();
include 'config.php';

if ($_SESSION['role'] != 'user') {
    header('Location: index.php');
    exit();
}

// Fetch items from the database
$items = $conn->query("SELECT * FROM items");

// Handle adding expenses
if (isset($_POST['add_expense'])) {
    $item_id = $_POST['item_id'];
    $amount = $_POST['amount'];
    $user_id = 1; // Assuming there's only one user or you handle user IDs separately

    // Check if amount is provided
    if (!empty($amount) && is_numeric($amount) && $amount > 0) {
        $sql = "INSERT INTO expenses (user_id, item_id, amount) VALUES ($user_id, $item_id, $amount)";
        if ($conn->query($sql)) {
            echo "Expense added successfully!";
        } else {
            echo "Error adding expense: " . $conn->error;
        }
    } else {
        echo "Please enter a valid amount.";
    }
}

// Fetch expenses by the user (for displaying in the table)
$expenses = $conn->query("SELECT e.id, i.name AS item_name, e.amount 
                          FROM expenses e
                          JOIN items i ON e.item_id = i.id
                          WHERE e.user_id = 1");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }

        h2 {
            background-color: #007BFF;
            color: white;
            text-align: center;
            padding: 20px;
            margin: 0;
        }

        h3 {
            color: #007BFF;
            margin-left: 20px;
        }

        form {
            margin: 20px;
        }

        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        td input[type="number"] {
            padding: 8px;
            width: 70%;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #218838;
        }

        a {
            display: inline-block;
            margin: 20px;
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #c82333;
        }

        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>User Panel - Add and View Expenses</h2>

    <!-- Display list of items with add expense field -->
    <h3>Add Expense for Items</h3>
    <?php while ($item = $items->fetch_assoc()) { ?>
        <form method="POST" action="" style="margin: 20px;">
            <table border="1">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Add Expense</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= $item['name']; ?></td>
                        <td>
                            <input type="number" name="amount" placeholder="Enter expense" >
                            <input type="hidden" name="item_id" value="<?= $item['id']; ?>">
                            <button type="submit" name="add_expense">Add Expense</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    <?php } ?>

    <!-- Display the user's expenses -->
    <h3>Your Expenses</h3>
    <table border="1">
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Expense Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($expense = $expenses->fetch_assoc()) { ?>
                <tr>
                    <td><?= $expense['item_name']; ?></td>
                    <td><?= $expense['amount']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="index.php">Logout</a>
</body>
</html>
