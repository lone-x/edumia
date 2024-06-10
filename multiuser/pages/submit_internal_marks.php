<?php
session_start();
include "../db_conn.php";

// Fetch submitted marks data
$marks_data = $_POST['marks'];

// Prepare the SQL statement to insert/update internal marks
$insert_stmt = $conn->prepare("INSERT INTO internal_marks (student_id, subjectid, marks) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE marks = VALUES(marks)");

foreach ($marks_data as $student_id => $subjects) {
    foreach ($subjects as $subject_id => $marks) {
        $insert_stmt->bind_param("iii", $student_id, $subject_id, $marks);
        $insert_stmt->execute();
    }
}

$insert_stmt->close();
$conn->close();

header("Location: put_internal_mark.php");
exit();
?>
