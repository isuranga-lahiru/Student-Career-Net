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
  $companyName   = $_POST['companyName'];
  $address    = $_POST['address'];
  $contactNumber  = $_POST['contactNumber'];

  $combineUid = encodeIndexNumber($address);

  // Check if the company is already registered
  $checkQuery = "SELECT * FROM `company_table` WHERE `UniqueWebsiteID` = ?";
  $checkStmt  = $conn->prepare($checkQuery);
  $checkStmt->bind_param("s", $combineUid);
  $checkStmt->execute();
  $checkStmt->store_result();

  if ($checkStmt->num_rows > 0) {
    setcookie('regNo', $combineUid, time() + (86400 * 30), "/", "", true, true); // Set cookie before any output
    echo "<script>alert('You are already registered ...')</script>";
    echo "<script>window.location.href = 'upload.php';</script>"; // Use JS for redirection
  } else {
    // Insert the new registration details into the database
    $query = "INSERT INTO `company_table`(`CompanyName`, `Address`, `ContactNo`, `UniqueWebsiteID`) VALUES (?, ?, ?, ?)";
    $stmt  = $conn->prepare($query);
    $stmt->bind_param("ssss", $companyName, $address, $contactNumber, $combineUid);

    if ($stmt->execute()) {
      // Registration success
      setcookie('combineUid', $combineUid, time() + (86400 * 30), "/", "", true, true); // Set cookie before any output
      echo "<script>alert('Registration successful')</script>";
      echo "<script>alert('Congratulations! Your unique ID is: {$combineUid}')</script>";
      echo "<script>window.location.href = 'companies.php';</script>"; // Use JS for redirection
    } else {
      // Registration failed
      echo "<script>alert('Something went wrong!')</script>";
      echo "<script>window.location.href = 'company_registration.php';</script>"; // Use JS for redirection
    }
    $stmt->close();
  }
  $checkStmt->close();
}

// Handle login verification
if (isset($_POST['login'])) {
  $companyName = $_POST['CompanyName'];  // Corrected variable name to match form input
  $uniqueId = $_POST['webId'];

  // Check if the provided credentials match any records in the database
  $loginQuery = "SELECT * FROM `company_table` WHERE `CompanyName` = ? AND `UniqueWebsiteID` = ?";
  $loginStmt = $conn->prepare($loginQuery);
  $loginStmt->bind_param("ss", $companyName, $uniqueId); // Corrected parameter binding
  $loginStmt->execute();
  $loginStmt->store_result();

  if ($loginStmt->num_rows > 0) {
    // Credentials match, login successful
    setcookie('combineUid', $uniqueId, time() + (86400 * 30), "/", "", true, true); // Set cookie before any output
    echo "<script>alert('Login successful!')</script>";
    echo "<script>window.location.href = 'companies.php';</script>"; // Redirect to companies page
  } else {
    // Invalid credentials
    echo "<script>alert('Invalid registration details. Please try again.')</script>";
    echo "<script>window.location.href = 'company_registration.php';</script>"; // Redirect to registration page
  }

  $loginStmt->close();
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Companies - Student Career Net</title>
  <link
    href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
    rel="stylesheet" />
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
    rel="stylesheet" />
  <link rel="stylesheet" href="styles.css" />
  <style>
    /* Button Hover Effects */
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

    body {
      background-image: url('immmage/company.gif');
      /* Set the background image */
      display: cover;
      justify-content: center;
      align-items: center;
      height: 100vh;
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

  <?php require "Nav.php"; ?>

  <div class="container-fluid wraper">
    <div class="main_container mt-5">
      <!-- Initial Question Board -->
      <h2 class="animate__animated animate__fadeInDown">
        Are your company already registered?
      </h2>
      <button
        id="already-registered"
        class="btn btn-primary animate__animated animate__bounceIn">
        Yes
      </button>
      <button
        id="not-registered"
        class="btn btn-secondary animate__animated animate__bounceIn">
        No
      </button>
<header class="formtext">
<!-- Verification Form (hidden initially) -->
<div
        class="verification-form mt-4 animate__animated  "
        style="display: none">
        <h3>Verify Your Company Details</h3>
        <form id="verification-form" method="POST" action="company_registration.php">
          <div class="form-group">
            <label for="companyName">Company Name</label>
            <input
              type="text"
              class="form-control"
              id="companyName"
              placeholder="Enter your company name"
              name="CompanyName"
              required />
          </div>
          <div class="form-group">
            <label for="websiteID">Website ID</label>
            <input
              type="text"
              class="form-control"
              id="websiteID"
              placeholder="Enter your website ID"
              name="webId"
              required />
          </div>
          <button type="submit" class="btn btn-primary" name="login">Verify</button>
        </form>
      </div>

      <!-- Registration Form (hidden initially) -->
      <div
        class="registration-form mt-4 animate__animated"
        style="display: none">
        <h3>Company Registration</h3>
        <form id="registration-form" action="company_registration.php" method="POST">
          <div class="form-group">
            <label for="regCompanyName">Company Name</label>
            <input
              type="text"
              class="form-control"
              id="regCompanyName"
              placeholder="Enter company name"
              name="companyName"
              required />
          </div>
          <div class="form-group">
            <label for="regAddress">Address</label>
            <input
              type="text"
              class="form-control"
              id="regAddress"
              placeholder="Enter address"
              name="address"
              required />
          </div>
          <div class="form-group">
            <label for="regContact">Contact Number</label>
            <input
              type="text"
              class="form-control"
              id="regContact"
              placeholder="Enter contact number"
              name="contactNumber"
              required />
          </div>
          <button type="submit" class="btn btn-primary" name="register">Submit</button>
        </form>
      </div>
</header>
      

      <!-- Modal for Success/Failure Messages -->
      <div
        class="modal fade"
        id="messageModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="messageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content animate__animated animate__zoomIn">
            <div class="modal-header">
              <h5 class="modal-title" id="messageModalLabel"></h5>
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

  <!-- Footer -->
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





</body>

</html>