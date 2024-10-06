<?php
session_start();
include 'config.php';

if ($_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit();
}

// Handle adding items
if (isset($_POST['add_item'])) {
    $name = $_POST['name'];
    $sql = "INSERT INTO items (name) VALUES ('$name')";
    if ($conn->query($sql)) {
        echo "Item added successfully!";
    } else {
        echo "Error adding item: " . $conn->error;
    }
}

// Fetch all items
$items = $conn->query("SELECT * FROM items");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            padding: 0;
        }

        h2 {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 20px;
            margin: 0;
        }

        h3 {
            color: #4CAF50;
            margin-left: 20px;
        }

        form {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            margin: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        table {
            width: 80%;
            margin: 20px auto;
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
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        a {
            display: inline-block;
            margin: 20px;
            padding: 10px 15px;
            background-color: #ff6347;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        a:hover {
            background-color: #ff4500;
        }
    </style>
</head>
<body>
    <h2>Admin Panel - Manage Items</h2>

    <!-- Add new item form -->
    <h3>Add New Item</h3>
    <form method="POST" action="">
        <input type="text" name="name" placeholder="Item name" required>
        <button type="submit" name="add_item">Add Item</button>
    </form>

    <!-- Display list of items -->
    <h3>Items List</h3>
    <table border="1">
        <thead>
            <tr>
                <th>Item Name</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $items->fetch_assoc()) { ?>
                <tr>
                    <td><?= $item['name']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <a href="index.php">Logout</a>
</body>
</html>
