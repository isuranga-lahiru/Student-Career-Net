<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Upload Resume - Student Career Net</title>
    <link
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light">
      <a class="navbar-brand" href="#">
        <img src="immmage/logo2.jpg" alt="Portal Logo" width="50" />
      </a>
      <button
        class="navbar-toggler"
        type="button"
        data-toggle="collapse"
        data-target="#navbarNav"
        aria-controls="navbarNav"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="index.html">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="registration.html">Upload Resume</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="company_registration.html">Companies</a>
          </li>
        </ul>
      </div>
    </nav>

    <!-- Upload Resume Form -->
    <div class="container mt-5">
      <h2>Upload Your Resume</h2>
      <form>
        <div class="form-group">
          <label for="name">Full Name</label>
          <input
            type="text"
            class="form-control"
            id="name"
            placeholder="Enter your full name"
          />
        </div>
        <div class="form-group">
          <label for="email">Email Address</label>
          <input
            type="email"
            class="form-control"
            id="email"
            placeholder="Enter your email"
          />
        </div>

        <!-- Interest Input Field with Tags -->
        <div class="form-group">
          <label for="interests">Interests</label>
          <input
            type="text"
            class="form-control"
            id="interests-input"
            placeholder="Type your interests and press Enter"
          />
          <div id="interest-tags" class="mt-2"></div>
        </div>

        <div class="form-group">
          <label for="resume">Upload Resume (PDF, max 2MB)</label>
          <input
            type="file"
            class="form-control-file"
            id="resume"
            accept=".pdf"
            required
          />
          <small
            class="form-text text-muted"
            id="file-size-warning"
            style="color: red; display: none"
          >
            The file size exceeds 2MB. Please upload a smaller file.
          </small>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
      </form>
    </div>

    <!-- Footer -->
    <footer class="footer">
      <p>&copy; 2024 Student Career Net. All Rights Reserved.</p>
    </footer>

    <!-- Bootstrap JS and Dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Custom Script for Interests Tagging (if needed) -->
    <script>
      const input = document.getElementById("interests-input");
      const tagContainer = document.getElementById("interest-tags");
      let interests = [];

      input.addEventListener("keypress", function (e) {
        if (e.key === "Enter") {
          e.preventDefault();
          const interest = input.value.trim();
          if (interest && !interests.includes(interest)) {
            interests.push(interest);
            const tag = document.createElement("span");
            tag.className = "badge badge-primary mr-2 mb-2";
            tag.innerHTML = `${interest} <span class="ml-2" style="cursor:pointer;">&times;</span>`;

            // Remove interest when 'x' is clicked
            tag.querySelector("span").addEventListener("click", function () {
              interests = interests.filter((i) => i !== interest);
              tag.remove();
            });

            tagContainer.appendChild(tag);
            input.value = "";
          }
        }
      });

      document
        .getElementById("resume-form")
        .addEventListener("submit", function (event) {
          var resumeInput = document.getElementById("resume");
          var fileSize = resumeInput.files[0].size / 1024 / 1024; // Convert bytes to MB

          if (fileSize > 2) {
            // If file size is greater than 2MB, show warning and prevent form submission
            document.getElementById("file-size-warning").style.display =
              "block";
            event.preventDefault();
          } else {
            document.getElementById("file-size-warning").style.display = "none";
          }
        });
    </script>
  </body>
</html>
<?php
if (isset($_SESSION['unique_id'])) {

  
  
}
  
?>