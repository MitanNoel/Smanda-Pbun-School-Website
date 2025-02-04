<?php
// Error reporting should be turned off in production
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Check if the user is already logged in, if yes then redirect to main page
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: /'); // Make sure to use the correct path to your main page
    exit;
}

require 'credential.php';

// Create a secure connection to the database
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check connection and handle any connection errors securely
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    // Consider adding a user-friendly error message or redirect here
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING);

    // Prepare the SQL statement to prevent SQL injection
    if ($stmt = $conn->prepare("SELECT id, username, password FROM Mari WHERE username=?")) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verify the password against the hashed password in the database
            if (password_verify($password, $user['password'])) {
                // Regenerate session ID upon login
                session_regenerate_id();

                // Set session variables
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $user['id'];

                // Get the current date and time
                $currentDateTime = date('Y-m-d H:i:s');

                // Update the "isactive" status to 1 (online) and "lastlogin" to current date and time
                if ($updateStmt = $conn->prepare("UPDATE Mari SET isactive = 1, lastlogin = ? WHERE username=?")) {
                    $updateStmt->bind_param("ss", $currentDateTime, $username);
                    $updateStmt->execute();
                    $updateStmt->close();
                }

                // Redirect to the main page
                header('Location: /'); // Make sure to use the correct path to your main page
                exit;
            } 
        }
        $stmt->close();
        // If the username doesn't exist or the password is incorrect, redirect to the login page with an error
        header('Location: /login.html?error=true');
        exit;
    } 
    else {
        error_log("Prepare failed: " . $conn->error);
        // Consider adding a user-friendly error message or redirect here
    }
}

else {
    echo "Invalid request method.";
}

$conn->close();
?>
