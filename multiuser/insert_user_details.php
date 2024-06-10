<?php
session_start();
include "db_conn.php";

// Read user details from the file
$user_details = file("user_details.txt", FILE_IGNORE_NEW_LINES);

foreach ($user_details as $user_detail) {
    preg_match('/Name: (.*), Username: (.*), Password: (.*)/', $user_detail, $matches);
    $name = $matches[1];
    $username = $matches[2];
    $password = $matches[3];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role = 'student';
    
    // Insert user data into the database
    $sql = "INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $username, $hashed_password, $role);
    
    if ($stmt->execute()) {
        echo "User $username inserted successfully<br>";
    } else {
        echo "Error inserting user $username: " . $stmt->error . "<br>";
    }
    
    $stmt->close();
}

$conn->close();
?>
