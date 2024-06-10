<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "db_conn.php"; // Assuming you have a separate file for database connection
    
    $name = $_POST["name"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $role = $_POST["role"];
    
    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // You should also perform validation here
    
    // Insert user data into the database
    $sql = "INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $username, $hashed_password, $role);
    
    if ($stmt->execute()) {
        // Registration successful, redirect to login page
        header("Location: login-index.php");
        exit();
    } else {
        // Handle registration error
        echo "Registration failed. Please try again.";
    }
    
    // Close statement
    $stmt->close();
    
    // Close connection
    $conn->close();
}
?>
