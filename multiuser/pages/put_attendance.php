<?php 
session_start();
include "../db_conn.php";

// Get today's date
$date = date('Y-m-d');
$day = date('l');

// Fetch today's attendance from the database
$attendance_query = $conn->prepare("SELECT * FROM daily_attendance WHERE date = ?");
$attendance_query->bind_param("s", $date);
$attendance_query->execute();
$attendance_result = $attendance_query->get_result();

$todays_attendance = [];
while ($row = $attendance_result->fetch_assoc()) {
    $todays_attendance[$row['student_id']] = $row;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daily Attendance</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Daily Attendance</h1>
        <div class="alert alert-info">
            <?php
            echo "Today's Date: $date <br> Day: $day";
            ?>
        </div>
        <form action="submit_attendance.php" method="post">
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Roll No</th>
                        <th>Student Name</th>
                        <th>Period 1</th>
                        <th>Period 2</th>
                        <th>Period 3</th>
                        <th>Period 4</th>
                        <th>Period 5</th>
                        <th>Period 6</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch students from the database
                    $result = $conn->query("SELECT roll_no, name, student_id FROM students");

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $student_id = $row['student_id'];
                            $attendance = isset($todays_attendance[$student_id]) ? $todays_attendance[$student_id] : [];

                            echo "<tr>
                                <td>{$row['roll_no']}</td>
                                <td>{$row['name']}</td>
                                <td><input type='checkbox' name='attendance[{$row['student_id']}][period1]' " . (isset($attendance['period1']) && $attendance['period1'] ? 'checked' : '') . "></td>
                                <td><input type='checkbox' name='attendance[{$row['student_id']}][period2]' " . (isset($attendance['period2']) && $attendance['period2'] ? 'checked' : '') . "></td>
                                <td><input type='checkbox' name='attendance[{$row['student_id']}][period3]' " . (isset($attendance['period3']) && $attendance['period3'] ? 'checked' : '') . "></td>
                                <td><input type='checkbox' name='attendance[{$row['student_id']}][period4]' " . (isset($attendance['period4']) && $attendance['period4'] ? 'checked' : '') . "></td>
                                <td><input type='checkbox' name='attendance[{$row['student_id']}][period5]' " . (isset($attendance['period5']) && $attendance['period5'] ? 'checked' : '') . "></td>
                                <td><input type='checkbox' name='attendance[{$row['student_id']}][period6]' " . (isset($attendance['period6']) && $attendance['period6'] ? 'checked' : '') . "></td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No students found</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary">Submit Attendance</button>
        </form>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
