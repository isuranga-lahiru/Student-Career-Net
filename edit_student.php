<?php
require_once "PHP/db_connection.php"; // Database connection

// Check if the admin is logged in by verifying the cookie
if (!isset($_COOKIE['email'])) {
    echo "<script>alert('Please log in first.')</script>";
    echo "<script>window.location.href = 'index.php';</script>";
    exit();
}

// Check if the student ID is passed via URL
if (isset($_GET['id'])) {
    $studentId = $_GET['id'];

    // Fetch student details from student_table
    $studentQuery = "SELECT * FROM `student_table` WHERE `UniRegNo` = ?";
    $studentStmt = $conn->prepare($studentQuery);
    $studentStmt->bind_param("s", $studentId);
    $studentStmt->execute();
    $studentResult = $studentStmt->get_result();

    // Fetch job-related details from student_job_table
    $jobQuery = "SELECT * FROM `student_job_table` WHERE `UniRegNo` = ?";
    $jobStmt = $conn->prepare($jobQuery);
    $jobStmt->bind_param("s", $studentId);
    $jobStmt->execute();
    $jobResult = $jobStmt->get_result();

    // Check if the student exists
    if ($studentResult->num_rows === 1) {
        $student = $studentResult->fetch_assoc();
        $job = $jobResult->fetch_assoc(); // Fetch job-related details
    } else {
        echo "<script>alert('Student not found.')</script>";
        echo "<script>window.location.href = 'admin.php';</script>";
        exit();
    }

    // Handle form submission for editing student details
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $studentName = $_POST['student_name'];
        $faculty = $_POST['faculty'];
        $academicYear = $_POST['academic_year'];
        $email = $_POST['email'];
        $interests = $_POST['interests'];

        // Update student details in the database (student_table)
        $updateStudentQuery = "UPDATE `student_table` SET `FullName` = ?, `Faculty` = ?, `AcademicYear` = ? WHERE `UniRegNo` = ?";
        $updateStudentStmt = $conn->prepare($updateStudentQuery);
        $updateStudentStmt->bind_param("ssis", $studentName, $faculty, $academicYear, $studentId);

        // Update job-related details in the database (student_job_table)
        $updateJobQuery = "UPDATE `student_job_table` SET `Email` = ?, `Interests` = ? WHERE `UniRegNo` = ?";
        $updateJobStmt = $conn->prepare($updateJobQuery);
        $updateJobStmt->bind_param("sss", $email, $interests, $studentId);

        // Execute the queries
        if ($updateStudentStmt->execute() && $updateJobStmt->execute()) {
            echo "<script>alert('Student details updated successfully.')</script>";
            echo "<script>window.location.href = 'admin.php';</script>";
        } else {
            echo "<script>alert('Failed to update student details.')</script>";
        }
    }
} else {
    echo "<script>alert('No student ID provided.')</script>";
    echo "<script>window.location.href = 'admin.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <!-- Navigation Bar -->
    <?php require "Nav.php"; ?>

    <header class="hero-section2 ">
    <div class="main_container formtext ">
        <h2 class="my-4">Edit Student Details</h2>

        <form action="" method="POST">
            <div class="form-group">
                <label for="student_name">Student Name</label>
                <input type="text" class="form-control" id="student_name" name="student_name" value="<?php echo htmlspecialchars($student['FullName']); ?>" required>
            </div>

            <div class="form-group">
                <label for="faculty">Faculty</label>
                <input type="text" class="form-control" id="faculty" name="faculty" value="<?php echo htmlspecialchars($student['Faculty']); ?>" required>
            </div>

            <div class="form-group">
                <label for="academic_year">Academic Year</label>
                <input type="number" class="form-control" id="academic_year" name="academic_year" value="<?php echo htmlspecialchars($student['AcademicYear']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($job['Email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="interests">Interests</label>
                <textarea class="form-control" id="interests" name="interests" required><?php echo htmlspecialchars($job['Interests']); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Update Student</button>
            <a href="admin.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    </header>
    

     <!-- Footer -->
     <footer class="footer">
      <p>&copy; 2024 Student Career Net. All Rights Reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
