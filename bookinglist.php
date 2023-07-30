<?php
session_start();

// Assuming you have already established a connection to your database
$dbHost = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "dbsystem";

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$studentId = $_SESSION["studentId"];

// Retrieve active bookings for the student
$activeBookingQuery = "SELECT id, equipment_name, quantity, preferred_date, preferred_start_time, preferred_end_time 
                       FROM tbl_booking 
                       WHERE student_id = '$studentId' AND status = 'active'
                       ORDER BY preferred_date, preferred_start_time";
$activeBookingResult = $conn->query($activeBookingQuery);

// Retrieve future inactive bookings for the student
$futureInactiveBookingQuery = "SELECT id, equipment_name, quantity, preferred_date, preferred_start_time, preferred_end_time 
                               FROM tbl_booking 
                               WHERE student_id = '$studentId' AND status = 'inactive' AND preferred_start_time > NOW()
                               ORDER BY preferred_date, preferred_start_time";
$futureInactiveBookingResult = $conn->query($futureInactiveBookingQuery);

// Retrieve past inactive bookings for the student
$pastInactiveBookingQuery = "SELECT id, equipment_name, quantity, preferred_date, preferred_start_time, preferred_end_time 
                             FROM tbl_booking 
                             WHERE student_id = '$studentId' AND status = 'inactive' AND preferred_end_time <= NOW()
                             ORDER BY preferred_date DESC, preferred_start_time DESC";
$pastInactiveBookingResult = $conn->query($pastInactiveBookingQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking List</title>
    <!-- Add Bootstrap CSS link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Your Booking List</h2>

        <!-- Active Bookings Section -->
        <div class="mb-4">
            <h4>Active Bookings</h4>
            <?php if ($activeBookingResult->num_rows > 0) : ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Equipment Name</th>
                            <th>Quantity</th>
                            <th>Preferred Date</th>
                            <th>Preferred Start Time</th>
                            <th>Preferred End Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $activeBookingResult->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $row["id"]; ?></td>
                                <td><?php echo $row["equipment_name"]; ?></td>
                                <td><?php echo $row["quantity"]; ?></td>
                                <td><?php echo $row["preferred_date"]; ?></td>
                                <td><?php echo $row["preferred_start_time"]; ?></td>
                                <td><?php echo $row["preferred_end_time"]; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>You don't have any active bookings.</p>
            <?php endif; ?>
        </div>

        <!-- Inactive Bookings Section -->
        <div class="mb-4">
            <h4>Inactive Bookings</h4>
            <?php if ($futureInactiveBookingResult->num_rows > 0 || $pastInactiveBookingResult->num_rows > 0) : ?>
                <?php if ($futureInactiveBookingResult->num_rows > 0) : ?>
                    <h5>Future Inactive Bookings (Upcoming)</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Equipment Name</th>
                                <th>Quantity</th>
                                <th>Preferred Date</th>
                                <th>Preferred Start Time</th>
                                <th>Preferred End Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $futureInactiveBookingResult->fetch_assoc()) : ?>
                                <tr>
                                    <td><?php echo $row["id"]; ?></td>
                                    <td><?php echo $row["equipment_name"]; ?></td>
                                    <td><?php echo $row["quantity"]; ?></td>
                                    <td><?php echo $row["preferred_date"]; ?></td>
                                    <td><?php echo $row["preferred_start_time"]; ?></td>
                                    <td><?php echo $row["preferred_end_time"]; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <h5>Future Inactive Bookings (Upcoming)</h5>
                    <p>You don't have any future booking.</p>
                <?php endif; ?>

                <h5>Past Inactive Bookings (Completed)</h5>
                <?php if ($pastInactiveBookingResult->num_rows > 0) : ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Equipment Name</th>
                                <th>Quantity</th>
                                <th>Preferred Date</th>
                                <th>Preferred Start Time</th>
                                <th>Preferred End Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $pastInactiveBookingResult->fetch_assoc()) : ?>
                                <tr>
                                    <td><?php echo $row["id"]; ?></td>
                                    <td><?php echo $row["equipment_name"]; ?></td>
                                    <td><?php echo $row["quantity"]; ?></td>
                                    <td><?php echo $row["preferred_date"]; ?></td>
                                    <td><?php echo $row["preferred_start_time"]; ?></td>
                                    <td><?php echo $row["preferred_end_time"]; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p>No past inactive bookings found.</p>
                <?php endif; ?>
            <?php else : ?>
                <p>You don't have any inactive past bookings.</p>
            <?php endif; ?>
        </div>

        <div class="d-grid gap-2 mt-3">
            <a href="mainpagestudent.html" class="btn btn-primary">Go Back</a>
        </div>
    </div>
    <!-- Add Bootstrap JavaScript link -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>