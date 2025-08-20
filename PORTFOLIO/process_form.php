<?php
// --- Database Configuration ---
$servername = "localhost"; // Default for XAMPP
$username = "root";        // Default for XAMPP
$password = "";            // Default for XAMPP
$dbname = "portfolio_db";  // The database name you created

// --- Establish Database Connection ---
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check for connection errors
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    // If connection fails, send a server error response and stop the script
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection error.']);
    exit();
}

// --- Process Incoming Request ---

// Set response header to indicate JSON content
header('Content-Type: application/json');

// Get the JSON data sent from the JavaScript fetch call
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// --- Validate and Sanitize Input ---

// Check if all required fields are present and not empty
if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Please fill out all fields.']);
    exit();
}

// Sanitize inputs to prevent security vulnerabilities
$name = htmlspecialchars(strip_tags(trim($data['name'])));
$email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
$message = htmlspecialchars(strip_tags(trim($data['message'])));

// Validate the email format after sanitization
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
    exit();
}

// --- Insert Data into the Database ---

try {
    // Prepare an SQL statement to prevent SQL injection attacks
    $stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
    
    // Bind the sanitized variables to the prepared statement
    // "sss" means the parameters are three strings
    $stmt->bind_param("sss", $name, $email, $message);

    // Execute the statement
    if ($stmt->execute()) {
        // If successful, send a success response
        http_response_code(200); // OK
        echo json_encode(['status' => 'success', 'message' => 'Message sent successfully!']);
    } else {
        // If execution fails, throw an exception
        throw new Exception($stmt->error);
    }
} catch (Exception $e) {
    // Catch any database errors and send a specific server error response for debugging
    http_response_code(500); // Internal Server Error
    // IMPORTANT: Changed this line to show the actual error
    echo json_encode(['status' => 'error', 'message' => 'Failed to save message: ' . $e->getMessage()]);
} finally {
    // Always close the statement and connection
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?>
