<?php
session_start();

// Establish a connection to the database
$servername = "localhost"; // Replace with your server name
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "dbsystem"; // Replace with the name of your database

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve the form data
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Prepare the SQL statements for student and staff logins
    $studentStmt = $conn->prepare("SELECT * FROM tbl_student WHERE fld_studentid = ? AND fld_password = ?");
    $staffStmt = $conn->prepare("SELECT * FROM tbl_staff WHERE fld_staffid = ? AND fld_password = ?");
    
    // Bind parameters and execute the student login query
    $studentStmt->bind_param("ss", $username, $password);
    $studentStmt->execute();
    $studentResult = $studentStmt->get_result();
    
    // Bind parameters and execute the staff login query
    $staffStmt->bind_param("ss", $username, $password);
    $staffStmt->execute();
    $staffResult = $staffStmt->get_result();

    // Check if a matching record was found in the student table
    if ($studentResult->num_rows === 1) {
        // Authentication successful, set session variables for student
        $studentRow = $studentResult->fetch_assoc();
        $_SESSION["studentId"] = $studentRow["fld_studentid"];
        $_SESSION["firstName"] = $studentRow["fld_firstname"];
        $_SESSION["lastName"] = $studentRow["fld_lastname"];

        // Redirect to the desired page after successful student login
        header("Location: mainpagestudent.html");
        exit();
    }

    // Check if a matching record was found in the staff table
    if ($staffResult->num_rows === 1) {
        // Authentication successful, set session variables for staff
        $staffRow = $staffResult->fetch_assoc();
        $_SESSION["staffId"] = $staffRow["fld_staffid"];
        $_SESSION["firstName"] = $staffRow["fld_firstname"];
        $_SESSION["lastName"] = $staffRow["fld_lastname"];

        // Redirect to the desired page after successful staff login
        header("Location: mainpagestaff.html");
        exit();
    }

    // Invalid login credentials, display an error message
    echo "Invalid username or password.";

    // Close the statements
    $studentStmt->close();
    $staffStmt->close();
}

// Close the database connection
$conn->close();
?>
