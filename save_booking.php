<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Assuming you have already established a connection to your database
    $dbHost = "localhost";
    $dbUser = "root";
    $dbPass = "";
    $dbName = "dbsystem";

    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $equipmentName = $_POST["equipmentName"];
    $quantity = $_POST["quantity"];
    $preferredDate = $_POST["preferredDate"];
    $preferredStartTime = $_POST["preferredStartTime"];
    $preferredEndTime = $_POST["preferredEndTime"];
    $studentId = $_POST["studentId"];
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $email = $_POST["email"];
    $phoneNumber = $_POST["phoneNumber"];

    // Retrieve the initial quantity from tbl_equipmentlist
    $getInitialQuantityQuery = "SELECT fld_quantity FROM tbl_equipmentlist WHERE fld_equipname = '$equipmentName'";
    $initialQuantityResult = $conn->query($getInitialQuantityQuery);
    $initialQuantity = 0; // Default value

    if ($initialQuantityResult->num_rows == 1) {
        $initialQuantityRow = $initialQuantityResult->fetch_assoc();
        $initialQuantity = $initialQuantityRow["fld_quantity"];
    }

    // SQL query to insert the data into the tbl_booking table
    $insertBookingQuery = "INSERT INTO tbl_booking (equipment_name, quantity, original_quantity, preferred_date, preferred_start_time, preferred_end_time, student_id, first_name, last_name, email, phone_number) 
                           VALUES ('$equipmentName', '$quantity', '$initialQuantity', '$preferredDate', '$preferredStartTime', '$preferredEndTime', '$studentId', '$firstName', '$lastName', '$email', '$phoneNumber')";

    // Perform the insert query to add the booking to tbl_booking
    if ($conn->query($insertBookingQuery) === TRUE) {
        echo "Booking saved successfully!";
    } else {
        echo "Error saving booking: " . $conn->error;
    }

    $conn->close();
}
?>
