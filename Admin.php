<?php
require_once "PHP/db_connection.php"; // Database connection

// Check if the admin is logged in by verifying the cookie
if (!isset($_COOKIE['email'])) {
    echo "<script>alert('Please log in first.')</script>";
    echo "<script>window.location.href = 'index.php';</script>";
    exit();
}   

// Fetch all companies
$companyQuery = "SELECT * FROM `company_table`";
$companyResult = $conn->query($companyQuery);

// Fetch all students joined with job details
$studentQuery = "SELECT * FROM `student_table` JOIN `student_job_table` ON student_table.UniRegNo = student_job_table.UniRegNo";
$studentResult = $conn->query($studentQuery);

// Handle deletion of companies
if (isset($_GET['delete_company'])) {
    $companyId = $_GET['delete_company'];
    $deleteCompanyQuery = "DELETE FROM `company_table` WHERE `UniqueWebsiteID` = ?";
    $deleteCompanyStmt = $conn->prepare($deleteCompanyQuery);
    $deleteCompanyStmt->bind_param("s", $companyId);
    if ($deleteCompanyStmt->execute()) {
        echo "<script>alert('Company deleted successfully.'); window.location.href = 'admin.php';</script>";
    } else {
        echo "<script>alert('Failed to delete the company.');</script>";
    }
}

// Handle deletion of students
if (isset($_GET['delete_student'])) {
    $studentId = $_GET['delete_student'];
    $deleteStudentQuery = "DELETE FROM `student_table` WHERE `UniRegNo` = ?";
    $deleteStudentJobQuery = "DELETE FROM `student_job_table` WHERE `UniRegNo` = ?";

    // Prepare statements
    $deleteStudentStmt = $conn->prepare($deleteStudentQuery);
    $deleteStudentJobStmt = $conn->prepare($deleteStudentJobQuery);
    
    // Bind parameters
    $deleteStudentStmt->bind_param("s", $studentId);
    $deleteStudentJobStmt->bind_param("s", $studentId);

    // Execute the job deletion first
    if ($deleteStudentJobStmt->execute()) {
        // Now execute the student deletion
        if ($deleteStudentStmt->execute()) {
            echo "<script>alert('Student deleted successfully.'); window.location.href = 'admin.php';</script>";
        } else {
            echo "<script>alert('Failed to delete the student.');</script>";
        }
    } else {
        echo "<script>alert('Failed to delete the student job records.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css" />
    <style>
        body {
            font-family: "Roboto", sans-serif;
            background-color: #f8f9fa;
        }

        .dashboard-container {
            padding: 20px;
        }

        .dashboard-header {
            margin-bottom: 30px;
            text-align: center;
        }

        .dashboard-card {
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            background-color: #fff;
            padding: 20px;
            margin-bottom: 20px;
        }

        .logout-btn {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 20px;
            display: inline-block;
        }

       
    </style>
    
</head>
<body>

 <!-- Navigation Bar -->
 <?php require "Nav.php"; ?>
    <header class="hero-section2">

    
    <div class="main_container dashboard-container">
        <h2 class="dashboard-header">Welcome to the Admin Dashboard</h2>

        <!-- Companies Table -->
        <div class="dashboard-card">
            <h5>Manage Companies</h5>
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Company Name</th>
                        <th>Address</th>
                        <th>Contact Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($company = $companyResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($company['CompanyName']); ?></td>
                            <td><?php echo htmlspecialchars($company['Address']); ?></td>
                            <td><?php echo htmlspecialchars($company['ContactNo']); ?></td>
                            <td>
                                <a href="edit_company.php?id=<?php echo $company['UniqueWebsiteID']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                <a href="?delete_company=<?php echo $company['UniqueWebsiteID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this company?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Students Table -->
        <div class="dashboard-card">
            <h5>Manage Students</h5>
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Student Name</th>
                        <th>Registration Number</th>
                        <th>Email</th>
                        <th>Faculty</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($student = $studentResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['FullName']); ?></td>
                            <td><?php echo htmlspecialchars($student['UniRegNo']); ?></td>
                            <td><?php echo htmlspecialchars($student['Email']); ?></td>
                            <td><?php echo htmlspecialchars($student['Faculty']); ?></td>
                            <td>
                                <a href="edit_student.php?id=<?php echo $student['UniRegNo']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                <a href="?delete_student=<?php echo $student['UniRegNo']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <a href="Admin_Login.php" class="logout-btn">Logout</a>
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
