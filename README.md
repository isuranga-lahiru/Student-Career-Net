# Student-Career Project

This project is a web-based application designed to manage and track student career information. Follow the steps below to set up the project on your local machine using XAMPP.

## Prerequisites
- **XAMPP**: You can download and install XAMPP from [here](https://www.apachefriends.org/download.html).
- **Web Browser**: To access the web interface of the application (e.g., Chrome, Firefox).
- **Student-Career SQL File**: Ensure you have the `student_career.sql` file for database setup.

## Installation Steps

### Step 1: Install XAMPP
1. Download and install XAMPP from [the official website](https://www.apachefriends.org/download.html).
2. Complete the installation following the instructions for your operating system.

### Step 2: Start Apache and MySQL
1. Open the XAMPP Control Panel.
2. Start both **Apache** and **MySQL** services by clicking the "Start" buttons next to them.

### Step 3: Import the Database
1. Open your web browser and navigate to `http://localhost/phpmyadmin`.
2. In the phpMyAdmin interface, go to the **Import** tab.
3. Click **Choose File** and select the `student_career.sql` file.
4. Click **Go** to import the tables into your MySQL database.

### Step 4: Set Up Project Files
1. Copy all the project files to the `C:/xampp/htdocs` folder.
2. Ensure the files are placed correctly inside the `htdocs` directory.

### Step 5: Run the Application
1. Open your web browser and navigate to `http://localhost`.
2. You should now see the website for the Student-Career project.

## Troubleshooting
- **Ports**: Ensure that the default ports (80 for Apache, 3306 for MySQL) are not being used by other applications.
- **PHP Errors**: If you encounter PHP-related errors, ensure your PHP configuration is correct by checking the `php.ini` file within the XAMPP directory.
- **Database Connection**: If there are issues connecting to the database, verify the `config.php` file for the correct database credentials (username, password, database name).
