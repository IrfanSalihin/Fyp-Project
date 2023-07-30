<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "dbsystem";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Set the form input values
    $staffId = $_POST["staffId"];
    $staffFirstName = $_POST["staffFirstName"];
    $staffLastName = $_POST["staffLastName"];
    $phoneNumber = $_POST["phoneNumber"];
    $staffEmail = $_POST["staffEmail"];
    $staffPassword = $_POST["staffPassword"];

    // Step 3: Validate form data
    // Perform necessary validations (e.g., check if the fields are not empty, validate email format, etc.)

    // Handle the uploaded profile picture
    $profilePicture = $_FILES["profilePicture"];

    if ($profilePicture['error'] === UPLOAD_ERR_OK) {
        $profilePictureData = file_get_contents($profilePicture['tmp_name']);
        $profilePictureData = base64_encode($profilePictureData); // Encode the image data to store in the database
    } else {
        // Handle the error if the profile picture upload fails
        echo "Error uploading profile picture: " . $profilePicture['error'];
        exit;
    }

    // Step 5: Insert data into the tbl_staff table
    $sql = "INSERT INTO tbl_staff (fld_staffid, fld_firstname, fld_lastname, fld_phonenumber, fld_email, fld_password, fld_image)
            VALUES ('$staffId', '$staffFirstName', '$staffLastName', '$phoneNumber', '$staffEmail', '$staffPassword', '$profilePictureData')";

    if ($conn->query($sql) === TRUE) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the database connection
    $conn->close();
}
?>
