<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Add Bootstrap CSS link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <!-- Add Materialize CSS link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <title>Booking Form</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 6px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .booking-image {
            width: 100%;
            height: 300px;
            object-fit: contain;
            background-position: center;
            background-repeat: no-repeat;
            background-size: contain;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .form-label {
            font-size: 16px;
            color: #9e9e9e;
        }

        .back-button {
            position: fixed;
            top: 30px;
            right: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #4caf50;
            color: #fff;
            cursor: pointer;
            z-index: 10;
        }

        .back-button:hover {
            background-color: #45a049;
        }

        .back-icon {
            font-size: 24px;
        }

        .smaller-title {
            font-size: 40px;
            /* Adjust the font size as needed */
        }
    </style>
</head>

<body>
    <a href="mainpagestudent.html" class="btn-floating btn-large waves-effect waves-light blue darken-2 back-button">
        <i class="material-icons back-icon">arrow_back</i>
    </a>

    <div class="container">
        <h2 class="text-center mb-4 smaller-title">Booking Form</h2>

        <div class="booking-image"></div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Equipment Information</h5>
                <div class="mb-3">
                    <label for="equipmentName" class="form-label">Equipment Name</label>
                    <input type="text" id="equipmentName" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" id="quantity" class="form-control" min="1" value="1">
                </div>
                <div class="mb-3">
                    <label for="preferredDate" class="form-label">Preferred Date</label>
                    <input type="text" id="preferredDate" class="datepicker" readonly>
                </div>
                <div class="mb-3">
                    <label for="preferredStartTime" class="form-label">Preferred Start Time</label>
                    <input type="text" id="preferredStartTime" class="timepicker" readonly>
                </div>
                <div class="mb-3">
                    <label for="preferredEndTime" class="form-label">Preferred End Time</label>
                    <input type="text" id="preferredEndTime" class="timepicker" readonly>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Student Information</h5>
                <div class="mb-3">
                    <label for="studentId" class="form-label">Student ID</label>
                    <input type="text" id="studentId" class="form-control" value="<?php echo $_SESSION["studentId"]; ?>" disabled>
                </div>
                <div class="mb-3">
                    <label for="firstName" class="form-label">First Name</label>
                    <input type="text" id="firstName" class="form-control" value="<?php echo $_SESSION["firstName"]; ?>" disabled>
                </div>
                <div class="mb-3">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input type="text" id="lastName" class="form-control" value="<?php echo $_SESSION["lastName"]; ?>" disabled>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" class="form-control" value="<?php echo $_SESSION["email"]; ?>" disabled>
                </div>
                <div class="mb-3">
                    <label for="phoneNumber" class="form-label">Phone Number</label>
                    <input type="text" id="phoneNumber" class="form-control" value="<?php echo $_SESSION["phoneNumber"]; ?>" disabled>
                </div>
            </div>
        </div>

        <div class="d-grid gap-2">
            <button class="btn btn-primary btn-lg" onclick="submitBooking()">Submit</button>
        </div>
    </div>

    <!-- Add Bootstrap JavaScript link -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Add jQuery (optional, required for Materialize CSS) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Add Materialize JavaScript link -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        let selectedEquipmentData; // Global variable to store selected equipment data

        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const equipId = urlParams.get('equipId');
            if (equipId) {
                fetchEquipmentData(equipId);
            }
        });

        function fetchEquipmentData(equipId) {
            fetch(`fetch_equipment.php?equipId=${equipId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    selectedEquipmentData = data[0];

                    const equipmentNameField = document.getElementById('equipmentName');
                    const datePicker = document.getElementById('preferredDate');
                    const startTimePicker = document.getElementById('preferredStartTime');
                    const endTimePicker = document.getElementById('preferredEndTime');

                    // Set Equipment Name
                    equipmentNameField.value = selectedEquipmentData.fld_equipname;

                    // Display Equipment Image
                    const imageContainer = document.querySelector('.booking-image');
                    imageContainer.style.backgroundImage = `url('data:image/jpeg;base64,${selectedEquipmentData.fld_equipimage}')`;

                    // Initialize Materialize date and time pickers
                    M.Datepicker.init(datePicker, {
                        format: 'yyyy-mm-dd',
                        autoClose: true,
                        container: 'body',
                    });

                    M.Timepicker.init(startTimePicker, {
                        autoClose: true,
                        twelveHour: false,
                        container: 'body',
                    });

                    M.Timepicker.init(endTimePicker, {
                        autoClose: true,
                        twelveHour: false,
                        container: 'body',
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function updateQuantityOnStartTimeReached() {
            // Deduct the equipment quantity when the preferred start time is reached
            const quantity = document.getElementById('quantity').value;
            const equipmentName = selectedEquipmentData.fld_equipname;

            $.ajax({
                type: 'POST',
                url: 'update_quantity.php',
                data: {
                    action: 'deduct',
                    equipmentName: equipmentName,
                    quantity: quantity
                },
                success: function(response) {
                    console.log('Quantity deducted successfully!');
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }

        function updateQuantityOnEndTimeReached() {
            // Re-add the equipment quantity when the preferred end time is reached
            const quantity = document.getElementById('quantity').value;
            const equipmentName = selectedEquipmentData.fld_equipname;

            $.ajax({
                type: 'POST',
                url: 'update_quantity.php',
                data: {
                    action: 'add',
                    equipmentName: equipmentName,
                    quantity: quantity
                },
                success: function(response) {
                    console.log('Quantity added back successfully!');
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }

        function submitBooking() {
            const equipmentName = selectedEquipmentData.fld_equipname;
            const quantity = document.getElementById('quantity').value;
            const preferredDate = document.getElementById('preferredDate').value;
            const preferredStartTimePicker = M.Timepicker.getInstance(document.getElementById('preferredStartTime'));
            const preferredEndTimePicker = M.Timepicker.getInstance(document.getElementById('preferredEndTime'));

            const preferredStartTime = preferredStartTimePicker.time;
            const preferredEndTime = preferredEndTimePicker.time;

            // Student Information
            const studentId = "<?php echo $_SESSION['studentId']; ?>";
            const firstName = "<?php echo $_SESSION['firstName']; ?>";
            const lastName = "<?php echo $_SESSION['lastName']; ?>";
            const email = "<?php echo $_SESSION['email']; ?>";
            const phoneNumber = "<?php echo $_SESSION['phoneNumber']; ?>";

            // Send the form data to the server using AJAX
            $.ajax({
                type: 'POST',
                url: 'save_booking.php',
                data: {
                    equipmentName: equipmentName,
                    quantity: quantity,
                    preferredDate: preferredDate,
                    preferredStartTime: preferredStartTime,
                    preferredEndTime: preferredEndTime,
                    studentId: studentId,
                    firstName: firstName,
                    lastName: lastName,
                    email: email,
                    phoneNumber: phoneNumber
                },
                success: function(response) {
                    // Handle success (if needed)
                    alert('Booking saved successfully!');
                },
                error: function(xhr, status, error) {
                    // Handle error (if needed)
                    console.error(xhr.responseText);
                }
            });

            // Calculate the time difference in milliseconds between now and the preferred start time
            const startTimeDate = new Date();
            startTimeDate.setHours(preferredStartTime.slice(0, 2));
            startTimeDate.setMinutes(preferredStartTime.slice(3, 5));
            startTimeDate.setSeconds(0);
            const timeUntilStartTime = startTimeDate.getTime() - Date.now();

            // Calculate the time difference in milliseconds between now and the preferred end time
            const endTimeDate = new Date();
            endTimeDate.setHours(preferredEndTime.slice(0, 2));
            endTimeDate.setMinutes(preferredEndTime.slice(3, 5));
            endTimeDate.setSeconds(0);
            const timeUntilEndTime = endTimeDate.getTime() - Date.now();

            // Set up the timeout to update the quantity when the preferred start time is reached
            if (timeUntilStartTime > 0) {
                setTimeout(updateQuantityOnStartTimeReached, timeUntilStartTime);
            }

            // Set up the timeout to revert the quantity when the preferred end time is reached
            if (timeUntilEndTime > 0) {
                setTimeout(updateQuantityOnEndTimeReached, timeUntilEndTime);
            }
        }
    </script>
</body>

</html>