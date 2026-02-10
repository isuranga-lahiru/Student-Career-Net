<?php
require_once "PHP/db_connection.php"; // Database connection

// Check if the admin is logged in by verifying the cookie
if (!isset($_COOKIE['email'])) {
    echo "<script>alert('Please log in first.')</script>";
    echo "<script>window.location.href = 'index.php';</script>";
    exit();
}

// Check if the company ID is passed via URL
if (isset($_GET['id'])) {
    $companyId = $_GET['id'];

    // Fetch company details
    $companyQuery = "SELECT * FROM `company_table` WHERE `UniqueWebsiteID` = ?";
    $companyStmt = $conn->prepare($companyQuery);
    $companyStmt->bind_param("s", $companyId);
    $companyStmt->execute();
    $companyResult = $companyStmt->get_result();

    // Check if the company exists
    if ($companyResult->num_rows === 1) {
        $company = $companyResult->fetch_assoc();
    } else {
        echo "<script>alert('Company not found.')</script>";
        echo "<script>window.location.href = 'admin.php';</script>";
        exit();
    }

    // Handle form submission for editing company
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $companyName = $_POST['company_name'];
        $address = $_POST['address'];
        $contactNo = $_POST['contact_no'];

        // Update company details in the database
        $updateQuery = "UPDATE `company_table` SET `CompanyName` = ?, `Address` = ?, `ContactNo` = ? WHERE `UniqueWebsiteID` = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ssss", $companyName, $address, $contactNo, $companyId);

        if ($updateStmt->execute()) {
            echo "<script>alert('Company details updated successfully.')</script>";
            echo "<script>window.location.href = 'admin.php';</script>";
        } else {
            echo "<script>alert('Failed to update company details.')</script>";
        }
    }
} else {
    echo "<script>alert('No company ID provided.')</script>";
    echo "<script>window.location.href = 'admin.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Company</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <!-- Navigation Bar -->
    <?php require "Nav.php"; ?>

    <header class="hero-section2 company-table-data">
    <div class="main_container">
        <h2 class="my-4">Edit Company Details</h2>

        <form action="" method="POST">
            <div class="form-group">
                <label for="company_name">Company Name</label>
                <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo htmlspecialchars($company['CompanyName']); ?>" required>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($company['Address']); ?>" required>
            </div>

            <div class="form-group">
                <label for="contact_no">Contact Number</label>
                <input type="text" class="form-control" id="contact_no" name="contact_no" value="<?php echo htmlspecialchars($company['ContactNo']); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Company</button>
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
