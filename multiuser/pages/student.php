<?php 
   session_start();
   include "../db_conn.php";
   if (isset($_SESSION['username']) && isset($_SESSION['id'])) {   
	$sql = "
    SELECT 
        Days.DayName AS Day,
        MAX(CASE WHEN TimeSlots.StartTime = '09:30' THEN Subjects.SubjectName ELSE '' END) AS '09:30 - 10:30',
        MAX(CASE WHEN TimeSlots.StartTime = '10:30' THEN Subjects.SubjectName ELSE '' END) AS '10:30 - 11:30',
        MAX(CASE WHEN TimeSlots.StartTime = '11:30' THEN Subjects.SubjectName ELSE '' END) AS '11:30 - 12:30',
        MAX(CASE WHEN TimeSlots.StartTime = '13:30' THEN Subjects.SubjectName ELSE '' END) AS '01:30 - 02:30',
        MAX(CASE WHEN TimeSlots.StartTime = '14:30' THEN Subjects.SubjectName ELSE '' END) AS '02:30 - 03:30',
        MAX(CASE WHEN TimeSlots.StartTime = '15:30' THEN Subjects.SubjectName ELSE '' END) AS '03:30 - 04:30'
    FROM 
        Days
    CROSS JOIN 
        (
            SELECT '09:30' AS StartTime, '10:30' AS EndTime
            UNION ALL SELECT '10:30', '11:30'
            UNION ALL SELECT '11:30', '12:30'
            UNION ALL SELECT '13:30', '14:30'
            UNION ALL SELECT '14:30', '15:30'
            UNION ALL SELECT '15:30', '16:30'
        ) AS TimeSlots
    LEFT JOIN 
        Timetable ON Days.DayID = Timetable.DayID AND TimeSlots.StartTime = Timetable.StartTime
    LEFT JOIN 
        Subjects ON Timetable.SubjectID = Subjects.SubjectID
    GROUP BY 
        Days.DayName
    ORDER BY 
        Days.DayID;
";

$result = $conn->query($sql);

// Check if there are any results
if ($result->num_rows > 0) {
    // Fetch result rows as an associative array
    $rows = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $rows = [];
}

// Close connection
$conn->close();

?>


<!DOCTYPE html>
<html>
<head>
	<title>HOME</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
</head>
<body>
<body>
    <div class="container">
        <h2>Timetable</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>09:30 - 10:30</th>
                        <th>10:30 - 11:30</th>
                        <th>11:30 - 12:30</th>
                        <th>01:30 - 02:30</th>
                        <th>02:30 - 03:30</th>
                        <th>03:30 - 04:30</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?php echo $row['Day']; ?></td>
                            <td><?php echo $row['09:30 - 10:30']; ?></td>
                            <td><?php echo $row['10:30 - 11:30']; ?></td>
                            <td><?php echo $row['11:30 - 12:30']; ?></td>
                            <td><?php echo $row['01:30 - 02:30']; ?></td>
                            <td><?php echo $row['02:30 - 03:30']; ?></td>
                            <td><?php echo $row['03:30 - 04:30']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Bootstrap JS (optional) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
      <div class="container d-flex justify-content-center align-items-center"
      style="min-height: 100vh">
      		<div class="card" style="width: 18rem;">
			  <div class="card-body text-center">
			    <h5 class="card-title">
                    name: <?=$_SESSION['name']?>
                    </br></br>
                    role: <?=$_SESSION['role']?>
			    </h5>
			    <a href="../logout.php" class="btn btn-dark">Logout</a>
			  </div>
			</div> 
      </div>
</body>
</html>
<?php }else{
	header("Location: ../login-index.php");
} ?>