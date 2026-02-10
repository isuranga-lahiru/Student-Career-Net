<?php
require_once "PHP/db_connection.php"; // Database connection

// Check if the company is logged in by verifying the cookie
if (!isset($_COOKIE['combineUid'])) {
    echo "<script>alert('Please log in first.')</script>";
    echo "<script>window.location.href = 'company_registration.php';</script>";
    exit();
}

$combineUid = $_COOKIE['combineUid'];

// Fetch company details
$companyQuery = "SELECT * FROM `company_table` WHERE `UniqueWebsiteID` = ?";
$companyStmt = $conn->prepare($companyQuery);
$companyStmt->bind_param("s", $combineUid);
$companyStmt->execute();
$companyResult = $companyStmt->get_result();
$company = $companyResult->fetch_assoc();

// Fetch associated student details
$studentQuery = "SELECT * FROM `student_table` JOIN  `student_job_table` ON student_table.UniRegNo=student_job_table.UniRegNo";
$studentStmt = $conn->prepare($studentQuery);
$studentStmt->execute();
$studentResult = $studentStmt->get_result();

// Handle email sending

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Adjust these paths to match where you've extracted PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

////////////////////////// Handle email sending


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sendEmail'])) {
    // Validate email
    $studentEmail = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $fullName = htmlspecialchars($_POST['FullName'], ENT_QUOTES, 'UTF-8');


    // Sanitize company name
    $companyName = htmlspecialchars($company['CompanyName'], ENT_QUOTES, 'UTF-8');
    $companyEmail = "student.career.eusl@gmail.com"; // Replace with the actual company email
    $companyAddress = htmlspecialchars($company['Address'], ENT_QUOTES, 'UTF-8');
    $companyContact = htmlspecialchars($company['ContactNo'], ENT_QUOTES, 'UTF-8');

    if ($studentEmail) { // Proceed only if the email is valid
        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->SMTPDebug = 0;                       // Disable verbose debug output for production
            $mail->isSMTP();                            // Set mailer to use SMTP
            $mail->Host       = 'smtp.gmail.com';       // Specify Gmail SMTP server
            $mail->SMTPAuth   = true;                   // Enable SMTP authentication
            $mail->Username   = 'student.career.eusl@gmail.com'; // SMTP username
            $mail->Password   = 'ksvf acrx hmpp xker';  // SMTP password (App Password)
            $mail->SMTPSecure = 'tls';                  // Enable TLS encryption
            $mail->Port       = 587;                    // TCP port to connect to

            // Recipients
            $mail->setFrom('student.career.eusl@gmail.com', $companyName);
            $mail->addAddress($studentEmail);  // Add recipient's email

            // Content


            $mail->isHTML(true);  // Set email format to HTML
            $mail->Subject = 'Invitation for Interview with ' . $companyName;

            $mail->Body = '
                <html>
                    <head>
                        <style>
                            body {
                                font-family: "Poppins", sans-serif;
                                font-weight: 100;
                                font-style: normal;
                                background-color: #f4f4f4;
                                margin: 0;
                                padding: 20px;
                            }
                            .email-container {
                                background-color: #ffffff;
                                border-radius: 10px;
                                padding: 20px;
                                max-width: 600px;
                                margin: 0 auto;
                                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                            }
                            .header {
                                background-color: #4CAF50;
                                color: #ffffff;
                                padding: 10px;
                                border-radius: 10px 10px 0 0;
                                text-align: center;
                            }
                            .header h1 {
                                margin: 0;
                                font-size: 24px;
                            }
                            .content {
                                padding: 20px;
                            }
                            .content p {
                                font-size: 16px;
                                color: #333;
                            }
                            .footer {
                                margin-top: 20px;
                                font-size: 14px;
                                color: #777;
                                text-align: center;
                            }

                            .footer p {
                                margin: 5px 0;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="email-container">
                            <div class="header">
                                <h1>Interview Invitation</h1>
                            </div>
                            <div class="content">
                                <p>Dear ' . htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') . ',</p>
                                <p>
                                    We are pleased to inform you that we would like to invite you to an interview to discuss your qualifications and experience further.
                                </p>
                                <p>
                                    Please find the details below:
                                </p>
                                <p>
                                    <strong>Address:</strong>' . htmlspecialchars($companyAddress, ENT_QUOTES, 'UTF-8') . '<br>
                                    <strong>Company Name:</strong> ' . htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8') . '<br>
                                    <strong>Contact Number:</strong> ' . htmlspecialchars($companyContact, ENT_QUOTES, 'UTF-8') . '
                                </p>
                                <p>We look forward to meeting with you.</p>
                            </div>
                            <div class="footer">
                                <p>Best regards,</p>
                                <p><strong>' . htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8') . '</strong></p>
                            </div>
                        </div>
                    </body>
                </html>
            ';

            $mail->AltBody = 'Dear ' . htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') . ',
                We are pleased to inform you that we would like to invite you to an interview to discuss your qualifications and experience further.

                For further information:
                Address: ' . htmlspecialchars($companyAddress, ENT_QUOTES, 'UTF-8') . '
                Company Name: ' . htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8') . '
                Contact Number: ' . htmlspecialchars($companyContact, ENT_QUOTES, 'UTF-8') . '

                We look forward to meeting with you.';



            // Send the email
            $mail->send();
            echo "<script>alert('Email sent successfully!');</script>";
        } catch (Exception $e) {
            echo "<script>alert('Failed to send email. Error: {$mail->ErrorInfo}');</script>";
        }
    } else {
        echo "<script>alert('Invalid email address.');</script>";
    }
}

/////////////////

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Details - Student Career Net</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .logout-btn {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 20px;
            display: inline-block;
        }

        body {
            background-image: url('immmage/company_de.gif');
            /* Set the background image */
            background-size: cover;
            /* Cover the entire screen */
            background-position: center;
            /* Center the background image */
            background-repeat: no-repeat;
            /* Prevent repetition */
        }
    </style>
</head>

<body>


    <!-- Navigation Bar -->
    <?php require "Nav.php" ?>


    <div class="container-fluid wraper">
        <div class="formtext container mt-5">
            <!-- Company Details Section -->
            <h2 class="text-center">Company Details</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="card-title"><?php echo htmlspecialchars($company['CompanyName']); ?></h4>
                    <p class="card-text">
                        <strong>Address:<span class="database-imorted"> <?php echo htmlspecialchars($company['Address']); ?></span></strong><br>
                        <strong>Contact Number:<span class="database-imorted"><?php echo htmlspecialchars($company['ContactNo']); ?></span></strong><br>
                        <strong>Unique ID:<span class="database-imorted"><?php echo htmlspecialchars($company['UniqueWebsiteID']); ?></span></strong>
                    </p>
                </div>
            </div>

            <!-- Student Details Section -->
            <h3 class="text-center">Associated Students</h3>
            <?php if ($studentResult->num_rows > 0): ?>
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Student Name</th>
                            <th>Faculty</th>
                            <th>University Registration Number</th>
                            <th>Email</th>
                            <th>Interests</th>
                            <th>CV-Link</th>
                            <th>Action</th> <!-- New column for sending email -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($student = $studentResult->fetch_assoc()): ?>
                            <tr>
                                <td><span class="company-table-data"><?php echo htmlspecialchars($student['FullName']); ?></span></td>
                                <td><span class="company-table-data"><?php echo htmlspecialchars($student['Faculty']); ?></span></td>
                                <td><span class="company-table-data"><?php echo htmlspecialchars($student['UniRegNo']); ?></span></td>
                                <td><span class="company-table-data"><?php echo htmlspecialchars($student['Email']); ?></span></td>
                                <td><span class="company-table-data"><?php echo htmlspecialchars($student['Interests']); ?></span></td>
                                <td>
                                    <a href="<?php echo 'uploads/' . htmlspecialchars($student['Email']) . '.pdf'; ?>" target="_blank">View CV</a> <!-- CV link -->
                                </td>

                                <td>
                                    <!-- Send Email Form -->
                                    <form method="POST">
                                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($student['Email']); ?>">
                                        <input type="hidden" name="FullName" value="<?php echo htmlspecialchars($student['FullName']); ?>">
                                        <button type="submit" name="sendEmail" class="btn btn-primary">Send Email</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center">No students associated with this company yet.</p>
            <?php endif; ?>
            <a href="index.php" class="logout-btn">Logout</a>
        </div>
    </div>
    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 Student Career Net. All Rights Reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>