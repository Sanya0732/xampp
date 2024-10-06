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

    // Check if content is set and not empty
    if (isset($_POST['content']) && !empty($_POST['content'])) {
        $content = $_POST['content'];

        // Insert the content into the database
        $stmt = $conn->prepare("INSERT INTO data (content) VALUES (:content)");
        $stmt->bindParam(':content', $content);
        $stmt->execute();

        echo "Content successfully submitted!";
    } else {
        echo "Content cannot be empty.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$conn = null;
?>
