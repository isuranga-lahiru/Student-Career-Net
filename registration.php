<?php
require_once "PHP/db_connection.php"; // database connection file imported
function encodeIndexNumber($indexNumber)
{
  $base64Encoded = base64_encode($indexNumber);
  $customEncoded = strtr($base64Encoded, [
    '+' => 'U', // Replace '+' with 'U'
    '/' => 'S', // Replace '/' with 'S'
    '=' => 'D'  // Replace '=' with 'D'
  ]);
  $finalEncoded = substr(str_shuffle($customEncoded), 0, 8);
  return $finalEncoded;
}
if (isset($_POST['register'])) {
  $fullname   = $_POST['fullname'];
  $faculty    = $_POST['faculty'];
  $regNumber  = $_POST['regNumber'];
  $acedemicyr = $_POST['acedemicyr'];

  // Check if the student is already registered
  $checkQuery = "SELECT * FROM `student_table` WHERE `UniRegNo` = ?";
  $checkStmt  = $conn->prepare($checkQuery);
  $checkStmt->bind_param("s", $regNumber);
  $checkStmt->execute();
  $checkStmt->store_result();
  if ($checkStmt->num_rows > 0) {
    setcookie('regNo', $regNumber, time() + (86400 * 30), "/", "", true, true); // Set cookie before any output
    echo "<script>alert('You are already registered ...')</script>";
    echo "<script>window.location.href = 'upload.php';</script>"; // Use JS for redirection
  } else {
    // Generate a unique ID using base64 encoding
    $combineUid = encodeIndexNumber($regNumber);
    // Insert the new registration details into the database
    $query = "INSERT INTO `Student_Table` (`FullName`, `Faculty`, `UniRegNo`, `AcademicYear`, `UniqueID`) VALUES (?, ?, ?, ?, ?)";
    $stmt  = $conn->prepare($query);
    $stmt->bind_param("sssss", $fullname, $faculty, $regNumber, $acedemicyr, $combineUid);
    if ($stmt->execute()) {
      // Registration success
      setcookie('regNo', $regNumber, time() + (86400 * 30), "/", "", true, true); // Set cookie before any output
      echo "<script>alert('Registration successful')</script>";
      echo "<script>alert('Congratulations! Your unique ID is: {$combineUid}')</script>";
      echo "<script>window.location.href = 'upload.php';</script>"; // Use JS for redirection
    } else {
      // Registration failed
      echo "<script>alert('Something went wrong!')</script>";
      echo "<script>window.location.href = 'registration.php';</script>"; // Use JS for redirection
    }
    $stmt->close();
  }
  $checkStmt->close();
}
// Handle login verification
if (isset($_POST['login'])) {
  $regNumber = $_POST['regNo'];
  $uniqueId  = $_POST['uniqueId'];
  // Check if the provided credentials match any records in the database
  $loginQuery = "SELECT * FROM `student_table` WHERE `UniRegNo` = ? AND `UniqueID` = ?";
  $loginStmt  = $conn->prepare($loginQuery);
  $loginStmt->bind_param("ss", $regNumber, $uniqueId);
  $loginStmt->execute();
  $loginStmt->store_result();
  if ($loginStmt->num_rows > 0) {
    // Credentials match, login successful
    setcookie('regNo', $regNumber, time() + (86400 * 30), "/", "", true, true); // Set cookie before any output
    echo "<script>alert('Login successful!')</script>";
    echo "<script>window.location.href = 'student_dashboard.php';</script>"; // Redirect to upload page
  } else {
    // Invalid credentials
    echo "<script>alert('Invalid registration details. Please try again.')</script>";
    echo "<script>window.location.href = 'registration.php';</script>"; // Redirect to registration page
  }
  $loginStmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Upload Resume - Student Career Net</title>
  <link
    href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
    rel="stylesheet" />
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
    rel="stylesheet" />
  <link rel="stylesheet" href="styles.css" />
  <style>
    body {
      background: url("immmage/resume.jpg");
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
    }

    .navbar {
      background: transparent;
    }

 

    #already-registered,
    #not-registered,
    .btn-primary {
      transition: all 0.3s ease-in-out;
    }

    #already-registered:hover,
    #not-registered:hover,
    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    /* Animated Modal */
    .modal-content {
      animation: fadeInModal 0.5s;
    }

    @keyframes fadeInModal {
      0% {
        opacity: 0;
        transform: translateY(-100px);
      }

      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Question Board Styles */
    .question-board {
      background-color: rgba(255, 255, 255, 0.6);
      /* Semi-transparent white background */
      border-radius: 10px;
      /* Optional: Rounded corners */
      padding: 20px;
      /* Add padding for spacing */
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
      /* Optional: Subtle shadow for depth */
    }

    .question-board h2 {
      color: #333;
      /* Darker text color for the question */
      text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.7);
      /* Light shadow for readability */
    }

    .question-board button {
      margin: 10px 0;
      /* Add spacing between buttons */
    }
  </style>


</head>

<body>
  <!-- Navigation Bar -->
  <?php require 'Nav.php' ?>
  <!-- Question Board -->
  <div class="container-fluid wraper">
    <div class="main_container">
      <h2 class="animate__animated animate__fadeInDown">
        Are you already registered?</h2>
      <button id="already-registered"
        class="btn btn-primary animate__animated animate__bounceIn">Yes</button>
      <button id="not-registered"
        class="btn btn-secondary animate__animated animate__bounceIn">No</button>


      <!-- Registration Form (hidden initially) -->
      <div class="registration-form mt-4 animate__animated formtext" style="display: none">
        <h3>Register</h3>
        <form id="registration-form" method="POST" action="registration.php">
          <div class="form-group">
            <label for="fullName">Full Name</label>
            <input
              type="text"
              class="form-control"
              id="fullName"
              placeholder="Enter your full name"
              name="fullname"
              required />
          </div>
          <div class="form-group">
            <label for="faculty">Faculty</label>
            <input
              type="text"
              class="form-control"
              id="faculty"
              placeholder="Enter your faculty"
              name="faculty"
              required />
          </div>
          <div class="form-group">
            <label for="registrationNumber">University Registration Number</label>
            <input
              type="text"
              class="form-control"
              id="registrationNumber"
              placeholder="Enter your registration number"
              name="regNumber"
              required />
          </div>
          <div class="form-group">
            <label for="academicYear">Academic Year</label>
            <input
              type="text"
              class="form-control"
              id="academicYear"
              placeholder="Enter your academic year"
              name="acedemicyr"
              required />
          </div>
          <button type="submit" class="btn btn-primary" name="register">Submit</button>
        </form>
      </div>
      <!-- Verification Form (hidden initially) -->
      <div class="verification-form mt-4 animate__animated" style="display: none">
        <h3>Verify Your Details</h3>
        <form id="verification-form" method="POST" action="registration.php">
          <div class="form-group">
            <label for="verifyID">Registration ID</label>
            <input
              type="text"
              class="form-control"
              id="verifyID"
              name="uniqueId"
              placeholder="Enter your registration ID"
              required />
          </div>
          <div class="form-group">
            <label for="verifyRegNumber">University Registration Number</label>
            <input
              type="text"
              class="form-control"
              id="verifyRegNumber"
              name="regNo"
              placeholder="Enter your university registration number"
              required />
          </div>
          <button type="submit" class="btn btn-primary" name="login">Verify</button>
        </form>
      </div>
      <!-- Modal for Success/Failure Messages -->
      <div
        class="modal fade"
        id="successModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="successModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="successModalLabel"></h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" id="redirect-btn">
                OK
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <footer class="footer">
    <p>&copy; 2024 Student Career Net. All Rights Reserved.</p>
  </footer>


  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <!-- Custom JavaScript -->
  <script>
    document
      .getElementById("already-registered")
      .addEventListener("click", function() {
        $(".registration-form").hide("slide", {
          direction: "left"
        }, 600);
        $(".verification-form")
          .show("slide", {
            direction: "right"
          }, 600)
          .addClass("animate__fadeInRight");
      });
    document
      .getElementById("not-registered")
      .addEventListener("click", function() {
        $(".verification-form").hide("slide", {
          direction: "right"
        }, 600);
        $(".registration-form")
          .show("slide", {
            direction: "left"
          }, 600)
          .addClass("animate__fadeInLeft");
      });
  </script>



  <!-- Footer -->




</body>


</html>