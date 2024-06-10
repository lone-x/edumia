<?php
//registration template
if (!isset($_SESSION['username']) && !isset($_SESSION['id'])) {
?>
<!DOCTYPE html>
<html>
<head>
    <title>Multi-user Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center"
    style="min-height: 100vh">
        <form class="border shadow p-3 rounded"
            action="./register.php" 
            method="post" 
            style="width: 450px;">
            <h1 class="text-center p-3">Registration</h1>
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" name="name" id="name">
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" name="username" id="username">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="password">
            </div>
            <div class="mb-1">
                <label class="form-label">Role:</label>
            </div>
            <select class="form-select mb-3" name="role" aria-label="Default select example">
                <option selected value="student">Student</option>
                <option value="admin">Admin</option>
                <!-- <option value="parent">Parent</option> -->
                <option value="teacher">Teacher</option>
            </select>
            
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
</body>
</html>
<?php
} else {
    header("Location: redirect.php");
}
?>
