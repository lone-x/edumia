<?php 
session_start();
include "../db_conn.php";

if (isset($_SESSION['username']) && isset($_SESSION['id'])) {  

    // Fetch subjects data
    $sql = "SELECT * FROM Subjects";
    $result = $conn->query($sql);
    $subjects = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $subjects[$row['SubjectID']] = $row['SubjectName'];
        }
    }

    // If form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['subjects'])) {
        foreach ($_POST['subjects'] as $day => $timeSlots) {
            foreach ($timeSlots as $timeSlot => $subjectID) {
                // Update Timetable table
                $sql = "UPDATE Timetable 
                        SET SubjectID = '$subjectID' 
                        WHERE DayID = (
                            SELECT DayID FROM Days WHERE DayName = '$day'
                        ) 
                        AND StartTime = '$timeSlot'";
                $conn->query($sql);
            }
        }
        // Redirect after updating
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }

    // Fetch timetable data
    $sql = "
        SELECT 
            Days.DayName AS Day,
            Timetable.StartTime,
            Timetable.SubjectID
        FROM 
            Days
        CROSS JOIN 
            (
                SELECT '09:30' AS StartTime
                UNION ALL SELECT '10:30'
                UNION ALL SELECT '11:30'
                UNION ALL SELECT '13:30'
                UNION ALL SELECT '14:30'
                UNION ALL SELECT '15:30'
            ) AS TimeSlots
        LEFT JOIN 
            Timetable ON Days.DayID = Timetable.DayID AND TimeSlots.StartTime = Timetable.StartTime
        ORDER BY 
            Days.DayID, Timetable.StartTime;
    ";

    $result = $conn->query($sql);

    // Initialize $timeSlots array
    $timeSlots = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $timeSlots[$row['Day']][] = [
                'StartTime' => $row['StartTime'],
                'SubjectID' => $row['SubjectID']
            ];
        }
    }

    // Close connection
    $conn->close(); 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Timetable Editor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>



<!-- 	code for form for timetable editing table 
 -->    
 <div class="container">
        <h2>Timetable Editor</h2>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
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
                        <?php foreach ($timeSlots as $day => $slots): ?>
                            <tr>
                                <td><?php echo $day; ?></td>
                                <?php foreach ($slots as $slot): ?>
                                    <td>
                                        <select class="form-control" name="subjects[<?php echo $day; ?>][<?php echo $slot['StartTime']; ?>]">
                                            <option value="">Select Subject</option>
                                            <?php foreach ($subjects as $subjectID => $subjectName): ?>
                                                <option value="<?php echo $subjectID; ?>" <?php if ($slot['SubjectID'] == $subjectID) echo 'selected'; ?>><?php echo $subjectName; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</body>
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
</html>
<?php 
} else {
    header("Location: ../login-index.php");
} 
?>
