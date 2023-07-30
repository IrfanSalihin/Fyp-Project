<?php
// Step 1: Connect to the database (replace the placeholders with your actual database credentials)
$host = "localhost";
$username = "root";
$password = "";
$database = "dbsystem";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 2: Retrieve the form data
$studentId = $_POST['studentId'];
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$phoneNumber = $_POST['phoneNumber'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];

// Step 3: Validate form data
// Perform necessary validations (e.g., check if the fields are not empty, validate email format, etc.)

// Step 4: Handle the profile picture upload
$profilePicture = $_FILES['profilePicture'];

if ($profilePicture['error'] === UPLOAD_ERR_OK) {
    $profilePictureData = file_get_contents($profilePicture['tmp_name']);
    $profilePictureData = base64_encode($profilePictureData); // Encode the image data to store in the database
} else {
    // Handle the error if the profile picture upload fails
    echo "Error uploading profile picture: " . $profilePicture['error'];
    exit;
}

// Step 5: Insert data into the tbl_student table
$sql = "INSERT INTO tbl_student (fld_studentid, fld_firstname, fld_lastname, fld_phonenumber, fld_email, fld_password, fld_image)
        VALUES ('$studentId', '$firstName', '$lastName', '$phoneNumber', '$email', '$password', '$profilePictureData')";

if ($conn->query($sql) === TRUE) {
    echo "Registration successful!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close the database connection
$conn->close();
?>
