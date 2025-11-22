<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data safely
    $name     = htmlspecialchars($_POST['name']);
    $email    = htmlspecialchars($_POST['email']);
    $phone    = htmlspecialchars($_POST['phone']);
    $services = htmlspecialchars($_POST['services']);
    $budget   = htmlspecialchars($_POST['budget']);
    $message  = htmlspecialchars($_POST['message']);

    // Database connection settings
    $servername = "localhost";
    $username   = "root";      // default XAMPP MySQL user
    $password   = "";          // default is empty in XAMPP
    $dbname     = "service_db"; // your database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and insert data
    $sql = "INSERT INTO service_request (name, email, phone_number, needed_service, budget, message) 
            VALUES ('$name', '$email','$phone', '$services', '$budget', '$message')";

if (isset($_POST['budget']) && is_numeric($_POST['budget'])) {
    // It's a number, so it's safe to proceed
    $budget = floatval($_POST['budget']); // Use floatval() to allow decimals

    // ... your database insert code here ...

} else {
    // The budget was not a valid number or was not set
    // Handle the error (e.g., show a message to the user)
    $budget = 0.00; // Or set a default value
}
    if ($conn->query($sql) === TRUE) {
        echo "<h2>Thank you for your submission!</h2>";
        echo "<p><strong>Name:</strong> $name</p>";
        echo "<p><strong>Email:</strong> $email</p>";
        echo "<p><strong>Phone Number:</strong> $phone</p>";
        echo "<p><strong>Service:</strong> $services</p>";
        echo "<p><strong>Budget:</strong> $budget</p>";
        echo "<p><strong>Message:</strong> $message</p>";
        echo "<p style='color:green;'> Your data has been saved you will be contacted soon.</p>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
} else {
    echo "Invalid request.";
}

?>
