<?php
// Assuming you have already established a connection to your database
$dbHost = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "dbsystem";

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Booking Status</title>
</head>

<body>
    <?php
    // Set the server timezone to Asia/Kuala_Lumpur
    date_default_timezone_set('Asia/Kuala_Lumpur');

    // Get the current server time in the format "H:i:s"
    $currentDateTime = date('H:i:s');
    echo "Server Time: $currentDateTime<br>";

    // SQL query to retrieve bookings
    $bookingsQuery = "SELECT id, equipment_name, preferred_start_time, preferred_end_time, quantity FROM tbl_booking";
    $bookingsResult = $conn->query($bookingsQuery);

    if ($bookingsResult->num_rows > 0) {
        $conn->begin_transaction();

        try {
            // Loop through the bookings and update the status and quantity based on the current time
            while ($row = $bookingsResult->fetch_assoc()) {
                $bookingId = $row["id"];
                $equipmentName = $row["equipment_name"];
                $preferredStartTime = $row["preferred_start_time"];
                $preferredEndTime = $row["preferred_end_time"];
                $quantity = $row["quantity"];

                echo "Booking ID: $bookingId, Preferred Start Time: $preferredStartTime, Preferred End Time: $preferredEndTime<br>";

                // Get the current server timestamp (Unix timestamp)
                $currentTimestamp = time();

                // Convert the preferred start and end times to timestamps (Unix timestamps)
                $startTimestamp = strtotime($preferredStartTime);
                $endTimestamp = strtotime($preferredEndTime);

                // Initialize the status variable
                $status = '';

                // Check if the current time is within the interval of the preferred start and end times
                if ($currentTimestamp >= $startTimestamp && $currentTimestamp <= $endTimestamp) {
                    // Check if the booking quantity has not been deducted yet
                    if ($quantity > 0) {
                        $status = 'active';
                        // Deduct the exact quantity when the booking is active
                        $updateQuantityQuery = "UPDATE tbl_equipmentlist SET fld_quantity = fld_quantity - $quantity WHERE fld_equipname = '$equipmentName'";
                        $conn->query($updateQuantityQuery);
                    }
                } else {
                    // Check if the booking quantity has been deducted and needs to be re-added
                    if ($quantity == 0) {
                        $status = 'inactive';
                        // Re-add the exact quantity when the booking is inactive
                        $updateQuantityQuery = "UPDATE tbl_equipmentlist SET fld_quantity = fld_quantity + $quantity WHERE fld_equipname = '$equipmentName'";
                        $conn->query($updateQuantityQuery);
                    }
                }

                // If the status is still empty, set it to 'active' or 'inactive' based on the time comparison
                if ($status === '') {
                    $status = ($currentTimestamp >= $startTimestamp && $currentTimestamp <= $endTimestamp) ? 'active' : 'inactive';
                }

                // SQL query to update the status in the tbl_booking table
                $updateStatusQuery = "UPDATE tbl_booking SET status = '$status' WHERE id = $bookingId";
                $conn->query($updateStatusQuery);
            }

            // Commit the transaction if all queries succeed
            $conn->commit();
            echo "Booking statuses and equipment quantities updated successfully!";
        } catch (Exception $e) {
            // Rollback the transaction if any query fails
            $conn->rollback();
            echo "Error: " . $e->getMessage();
        }
    }

    $conn->close();
