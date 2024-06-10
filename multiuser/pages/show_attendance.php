<?php
// Database connection
session_start();
include "../db_conn.php";

// Fetch attendance data
$sql = "SELECT students.roll_no, students.name, daily_attendance.date, 
        daily_attendance.period1, daily_attendance.period2, daily_attendance.period3, 
        daily_attendance.period4, daily_attendance.period5, daily_attendance.period6 
        FROM daily_attendance 
        JOIN students ON daily_attendance.student_id = students.student_id 
        ORDER BY students.roll_no, daily_attendance.date";
$result = $conn->query($sql);

// Process data
$attendance_data = [];
$weeks = [];

while ($row = $result->fetch_assoc()) {
    $date = new DateTime($row['date']);
    $month = $date->format('F Y');
    $week = 'Week ' . ceil($date->format('j') / 7);
    $week_key = $month . ' - ' . $week;

    if (!isset($attendance_data[$row['roll_no']])) {
        $attendance_data[$row['roll_no']] = [
            'name' => $row['name'],
            'attendance' => []
        ];
    }

    if (!isset($attendance_data[$row['roll_no']]['attendance'][$week_key])) {
        $attendance_data[$row['roll_no']]['attendance'][$week_key] = [];
    }

    $attendance_data[$row['roll_no']]['attendance'][$week_key][] = $row;
    $weeks[$week_key] = true;
}

// Calculate attendance percentage
foreach ($attendance_data as $roll_no => &$data) {
    $total_days = 0;
    $present_days = 0;

    foreach ($data['attendance'] as $week_key => $days) {
        foreach ($days as $day) {
            $total_days++;
            $present_days += array_sum(array_slice($day, 3, 6));
        }
    }

    $data['attendance_percentage'] = $total_days ? ($present_days / ($total_days * 6)) * 100 : 0;
}
unset($data); // Break reference to the last element

// Sort attendance data by roll number
ksort($attendance_data);

// Handle search functionality
$search_query = '';
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
    $filtered_data = [];
    foreach ($attendance_data as $roll_no => $data) {
        if (strpos($data['name'], $search_query) !== false || strpos($roll_no, $search_query) !== false) {
            $filtered_data[$roll_no] = $data;
        }
    }
    $attendance_data = $filtered_data;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Report</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Attendance Report</h1>
        
        <form method="post" class="form-inline mb-4">
            <input type="text" name="search" class="form-control mr-sm-2" placeholder="Search by roll no" value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
        
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Roll No</th>
                    <th>Name</th>
                    <th>Attendance Percentage</th>
                    <?php foreach ($weeks as $week => $_): ?>
                        <th><?php echo $week; ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendance_data as $roll_no => $data): ?>
                    <tr>
                        <td><?php echo $roll_no; ?></td>
                        <td><?php echo $data['name']; ?></td>
                        <td><?php echo number_format($data['attendance_percentage'], 2) . '%'; ?></td>
                        <?php foreach ($weeks as $week => $_): ?>
                            <td>
                                <?php if (isset($data['attendance'][$week])): ?>
                                    <table class="table table-bordered">
                                        <?php foreach ($data['attendance'][$week] as $day): ?>
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
                                <?php else: ?>
                                    <p>No data</p>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
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
