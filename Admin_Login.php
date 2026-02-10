<?php
require_once "PHP/db_connection.php"; // database connection file imported
if (isset($_POST['login'])) {
  $uname = $_POST['uname'];
  $pw  = $_POST['pw'];

  // Check if the provided credentials match any records in the database
  $loginQuery = "SELECT * FROM `Admin` WHERE `Email` = ? AND `Password` = ?";
  $loginStmt  = $conn->prepare($loginQuery);
  $loginStmt->bind_param("ss", $uname, $pw);
  $loginStmt->execute();
  $loginStmt->store_result();

  if ($loginStmt->num_rows > 0) {
      // Credentials match, login successful
      setcookie('email', $uname, time() + (86400 * 30), "/", "", true, true); // Set cookie before any output
      echo "<script>alert('Login successful!')</script>";
      echo "<script>window.location.href = 'admin.php';</script>"; // Redirect to upload page
  } else {
      // Invalid credentials
      echo "<script>alert('Invalid registration details. Please try again.')</script>";
      echo "<script>window.location.href = 'admin_login.php';</script>"; // Redirect to registration page
  }

  $loginStmt->close();
}


?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login</title>
    <link
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="styles.css" />
    <style>
      body {
        font-family: "Roboto", sans-serif;
        background-color: #f4f4f9;
      }

      .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background:url("immmage/Admine1.gif");
        background-repeat: no-repeat;
        background-size:cover;
      }

      .login-box {
        padding: 40px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.4);
        border-radius: 16px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(8.4px);
        -webkit-backdrop-filter: blur(8.4px);
        border: 1px solid rgba(255, 255, 255, 0.3);
      }

      .login-box h2 {
        text-align: center;
        margin-bottom: 30px;
      }

      .btn-primary {
        width: 100%;
      }

      .navbar {
    background-color: transparent !important;
    position: fixed;
    width: 100%;
    z-index: 3;
      }

      ul li{
    padding:10px 20px;
    transition:.5s ease-in-out;
  }

  ul li:hover{
    background:#333;
    border-radius:10px;
  }



      .error-msg {
        color: red;
        display: none;
        text-align: center;
        margin-top: 10px;
      }
      /* Back to Home button in the upper-left corner */
      .back-to-home {
        position: absolute;
        top: 10px;
        left: 10px;
        padding: 5px 10px;
        background-color: #6c757d;
        color: white;
        border-radius: 5px;
        text-decoration: none;
      }
      .back-to-home:hover {
        background-color: #5a6268;
        text-decoration: none;
      }
      
    </style>
  </head>
  <body>

   
    <!-- Navigation Bar -->
    <?php require "Nav.php"; ?>

    <div class="login-container">
      <div class="login-box">
       
        <h2>Admin Login</h2>
        <form id="adminLoginForm" action="Admin_Login.php" method="POST">
          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" name="uname" id="username" required />
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <input
              type="password"
              name="pw"
              class="form-control"
              id="password"
              required
            />
          </div>
          <button type="submit" class="btn btn-primary" name="login">Login</button>
          <!-- <p class="error-msg" id="errorMsg" hidden>Invalid Username or Password</p> -->
        </form>
      </div>
    </div>

    
     <!-- Footer -->
     <footer class="footer">
      <p>&copy; 2024 Student Career Net. All Rights Reserved.</p>
    </footer>
 
  </body>
</html>
