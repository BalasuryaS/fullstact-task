<?php
// Establish database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "users";
$table = "registration";

// Create connection
$conn = new mysqli($localhost, $root, $password, $users);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['cpassword'] ?? '';

// Check if passwords match and meet complexity requirements
if ($password != $confirmPassword) {
    echo "Passwords do not match";
} elseif (strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/\d/", $password) || !preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $password)) {
    echo "Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character";
} else {
    // Check if email is already registered
    $emailCheckQuery = "SELECT * FROM registration WHERE email = ?";
    $stmt = $conn->prepare($emailCheckQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Email is already registered";
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert data into the database
        $insertQuery = "INSERT INTO registration (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("sss", $username, $email, $hashedPassword);

        if ($stmt->execute()) {
            echo "Registration successful!";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

// Close database connection
$stmt->close();
$conn->close();
?>
