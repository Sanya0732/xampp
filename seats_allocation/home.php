<?php
session_start(); // Ensure the session is started

// Initialize the queue in session if it doesn't exist
if (!isset($_SESSION['ticket_queue'])) {
    $_SESSION['ticket_queue'] = [];
}

// Function to add a ticket to the queue (enqueue)
function enqueueTicket($ticket_number) {
    array_push($_SESSION['ticket_queue'], $ticket_number); // Add to the queue
}

// Function to remove and return the next ticket from the queue (dequeue)
function dequeueTicket() {
    if (!empty($_SESSION['ticket_queue'])) {
        return array_shift($_SESSION['ticket_queue']); // Remove and return the first ticket
    }
    return null; // Return null if the queue is empty
}

// Function to check if the queue is empty
function isQueueEmpty() {
    return empty($_SESSION['ticket_queue']);
}

// Function to assign a seat
function assignSeat($ticket_number) {
    global $conn, $message, $message_type, $assigned_ticket;

    // Check if the ticket has already been assigned
    $check_sql = "SELECT * FROM seats2 WHERE ticket_number = $ticket_number";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        $message = "Error: Ticket Number $ticket_number has already been assigned a seat!";
        $message_type = 'error';
        return;
    }

    // Select available seat
    $ticket_type = ($ticket_number % 2 === 0) ? 'even' : 'odd';
    if ($ticket_type === 'odd') {
        $sql = "SELECT * FROM seats2 WHERE seat_number % 2 != 0 AND seat_status = 'empty' LIMIT 1";
    } else {
        $sql = "SELECT * FROM seats2 WHERE seat_number % 2 = 0 AND seat_status = 'empty' LIMIT 1";
    }

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $seat_number = $row['seat_number'];
        $update_sql = "UPDATE seats2 SET seat_status = 'occupied', ticket_number = $ticket_number, created_at = NOW(), removed_at = NULL WHERE seat_number = $seat_number";
        if ($conn->query($update_sql) === TRUE) {
            $assigned_ticket = $ticket_number; // Store assigned ticket number
            $message = "Seat assigned: Seat Number " . $seat_number . " for Ticket Number " . $ticket_number;
            $message_type = 'success';
        } else {
            $message = "Error updating seat: " . $conn->error;
            $message_type = 'error';
        }
    } else {
        // All seats are full, add ticket to queue
        enqueueTicket($ticket_number);
        $message = "All seats are full. Your ticket $ticket_number has been added to the queue.";
        $message_type = 'info';
    }
}

// Function to remove a ticket
function removeTicket($seat_number) {
    global $conn, $message, $message_type, $assigned_ticket;

    $sql = "UPDATE seats2 SET seat_status = 'empty', ticket_number = NULL, removed_at = NOW() WHERE seat_number = $seat_number";
    if ($conn->query($sql) === TRUE) {
        $message = "Seat Number $seat_number has been freed up.";

        // If the queue is not empty, assign the next ticket
        if (!isQueueEmpty()) {
            $next_ticket = dequeueTicket();
            assignSeat($next_ticket);
            $message .= " Next ticket in queue (Ticket $next_ticket) has been assigned.";
            $assigned_ticket = $next_ticket; // Update assigned ticket number
        }
        $message_type = 'info';
    } else {
        $message = "Error updating seat: " . $conn->error;
        $message_type = 'error';
    }
}

// Database connection
include 'db.php'; // Ensure database connection

$message = '';
$message_type = '';
$assigned_ticket = null; // Variable to hold assigned ticket number

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['assign_ticket'])) {
        $ticket_number = $_POST['ticket_number'];
        assignSeat($ticket_number);
    } elseif (isset($_POST['remove_seat'])) {
        $seat_number = $_POST['seat_number'];
        removeTicket($seat_number);
    }
}

// Fetch seat data
$seats1_result = $conn->query("SELECT * FROM seats2");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Assignment</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f8ff; /* Light blue background */
            margin: 0;
            padding: 20px;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #333; /* Darker color for title */
            font-size: 32px;
            font-weight: normal;
            margin-bottom: 30px;
        }

        form {
            text-align: center;
            margin-bottom: 20px;
        }

        input[type="number"] {
            padding: 10px;
            font-size: 16px;
            width: 220px;
            border: 1px solid #ccc; /* Light gray border */
            border-radius: 5px;
            margin-right: 10px;
        }

        button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50; /* Green button */
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049; /* Darker green on hover */
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
            color: white;
        }

        .available {
            background-color: #28a745; /* Green for available seats */
        }

        .occupied {
            background-color: #ff6347; /* Tomato red for occupied seats */
        }

        .message {
            max-width: 500px;
            margin: 20px auto;
            padding: 15px;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
            color: white;
        }

        .message.success {
            background-color: #28a745; /* Green for success */
        }

        .message.error {
            background-color: #ff6347; /* Red for error */
        }

        .message.info {
            background-color: #17a2b8; /* Blue for info */
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
            background-color: #343a40; /* Dark gray for header */
            color: white;
        }

        td {
            background-color: #f9f9f9; /* Light gray for table rows */
        }

    </style>

</head>
<body>
    <h1>Seat Assignment System</h1>

    <?php if (!empty($message)): ?>
        <div class="message <?= $message_type ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <?php if ($assigned_ticket !== null): ?>
        <div class="message success">
            Ticket Number <?= $assigned_ticket ?> is now being assigned!
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="ticket_number">Enter Ticket Number:</label>
        <input type="number" id="ticket_number" name="ticket_number" required>
        <button type="submit" name="assign_ticket">Assign Seat</button>
    </form>

    <h2 style="text-align: center;">Seat Matrix</h2>
    <div class="seat-grid">
        <?php
        if ($seats1_result->num_rows > 0) {
            while($seat = $seats1_result->fetch_assoc()) {
                $seat_status_class = ($seat['seat_status'] == 'occupied') ? 'occupied' : 'available';
                echo "<div class='seat $seat_status_class'>{$seat['seat_number']}</div>";
            }
        } else {
            echo "<p>No seats available.</p>";
        }
        ?>
    </div>

    <form method="POST" action="">
        <label for="seat_number">Enter Seat Number to Free:</label>
        <input type="number" id="seat_number" name="seat_number" required>
        <button type="submit" name="remove_seat">Remove Ticket</button>
    </form>

    <h2 style="text-align: center;">Current Ticket Queue</h2>
    <div>
        <ul>
            <?php
            if (!isQueueEmpty()) {
                foreach ($_SESSION['ticket_queue'] as $ticket) {
                    echo "<li>Ticket Number: $ticket</li>";
                }
            } else {
                echo "<li>No tickets in the queue.</li>";
            }
            ?>
        </ul>
    </div>

    <h2 style="text-align: center;">Seat Allocation Table</h2>
    <table>
        <thead>
            <tr>
                <th>Seat Number</th>
                <th>Ticket Number</th>
                <th>Assigned Timestamp</th>
                <th>Removed Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $table_result = $conn->query("SELECT seat_number, ticket_number, created_at, removed_at FROM seats2");
            if ($table_result->num_rows > 0) {
                while($row = $table_result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['seat_number']}</td>
                            <td>{$row['ticket_number']}</td>
                            <td>{$row['created_at']}</td>
                            <td>{$row['removed_at']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No seats assigned yet.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>
