<?php
require_once "PHP/db_connection.php"; // Database connection file


function encodeIndexNumber($regNumber)
{

  return base64_encode($regNumber);
}

// Check if the regNo cookie is set
if (isset($_COOKIE['regNo'])) {
  $regNumber = $_COOKIE['regNo'];

  if (isset($_POST['submit'])) {
    $fullname = $_POST['fullName'];
    $email = $_POST['email'];
    $interests = $_POST['interests'];

    // Handle file upload
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
      $resume = $_FILES['resume'];

      // Check file type (only allow PDF)
      $fileType = mime_content_type($resume['tmp_name']);
      if ($fileType === 'application/pdf') {
        // Check file size (max 2MB)
        if ($resume['size'] <= 2 * 1024 * 1024) {
          // Create unique file name
          // $uniqueId = uniqid($regNumber . "_", true);
          $fileName = $email . ".pdf";

          // Define the upload directory
          $uploadDir = "uploads/";

          // Check and create the directory recursively
          if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true); // Create nested directories with the correct permissions
          }

          $filePath = $uploadDir . $fileName;

          // Move the uploaded file to the target directory
          if (move_uploaded_file($resume['tmp_name'], $filePath)) {
            // Encode the registration number
            $combineUid = encodeIndexNumber($regNumber);

            // Insert into database
            $query = "INSERT INTO `student_job_table`(`UniRegNo`, `Email`, `Interests`) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sss", $regNumber, $email, $interests);

            if ($stmt->execute()) {
              echo "<script>alert('Upload successful!')</script>";
              echo "<script>window.location.href = 'student_dashboard.php';</script>";
            } else {
              echo "<script>alert('Database insert failed!')</script>";
            }
            $stmt->close();
          } else {
            echo "<script>alert('Failed to move uploaded file.')</script>";
          }
        } else {
          echo "<script>alert('File exceeds maximum allowed size of 2MB.')</script>";
        }
      } else {
        echo "<script>alert('Only PDF files are allowed.')</script>";
      }
    } else {
      echo "<script>alert('Please upload a valid resume file.')</script>";
    }
  }
} else {
  // Redirect to registration if cookie is not found
  echo "<script>window.location.href = 'registration.php';</script>";
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
  <link rel="stylesheet" href="styles.css" />

  <style>
    body {
      background-image: url('immmage/resume.jpg'); 
      background-size: cover;
      background-position: center; 
      background-repeat: no-repeat;
     
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

  </style>
</head>

<body>
  <!-- Navigation Bar -->
  <?php require "Nav.php"; ?>


  <header class="formtext">

    <!-- Upload Resume Form -->
    <div class="html main_container text-center animate__animated">
      <h2>Upload Your Resume</h2>
      <form action="upload.php" enctype="multipart/form-data" method="POST" id="resume-form">
        <div class="form-group">
          <label for="name">Full Name</label>
          <input
            type="text"
            class="form-control"
            id="name"
            placeholder="Enter your full name"
            name="fullName"
            required />
        </div>
        <div class="form-group">
          <label for="email">Email Address</label>
          <input
            type="email"
            class="form-control"
            id="email"
            placeholder="Enter your email"
            name="email"
            required />
        </div>

        <!-- Interest Input Field with Tags -->
        <div class="form-group">
          <label for="interests">Interests</label>
          <input
            type="text"
            class="form-control"
            id="interests-input"
            placeholder="Type your interests and press Enter"
            name="interests"
            required />
          <div id="interest-tags" class="mt-2"></div>
        </div>
        <div class="form-group">
          <label for="resume">Upload Resume (PDF, max 2MB)</label>
          <input
            type="file"
            class="form-control-file"
            id="resume"
            accept=".pdf"
            name="resume"
            required />
          <small
            class="form-text text-muted"
            id="file-size-warning"
            style="color: red; display: none">
            The file size exceeds 2MB. Please upload a smaller file.
          </small>
        </div>

        <button type="submit" class="btn btn-primary" name="submit">Submit</button>
      </form>
    </div>
  </header>

  <!-- Footer -->
  <footer class="footer">
    <p>&copy; 2024 Student Career Net. All Rights Reserved.</p>
  </footer>


  <!-- Bootstrap JS and Dependencies -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <!-- Custom Script for Interests Tagging -->
  <script>
    const input = document.getElementById("interests-input");
    const tagContainer = document.getElementById("interest-tags");
    let interests = [];

    input.addEventListener("keypress", function(e) {
      if (e.key === "Enter") {
        e.preventDefault();
        const interest = input.value.trim();
        if (interest && !interests.includes(interest)) {
          interests.push(interest);
          const tag = document.createElement("span");
          tag.className = "badge badge-primary mr-2 mb-2";
          tag.innerHTML = `${interest} <span class="ml-2" style="cursor:pointer;">&times;</span>`;

          // Remove interest when 'x' is clicked
          tag.querySelector("span").addEventListener("click", function() {
            interests = interests.filter((i) => i !== interest);
            tag.remove();
          });

          tagContainer.appendChild(tag);
          input.value = "";
        }
      }
    });

    document.getElementById("resume-form").addEventListener("submit", function(event) {
      var resumeInput = document.getElementById("resume");
      var fileSize = resumeInput.files[0].size / 1024 / 1024; // Convert bytes to MB

      if (fileSize > 2) {
        // If file size is greater than 2MB, show warning and prevent form submission
        document.getElementById("file-size-warning").style.display = "block";
        event.preventDefault();
      } else {
        document.getElementById("file-size-warning").style.display = "none";
      }
    });
  </script>

</body>

</html>