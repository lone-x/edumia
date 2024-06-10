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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Internal Marks Entry</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Enter Internal Marks</h1>
        <form action="submit_internal_marks.php" method="post">
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
                                    <input type="number" name="marks[<?php echo $student['student_id']; ?>][<?php echo $subject['subjectid']; ?>]" class="form-control" min="0" max="100" required>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary">Submit Marks</button>
        </form>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
