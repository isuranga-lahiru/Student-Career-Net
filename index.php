<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student Career Net</title>
    <link
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="styles.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap"
      rel="stylesheet"
    />

  </head>
  <body>
    <!-- Navigation Bar -->
    <?php require "Nav.php"?>

    <!-- Hero Section with Background Image -->
    <header class="hero-section">
      <div class="main_container text-center">
        <h1 class="display-3 text-info font-weight-normal">Welcome to the Student Career Net</h1>
        <p class="lead">Connecting Students with Local Companies</p>
        <a href="registration.php" class="btn btn-primary btn-lg upload-resume"
          >Upload Resume</a
        >
        <a
          href="company_registration.php"
          class="btn btn-outline-light btn-lg view-resume"
          >View Resumes</a
        >
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
  </body>
</html>
