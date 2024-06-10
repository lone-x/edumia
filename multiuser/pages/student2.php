<?php
session_start();
include "../db_conn.php";

// Ensure the user is logged in
if (isset($_SESSION['name']) && isset($_SESSION['id'])) {
    
}


$name = $_SESSION["name"];

// Fetch student details
$student_query = "SELECT student_id, roll_no, name FROM students WHERE name = ?";
$stmt = $conn->prepare($student_query);
$stmt->bind_param("i", $name);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$student_id = $student['roll_no'];
// Fetch subject details
$subjects_query = "SELECT subjectid, subjectname FROM subjects ORDER BY subjectname";
$subjects_result = $conn->query($subjects_query);

// Prepare subjects array
$subjects = [];
while ($subject = $subjects_result->fetch_assoc()) {
    $subjects[] = $subject;
}

// Fetch internal marks for the student
$marks_query = "SELECT * FROM internal_marks WHERE student_id = ?";
$stmt = $conn->prepare($marks_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$marks_result = $stmt->get_result();

// Prepare marks array
$marks_data = [];
while ($mark = $marks_result->fetch_assoc()) {
    $marks_data[$mark['subject_id']] = $mark['marks'];
}

// Fetch attendance data
$attendance_query = "SELECT daily_attendance.date, daily_attendance.period1, daily_attendance.period2, 
                     daily_attendance.period3, daily_attendance.period4, daily_attendance.period5, 
                     daily_attendance.period6 
                     FROM daily_attendance 
                     WHERE daily_attendance.student_id = ? 
                     ORDER BY daily_attendance.date";
$stmt = $conn->prepare($attendance_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$attendance_result = $stmt->get_result();

// Process attendance data
$attendance_data = [];
$weeks = [];

while ($row = $attendance_result->fetch_assoc()) {
    $date = new DateTime($row['date']);
    $month = $date->format('F Y');
    $week = 'Week ' . ceil($date->format('j') / 7);
    $week_key = $month . ' - ' . $week;

    if (!isset($attendance_data[$week_key])) {
        $attendance_data[$week_key] = [];
    }

    $attendance_data[$week_key][] = $row;
    $weeks[$week_key] = true;
}

// Calculate attendance percentage
$total_days = 0;
$present_days = 0;
foreach ($attendance_data as $week_key => $days) {
    foreach ($days as $day) {
        $total_days++;
        $present_days += array_sum(array_slice($day, 1, 6));
    }
}
$attendance_percentage = $total_days ? ($present_days / ($total_days * 6)) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Welcome, <?php echo $student['name']; ?></h1>
        <h2>Student Details</h2>
        <table class="table table-bordered">
            <tr>
                <th>Roll No</th>
                <td><?php echo $student['roll_no']; ?></td>
            </tr>
            <tr>
                <th>Name</th>
                <td><?php echo $student['name']; ?></td>
            </tr>
        </table>

        <h2>Internal Marks</h2>
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <?php foreach ($subjects as $subject): ?>
                        <th><?php echo $subject['subjectname']; ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php foreach ($subjects as $subject): ?>
                        <td>
                            <?php 
                            $marks = $marks_data[$subject['subjectid']] ?? 'N/A';
                            echo $marks;
                            ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            </tbody>
        </table>

        <h2>Attendance Report</h2>
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Week</th>
                    <th>Attendance Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendance_data as $week => $days): ?>
                    <tr>
                        <td><?php echo $week; ?></td>
                        <td>
                            <table class="table table-bordered">
                                <?php foreach ($days as $day): ?>
                                    <tr>
                                        <td><?php echo $day['date']; ?></td>
                                        <td><?php echo $day['period1'] ? 'P' : 'A'; ?></td>
                                        <td><?php echo $day['period2'] ? 'P' : 'A'; ?></td>
                                        <td><?php echo $day['period3'] ? 'P' : 'A'; ?></td>
                                        <td><?php echo $day['period4'] ? 'P' : 'A'; ?></td>
                                        <td><?php echo $day['period5'] ? 'P' : 'A'; ?></td>
                                        <td><?php echo $day['period6'] ? 'P' : 'A'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th>Total Attendance Percentage</th>
                    <td><?php echo number_format($attendance_percentage, 2) . '%'; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
