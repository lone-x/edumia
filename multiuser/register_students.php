<?php
session_start();
include "db_conn.php";

// Fetch students from the database
$students_query = "SELECT student_id, name FROM students";
$students_result = $conn->query($students_query);

// Check if any students exist
if ($students_result->num_rows > 0) {
    $user_details = [];

    // Generate random easy passwords and prepare user details
    while ($student = $students_result->fetch_assoc()) {
        $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
        $username = strtolower(str_replace(' ', '.', $student['name'])) . rand(100, 999);
        
        $user_details[] = [
            'name' => $student['name'],
            'username' => $username,
            'password' => $password,
            'hashed_password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'student'
        ];
    }

    // Store user details in a file
    $file = fopen("user_details.txt", "w");
    foreach ($user_details as $user) {
        fwrite($file, "Name: {$user['name']}, Username: {$user['username']}, Password: {$user['password']}\n");
    }
    fclose($file);

    // Display user details in a table
    echo "<html><head><title>Student User Details</title></head><body>";
    echo "<h1>Student User Details</h1>";
    echo "<table border='1'>
            <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Password</th>
            </tr>";
    foreach ($user_details as $user) {
        echo "<tr>
                <td>{$user['name']}</td>
                <td>{$user['username']}</td>
                <td>{$user['password']}</td>
            </tr>";
    }
    echo "</table>";
    echo "<a href='insert_user_details.php'>Insert User Details</a>";
    echo "</body></html>";
} else {
    echo "No students found";
}

$conn->close();
?>
