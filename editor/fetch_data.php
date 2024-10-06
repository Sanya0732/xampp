<?php
// Database configuration
$host = 'localhost';
$db_name = 'editor';
$username = 'root';
$password = '';

try {
    // Create a new PDO connection
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all the content from the database
    $stmt = $conn->query("SELECT content, created_at FROM data ORDER BY created_at DESC");

    // Display each entry
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<div><strong>Posted on:</strong> " . $row['created_at'] . "</div>";
        echo "<div>" . $row['content'] . "</div><hr>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$conn = null;
?>
