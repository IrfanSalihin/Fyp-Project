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

// Check if the 'selected_student' parameter is present in the URL
$selectedStudentId = isset($_GET['selected_student']) ? $_GET['selected_student'] : '';

// If 'selected_student' is -1, no student is selected
if ($selectedStudentId === '-1') {
    echo "<p>No student selected.</p>";
} else {
    // Sanitize the input to prevent SQL injection
    $selectedStudentId = $conn->real_escape_string($selectedStudentId);

    // Retrieve a list of students for the dropdown
    $studentListQuery = "SELECT DISTINCT student_id FROM tbl_booking";
    $studentListResult = $conn->query($studentListQuery);

    // Retrieve student information from the latest booking record
    $studentInfoQuery = "SELECT student_id, first_name, last_name, email, phone_number 
                         FROM tbl_booking 
                         WHERE student_id = '$selectedStudentId' 
                         ORDER BY preferred_date DESC, preferred_start_time DESC
                         LIMIT 1";
    $studentInfoResult = $conn->query($studentInfoQuery);

    // Retrieve active bookings for the selected student
    $activeBookingQuery = "SELECT id, equipment_name, quantity, preferred_date, preferred_start_time, preferred_end_time 
                           FROM tbl_booking 
                           WHERE student_id = '$selectedStudentId' AND status = 'active'
                           ORDER BY preferred_date, preferred_start_time";
    $activeBookingResult = $conn->query($activeBookingQuery);

    // Retrieve future inactive bookings for the selected student
    $futureInactiveBookingQuery = "SELECT id, equipment_name, quantity, preferred_date, preferred_start_time, preferred_end_time 
                                   FROM tbl_booking 
                                   WHERE student_id = '$selectedStudentId' AND status = 'inactive' AND preferred_start_time > NOW()
                                   ORDER BY preferred_date, preferred_start_time";
    $futureInactiveBookingResult = $conn->query($futureInactiveBookingQuery);

    // Retrieve past inactive bookings for the selected student
    $pastInactiveBookingQuery = "SELECT id, equipment_name, quantity, preferred_date, preferred_start_time, preferred_end_time 
                                 FROM tbl_booking 
                                 WHERE student_id = '$selectedStudentId' AND status = 'inactive' AND preferred_end_time <= NOW()
                                 ORDER BY preferred_date DESC, preferred_start_time DESC";
    $pastInactiveBookingResult = $conn->query($pastInactiveBookingQuery);
}

// Handle the delete action when the 'delete_booking' parameter is present in the URL
if (isset($_GET['delete_booking'])) {
    $deleteBookingId = $_GET['delete_booking'];
    // Sanitize the input to prevent SQL injection
    $deleteBookingId = $conn->real_escape_string($deleteBookingId);

    // Perform the delete operation in the database
    $deleteBookingQuery = "DELETE FROM tbl_booking WHERE id = '$deleteBookingId'";
    if ($conn->query($deleteBookingQuery) === TRUE) {
        echo "<script>alert('Booking deleted successfully.');</script>";
        // Redirect to the same page to update the booking list
        header("Location: bookingliststaff.php?selected_student=$selectedStudentId");
        exit;
    } else {
        echo "<script>alert('Error deleting booking: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Booking List</title>
    <!-- Add Bootstrap CSS link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Staff Booking List</h2>

        <!-- Student Selection Section -->
        <div class="mb-4">
            <h4>Select Student</h4>
            <form id="studentForm" method="get">
                <select name="selected_student" class="form-select" onchange="this.form.submit(); removeNoStudentOption();" id="studentSelect">
                    <option value="-1">No Student Selected</option>
                    <?php while ($row = $studentListResult->fetch_assoc()) : ?>
                        <option value="<?php echo $row['student_id']; ?>" <?php if ($selectedStudentId == $row['student_id']) echo 'selected'; ?>><?php echo $row['student_id']; ?></option>
                    <?php endwhile; ?>
                </select>
            </form>
        </div>

        <!-- Student Information Section -->
        <div class="mb-4">
            <h4>Student Information</h4>
            <?php
            if (isset($studentInfoResult) && $studentInfoResult->num_rows > 0) {
                $studentInfo = $studentInfoResult->fetch_assoc();
                echo "<p><strong>Student ID:</strong> " . $studentInfo["student_id"] . "</p>";
                echo "<p><strong>First Name:</strong> " . $studentInfo["first_name"] . "</p>";
                echo "<p><strong>Last Name:</strong> " . $studentInfo["last_name"] . "</p>";
                echo "<p><strong>Email:</strong> " . $studentInfo["email"] . "</p>";
                echo "<p><strong>Phone Number:</strong> " . $studentInfo["phone_number"] . "</p>";
            } elseif ($selectedStudentId !== '-1') {
                echo "<p>Student information not found.</p>";
            }
            ?>
        </div>

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
                            <th>Actions</th>
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
                                <td>
                                    <a href="bookingliststaff.php?selected_student=<?php echo $selectedStudentId; ?>&delete_booking=<?php echo $row["id"]; ?>" class="btn btn-danger btn-delete" onclick="return confirm('Are you sure you want to delete this booking?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No active bookings found for this student.</p>
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
                                <th>Actions</th>
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
                                    <td>
                                        <a href="bookingliststaff.php?selected_student=<?php echo $selectedStudentId; ?>&delete_booking=<?php echo $row["id"]; ?>" class="btn btn-danger btn-delete" onclick="return confirm('Are you sure you want to delete this booking?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <h5>Future Inactive Bookings (Upcoming)</h5>
                    <p>No future inactive bookings found for this student.</p>
                <?php endif; ?>

                <?php if ($pastInactiveBookingResult->num_rows > 0) : ?>
                    <h5>Past Inactive Bookings (Completed)</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Equipment Name</th>
                                <th>Quantity</th>
                                <th>Preferred Date</th>
                                <th>Preferred Start Time</th>
                                <th>Preferred End Time</th>
                                <th>Actions</th>
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
                                    <td>
                                        <a href="bookingliststaff.php?selected_student=<?php echo $selectedStudentId; ?>&delete_booking=<?php echo $row["id"]; ?>" class="btn btn-danger btn-delete" onclick="return confirm('Are you sure you want to delete this booking?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <h5>Past Inactive Bookings (Completed)</h5>
                    <p>No past inactive bookings found for this student.</p>
                <?php endif; ?>
            <?php else : ?>
                <p>No inactive bookings found for this student.</p>
            <?php endif; ?>
        </div>

        <!-- Additional Functionalities -->
        <div class="mb-4">
            <h4>Additional Functionalities</h4>
            <?php if (isset($studentInfo) && $studentInfoResult->num_rows > 0) : ?>
                <div class="d-grid gap-2">
                    <a href="https://mail.google.com/mail/?view=cm&to=<?php echo $studentInfo['email']; ?>&su=Equipment%20Booking" class="btn btn-primary" target="_blank">Contact Student</a>
                    <a href="generate_monthly_report.php?studentId=<?php echo $selectedStudentId; ?>" class="btn btn-success">Generate Monthly Report</a>
                    <!-- Replace 'generate_monthly_report.php' with the actual filename for generating monthly reports -->
                    <!-- Pass the studentId as a parameter in the URL for processing in the 'generate_monthly_report.php' page -->
                </div>
            <?php endif; ?>
        </div>

        <div class="d-grid gap-2 mt-3">
            <a href="mainpagestaff.html" class="btn btn-primary">Go Back</a>
        </div>
    </div>
    <!-- Add Bootstrap JavaScript link -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function removeNoStudentOption() {
            const studentSelect = document.getElementById("studentSelect");
            const noStudentOption = studentSelect.querySelector('option[value="-1"]');
            if (noStudentOption) {
                noStudentOption.remove();
            }
        }

        function disableNoStudentOption() {
            const studentSelect = document.getElementById("studentSelect");
            const noStudentOption = studentSelect.querySelector('option[value="-1"]');
            noStudentOption.disabled = true;
        }

        // Call the functions when the page loads
        window.onload = function() {
            disableNoStudentOption();
        };

        // Call the removeNoStudentOption() function and disableNoStudentOption() when a student is selected
        document.getElementById("studentSelect").addEventListener("change", function() {
            removeNoStudentOption();
            disableNoStudentOption();
        });
    </script>
</body>
</html>
