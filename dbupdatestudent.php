<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["studentId"])) {
    // Redirect to the login page or display an error message
    header("Location: login.html");
    exit();
}

// Establish a connection to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbsystem";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the student ID from the session
$studentId = $_SESSION["studentId"];

function phoneNumberToInt($phoneNumber) {
    return (int) preg_replace('/[^0-9]/', '', $phoneNumber);
}

// Retrieve the updated profile data from the request
$firstName = $_POST["firstName"];
$lastName = $_POST["lastName"];
$phoneNumber = phoneNumberToInt($_POST["phoneNumber"]);
$email = $_POST["email"];
$password = $_POST["password"];

// Check if a new profile picture was uploaded
$profilePicture = null;
if (isset($_FILES["profilePicture"]) && $_FILES["profilePicture"]["error"] === UPLOAD_ERR_OK) {
    $tmpName = $_FILES["profilePicture"]["tmp_name"];
    $profilePicture = processProfilePicture($tmpName);
}

// Update the student profile in the database
$sql = "UPDATE tbl_student SET fld_firstname = ?, fld_lastname = ?, fld_phonenumber = ?, fld_email = ?, fld_password = ?";
$parameterTypes = "sssss";
$parameterValues = array($firstName, $lastName, $phoneNumber, $email, $password);

if (isset($profilePicture)) {
    // Update the profile picture in the database only if a new one was uploaded
    $sql .= ", fld_image = ?";
    $parameterTypes .= "s";
    $parameterValues[] = $profilePicture;
}

$sql .= " WHERE fld_studentid = ?";
$parameterTypes .= "s";
$parameterValues[] = $studentId;

// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare($sql);
$stmt->bind_param($parameterTypes, ...$parameterValues);

if ($stmt->execute()) {
    // Return a success response
    $response = array("success" => true);
    echo json_encode($response);
} else {
    // Return an error response
    $response = array("success" => false);
    echo json_encode($response);
}

// Close the database connection
$stmt->close();
$conn->close();

function processProfilePicture($tmpName)
{
    // Generate a unique filename for the profile picture
    $filename = uniqid() . ".jpg";
    $destination = "profile_pictures/" . $filename;

    // Move the uploaded file to the destination folder
    if (move_uploaded_file($tmpName, $destination)) {
        // Return the filename of the saved image, which will be used as the image source
        return $destination;
    } else {
        // If there was an error uploading the file, return null or handle the error
        return null;
    }
}
?>
