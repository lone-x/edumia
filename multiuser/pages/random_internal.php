<?php
session_start();
include "../db_conn.php";

// Fetch student and subject details
$students_query = "SELECT student_id FROM students";
$students_result = $conn->query($students_query);

$subjects_query = "SELECT subjectid FROM subjects";
$subjects_result = $conn->query($subjects_query);

// Prepare the SQL statement
$insert_stmt = $conn->prepare("INSERT INTO internal_marks (student_id, subject_id, marks) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE marks = VALUES(marks)");

srand(time()); // Seed for random number generator

// Insert random marks for each student and each subject
while ($student = $students_result->fetch_assoc()) {
    while ($subject = $subjects_result->fetch_assoc()) {
        $marks = rand(35, 50);
        $insert_stmt->bind_param("iii", $student['student_id'], $subject['subjectid'], $marks);
        $insert_stmt->execute();
    }
    // Reset subject result pointer for the next student
    $subjects_result->data_seek(0);
}

$insert_stmt->close();
$conn->close();

echo "Random marks inserted successfully!";
?>
