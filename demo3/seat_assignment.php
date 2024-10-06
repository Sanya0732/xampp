<?php
// Include the DB connection file
include 'db_connection.php';

// Handle form submission to assign or remove a seat
$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['assign_ticket'])) {
        $ticket_number = $_POST['ticket_number'];
        assignSeat($ticket_number);
    } elseif (isset($_POST['remove_seat'])) {
        $seat_number = $_POST['seat_number'];
        removeTicket($seat_number);
    }
}

function assignSeat($ticket_number) {
    global $conn, $message, $message_type;

    // Check if the ticket number is already assigned to a seat
    $check_sql = "SELECT * FROM seats WHERE ticket_number = $ticket_number";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        $message = "Error: Ticket Number $ticket_number has already been assigned a seat!";
        $message_type = 'error';
        return;
    }

    // Determine if the ticket number is odd or even
    $ticket_type = ($ticket_number % 2 === 0) ? 'even' : 'odd';

    // Get available seat based on ticket type
    if ($ticket_type === 'odd') {
        $sql = "SELECT * FROM seats WHERE seat_number % 2 != 0 AND seat_status = 'empty' LIMIT 1";
    } else {
        $sql = "SELECT * FROM seats WHERE seat_number % 2 = 0 AND seat_status = 'empty' LIMIT 1";
    }

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $seat_number = $row['seat_number'];

        $update_sql = "UPDATE seats SET seat_status = 'occupied', ticket_number = $ticket_number, created_at = NOW(), removed_at = NULL WHERE seat_number = $seat_number";
        if ($conn->query($update_sql) === TRUE) {
            $message = "Seat assigned: Seat Number " . $seat_number . " for Ticket Number " . $ticket_number;
            $message_type = 'success';
        } else {
            $message = "Error updating seat: " . $conn->error;
            $message_type = 'error';
        }
    } else {
        // No appropriate seat found, add to waiting list
        $waiting_list_sql = "INSERT INTO waiting_list (ticket_number) VALUES ($ticket_number)";
        if ($conn->query($waiting_list_sql) === TRUE) {
            $message = "No seats available. Ticket Number $ticket_number added to waiting list.";
            $message_type = 'info';
        } else {
            $message = "Error adding to waiting list: " . $conn->error;
            $message_type = 'error';
        }
    }
}


function removeTicket($seat_number) {
    global $conn, $message, $message_type;

    // Update the seat status and handle waiting list
    $sql = "UPDATE seats SET seat_status = 'empty', ticket_number = NULL, removed_at = NOW() WHERE seat_number = $seat_number";
    if ($conn->query($sql) === TRUE) {
        // Check for waiting list
        $waiting_list_sql = "SELECT * FROM waiting_list WHERE status = 'pending' AND ticket_number % 2 = (SELECT seat_number % 2 FROM seats WHERE seat_number = $seat_number) LIMIT 1";
        $waiting_list_result = $conn->query($waiting_list_sql);

        if ($waiting_list_result->num_rows > 0) {
            $waiting_list_row = $waiting_list_result->fetch_assoc();
            $waiting_ticket_number = $waiting_list_row['ticket_number'];
            $waiting_list_id = $waiting_list_row['id'];

            // Assign the freed seat to the waiting list person
            $update_seat_sql = "UPDATE seats SET seat_status = 'occupied', ticket_number = $waiting_ticket_number, created_at = NOW(), removed_at = NULL WHERE seat_number = $seat_number";
            if ($conn->query($update_seat_sql) === TRUE) {
                // Update waiting list status
                $update_waiting_list_sql = "UPDATE waiting_list SET status = 'assigned' WHERE id = $waiting_list_id";
                $conn->query($update_waiting_list_sql);
                $message = "Seat Number $seat_number has been freed and assigned to waiting list ticket number $waiting_ticket_number.";
            } else {
                $message = "Error assigning waiting list seat: " . $conn->error;
                $message_type = 'error';
            }
        } else {
            $message = "Seat Number $seat_number has been freed up.";
        }
        $message_type = 'info';
    } else {
        $message = "Error updating seat: " . $conn->error;
        $message_type = 'error';
    }
}

// Fetch all seats for display
$seats_result = $conn->query("SELECT * FROM seats");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Assignment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            text-align: center;
            margin-bottom: 20px;
        }

        input[type="number"] {
            padding: 10px;
            font-size: 16px;
            width: 200px;
            border: 2px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        .seat-grid {
            display: grid;
            grid-template-columns: repeat(5, 60px);
            gap: 10px;
            justify-content: center;
            margin-bottom: 20px;
        }

        .seat {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: bold;
            color: white;
        }

        .available {
            background-color: #4CAF50;
        }

        .occupied {
            background-color: #FF6347;
        }

        .message {
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
            color: white;
        }

        .message.success {
            background-color: #4CAF50;
        }

        .message.error {
            background-color: #f44336;
        }

        .message.info {
            background-color: #2196F3;
        }

        table {
            margin: 0 auto;
            border-collapse: collapse;
            width: 80%;
            text-align: center;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 15px;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        td {
            background-color: #f9f9f9;
        }

    </style>
</head>
<body>

    <h1>Seat Assignment System</h1>

    <!-- Display message -->
    <?php if (!empty($message)): ?>
        <div class="message <?= $message_type ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <!-- Form to input ticket number -->
    <form method="POST" action="">
        <label for="ticket_number">Enter Ticket Number:</label>
        <input type="number" id="ticket_number" name="ticket_number" required>
        <button type="submit" name="assign_ticket">Assign Seat</button>
    </form>

    <!-- Seat Matrix -->
    <h2 style="text-align: center;">Seat Matrix</h2>
    <div class="seat-grid">
        <?php
        // Display each seat as a grid
        if ($seats_result->num_rows > 0) {
            while($seat = $seats_result->fetch_assoc()) {
                $seat_status_class = ($seat['seat_status'] == 'occupied') ? 'occupied' : 'available';
                echo "<div class='seat $seat_status_class'>{$seat['seat_number']}</div>";
            }
        } else {
            echo "No seats available.";
        }
        ?>
    </div>

    <!-- Form to remove a ticket -->
    <form method="POST" action="">
        <label for="seat_number">Enter Seat Number to Free:</label>
        <input type="number" id="seat_number" name="seat_number" required>
        <button type="submit" name="remove_seat">Remove Ticket</button>
    </form>

    <!-- Seat Allocation Table -->
    <h2 style="text-align: center;">Seat Allocation Table</h2>
    <table>
    <thead>
        <tr>
            <th>Seat Number</th>
            <th>Ticket Number</th>
            <th>Assigned Timestamp</th>
            <th>Removed Timestamp</th>
            <th>Waiting List Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Fetch the seats with ticket numbers and waiting list status
        $table_result = $conn->query("SELECT seats.seat_number, seats.ticket_number, seats.created_at, seats.removed_at, IF(waiting_list.status = 'assigned', 'Yes', 'No') as waiting_list_status
                                     FROM seats LEFT JOIN waiting_list ON seats.ticket_number = waiting_list.ticket_number");

        if ($table_result->num_rows > 0) {
            while($row = $table_result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['seat_number']}</td>
                        <td>{$row['ticket_number']}</td>
                        <td>{$row['created_at']}</td>
                        <td>{$row['removed_at']}</td>
                        <td>{$row['waiting_list_status']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No seats assigned yet.</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>


<?php
$conn->close();
?>
