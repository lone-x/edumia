<?php
session_start();
include "../db_conn.php";

// Get today's date
$date = date('Y-m-d');

// Fetch submitted attendance data
$attendance_data = $_POST['attendance'];

// Prepare the SQL statement to insert/update attendance
$insert_stmt = $conn->prepare("INSERT INTO daily_attendance (student_id, date, period1, period2, period3, period4, period5, period6) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE period1 = VALUES(period1), period2 = VALUES(period2), period3 = VALUES(period3), period4 = VALUES(period4), period5 = VALUES(period5), period6 = VALUES(period6)");

foreach ($attendance_data as $student_id => $attendance) {
    $period1 = isset($attendance['period1']) ? 1 : 0;
    $period2 = isset($attendance['period2']) ? 1 : 0;
    $period3 = isset($attendance['period3']) ? 1 : 0;
    $period4 = isset($attendance['period4']) ? 1 : 0;
    $period5 = isset($attendance['period5']) ? 1 : 0;
    $period6 = isset($attendance['period6']) ? 1 : 0;

    $insert_stmt->bind_param("isiiiiii", $student_id, $date, $period1, $period2, $period3, $period4, $period5, $period6);
    $insert_stmt->execute();
}

$insert_stmt->close();
$conn->close();

header("Location: show_attendance.php");
exit();
?>
