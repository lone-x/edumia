<?php 
session_start();
include "../db_conn.php";

// Fetch student details
$students_query = "SELECT student_id, roll_no, name FROM students ORDER BY roll_no";
$students_result = $conn->query($students_query);

// Fetch subject details
$subjects_query = "SELECT subjectid, subjectname FROM subjects ORDER BY subjectname";
$subjects_result = $conn->query($subjects_query);

// Prepare data arrays
$students = [];
$subjects = [];

while ($student = $students_result->fetch_assoc()) {
    $students[] = $student;
}

while ($subject = $subjects_result->fetch_assoc()) {
    $subjects[] = $subject;
}

// Fetch internal marks
$marks_query = "SELECT * FROM internal_marks";
$marks_result = $conn->query($marks_query);

// Prepare marks array
$marks_data = [];
while ($mark = $marks_result->fetch_assoc()) {
    $marks_data[$mark['student_id']][$mark['subject_id']] = $mark['marks'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Internal Marks</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Internal Marks</h1>
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Roll No</th>
                    <th>Student Name</th>
                    <?php foreach ($subjects as $subject): ?>
                        <th><?php echo $subject['subjectname']; ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo $student['roll_no']; ?></td>
                        <td><?php echo $student['name']; ?></td>
                        <?php foreach ($subjects as $subject): ?>
                            <td>
                                <?php 
                                $marks = $marks_data[$student['student_id']][$subject['subjectid']] ?? 'N/A';
                                echo $marks;
                                ?>
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
