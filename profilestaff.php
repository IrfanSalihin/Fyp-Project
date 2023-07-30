<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["staffId"])) {
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

$staffId = $_SESSION["staffId"];

// Retrieve the user profile data from the database
$sql = "SELECT * FROM tbl_staff WHERE fld_staffid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $staffId);
$stmt->execute();
$result = $stmt->get_result();

function phoneNumberWithLeadingZeros($phoneNumber)
{
    return sprintf("%010d", $phoneNumber);
}

function getMIMETypeFromBase64Data($base64Data)
{
    $imageData = base64_decode($base64Data);
    $imageInfo = getimagesizefromstring($imageData);
    if ($imageInfo === false) {
        return null; // Failed to get image info, return null or handle the error
    }

    return $imageInfo['mime'];
}

function processProfilePicture($base64Data)
{
    // Decode the base64-encoded image data
    $imageData = base64_decode($base64Data);

    // Generate a unique filename for the profile picture
    $filename = uniqid() . ".jpg";

    // Save the image data to a file in the "profile_pictures" directory
    file_put_contents("profile_pictures/" . $filename, $imageData);

    // Return the filename of the saved image, which will be used as the image source
    return "profile_pictures/" . $filename;
}

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $firstName = $row["fld_firstname"];
    $lastName = $row["fld_lastname"];
    $phoneNumber = phoneNumberWithLeadingZeros($row["fld_phonenumber"]);
    $email = $row["fld_email"];
    $password = $row["fld_password"];

    // Check if the profile picture is available
    $profilePicture = $row["fld_image"];
    if (empty($profilePicture)) {
        // If no image is available, set a default profile picture path
        $profilePicture = "default_profile_picture.jpg";
    } else {
        // If an image is available, retrieve it using the processProfilePicture function
        $profilePicture = processProfilePicture($profilePicture);
    }
} else {
    // Handle the case when the user profile data is not found
    // Redirect to an error page or display an error message
    echo "<p>No user profile found.</p>";
    exit();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Profile Page</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: #f5f5f5;
            font-family: 'Montserrat', sans-serif;
        }

        .header {
            width: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .header-title {
            font-size: 32px;
            font-weight: 500;
            color: #333;
        }

        .profile-container {
            max-width: 800px;
            width: 100%;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            margin-top: 20px;
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-size: cover;
            background-position: center;
            margin: 0 auto 20px;
        }

        .profile-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-info p {
            margin: 5px 0;
            font-size: 18px;
            color: #555;
        }

        .password-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .password-label {
            margin-right: 10px;
            font-size: 14px;
            color: #777;
        }

        .edit-profile-button {
            background-color: #4caf50;
            color: #fff;
            padding: 10px 20px;
            border-radius: 3px;
            border: none;
            cursor: pointer;
            display: block;
            width: 100%;
            text-align: center;
            font-size: 16px;
            margin-top: 20px;
        }

        .edit-profile-button:hover {
            background-color: #45a049;
        }

        .password-toggle-button {
            background-color: transparent;
            border: none;
            color: #757575;
            cursor: pointer;
            padding: 0;
            font-size: 14px;
            transition: color 0.3s;
        }

        .password-toggle-button:hover {
            color: #424242;
        }

        .edit-profile-container input[type="text"],
        .edit-profile-container input[type="password"],
        .edit-profile-container input[type="email"] {
            font-size: 16px;
            padding: 5px;
            border: none;
            border-bottom: 1px solid #9e9e9e;
            outline: none;
            width: 200px;
            text-align: center;
            margin-right: 10px;
        }

        .back-button {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #4caf50;
            color: #fff;
            cursor: pointer;
        }

        .back-button:hover {
            background-color: #45a049;
        }

        .back-icon {
            font-size: 24px;
        }

        /* Custom modal styles */
        #edit-profile-modal {
            max-width: 400px;
        }

        #edit-profile-modal .modal-content {
            padding: 24px;
        }

        #edit-profile-modal .modal-footer {
            padding: 8px 24px;
        }

        #edit-profile-modal .input-field {
            margin-top: 16px;
        }

        #edit-profile-modal input[type="text"],
        #edit-profile-modal input[type="password"],
        #edit-profile-modal input[type="email"] {
            font-size: 16px;
            padding: 5px;
            border: none;
            border-bottom: 1px solid #9e9e9e;
            outline: none;
            width: 100%;
            margin: 0;
        }

        #edit-profile-modal .file-upload-label {
            font-size: 14px;
            font-weight: 500;
            color: #757575;
            display: inline-block;
            cursor: pointer;
        }

        #edit-profile-modal .file-upload-label:hover {
            text-decoration: underline;
        }

        #edit-profile-modal .file-upload {
            display: none;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1 class="header-title">Profile Page</h1>
    </div>
    <div class="profile-container">
        <div class="profile-info">
            <?php if (!empty($profilePicture)) : ?>
                <!-- Display the profile picture using <img> tag with the correct MIME type -->
                <img src="<?php echo $profilePicture; ?>" alt="Profile Picture" class="profile-picture">
            <?php else : ?>
                <!-- Display a default profile picture or a placeholder if no image is available -->
                <img src="default_profile_picture.jpg" alt="Default Profile Picture" class="profile-picture">
            <?php endif; ?>
            <div class="edit-profile-container">
                <p>Staff ID: <input type="text" id="editStaffId" value="<?php echo $staffId; ?>" disabled></p>
                <p>First Name: <input type="text" id="editFirstName" value="<?php echo $firstName; ?>"></p>
                <p>Last Name: <input type="text" id="editLastName" value="<?php echo $lastName; ?>"></p>
                <p>Phone Number: <input type="text" id="editPhoneNumber" value="<?php echo $phoneNumber; ?>"></p>
                <p>Email: <input type="email" id="editEmail" value="<?php echo $email; ?>"></p>
                <div class="password-container">
                    <label for="editPassword" class="password-label">Password:</label>
                    <input type="password" id="editPassword" value="<?php echo $password; ?>">
                    <button class="password-toggle-button" onclick="togglePasswordVisibility()">Show</button>
                </div>
            </div>
        </div>
        <button class="edit-profile-button modal-trigger" data-target="edit-profile-modal" type="button">Edit Profile</button>
    </div>

    <!-- Back button -->
    <a href="mainpagestaff.html" class="btn-floating btn-large waves-effect waves-light blue darken-2 back-button">
        <i class="material-icons">arrow_back</i>
    </a>

    <!-- Edit Profile Modal -->
    <div id="edit-profile-modal" class="modal">
        <div class="modal-content">
            <h4>Edit Profile</h4>
            <div class="edit-profile-container">
                <form id="profile-form">
                    <div class="input-field">
                        <input type="text" id="modalEditFirstName" name="firstName" value="<?php echo $firstName; ?>">
                        <label for="modalEditFirstName">First Name</label>
                    </div>

                    <div class="input-field">
                        <input type="text" id="modalEditLastName" name="lastName" value="<?php echo $lastName; ?>">
                        <label for="modalEditLastName">Last Name</label>
                    </div>

                    <div class="input-field">
                        <input type="text" id="modalEditPhoneNumber" name="phoneNumber" value="<?php echo $phoneNumber; ?>">
                        <label for="modalEditPhoneNumber">Phone Number</label>
                    </div>

                    <div class="input-field">
                        <input type="email" id="modalEditEmail" name="email" value="<?php echo $email; ?>">
                        <label for="modalEditEmail">Email</label>
                    </div>

                    <div class="input-field">
                        <input type="password" id="modalEditPassword" name="password" value="<?php echo $password; ?>">
                        <label for="modalEditPassword">Password</label>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Cancel</a>
            <button class="waves-effect waves-green btn-flat" onclick="saveProfileChanges()">Save</button>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var elems = document.querySelectorAll('.modal');
            var instances = M.Modal.init(elems);
        });

        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("editPassword");
            var toggleButton = document.querySelector(".password-toggle-button");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                toggleButton.textContent = "Hide";
            } else {
                passwordInput.type = "password";
                toggleButton.textContent = "Show";
            }
        }

        function saveProfileChanges() {
            var form = document.getElementById("profile-form");
            var formData = new FormData(form);

            // Send the form data to the server
            sendFormData(formData);
        }

        function sendFormData(formData) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "dbupdatestaff.php", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        // Handle success response
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            M.toast({
                                html: "Profile updated successfully!"
                            });
                            location.reload();
                        } else {
                            M.toast({
                                html: "Error updating profile."
                            });
                        }
                    } else {
                        // Handle error response
                        M.toast({
                            html: "Error updating profile."
                        });
                    }
                }
            };
            xhr.send(formData);
        }
    </script>
</body>

</html>