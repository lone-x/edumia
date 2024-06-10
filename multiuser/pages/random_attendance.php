<?php 
session_start();
include "../db_conn.php";

// Function to generate an array of dates for the last two months excluding weekends
function generateDates($months = 2) {
    $dates = [];
    $startDate = new DateTime("-$months months");
    $endDate = new DateTime('yesterday'); // Use 'yesterday' to exclude today

    while ($startDate <= $endDate) {
        if ($startDate->format('N') < 6) { // Only add Monday to Friday
            $dates[] = $startDate->format('Y-m-d');
        }
        $startDate->modify('+1 day');
    }
    return $dates;
}

// Fetch all student IDs
$students = $conn->query("SELECT student_id FROM students");
$student_ids = [];
while ($row = $students->fetch_assoc()) {
    $student_ids[] = $row['student_id'];
}

// Generate dates for the last two months
$dates = generateDates();

// Prepare the SQL statement
$stmt = $conn->prepare("INSERT INTO daily_attendance (student_id, date, period1, period2, period3, period4, period5, period6) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

// Seed for random number generator
srand(time());

// Function to generate random attendance ensuring at least 65% presence
function generateAttendance($minPresencePercentage = 65) {
    $attendance = [];
    for ($i = 0; $i < 6; $i++) {
        $attendance[] = (rand(1, 100) <= $minPresencePercentage) ? 1 : 0;
    }
    return $attendance;
}

// Insert random attendance data for each student and each date
foreach ($student_ids as $student_id) {
    foreach ($dates as $date) {
        $attendance = generateAttendance();
        $stmt->bind_param("isiiiiii", $student_id, $date, $attendance[0], $attendance[1], $attendance[2], $attendance[3], $attendance[4], $attendance[5]);
        $stmt->execute();
    }
}

$stmt->close();
$conn->close();

echo "Random attendance data inserted successfully!";
?> 
