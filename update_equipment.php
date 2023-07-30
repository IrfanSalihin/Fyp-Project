<?php
$host = 'localhost';
$username = 'root';
$password = '';
$db_name = 'dbsystem';

// Establish the database connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check if the connection was successful
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON data sent by the AJAX request
    $requestData = json_decode(file_get_contents('php://input'), true);

    // Extract data from the request
    $equipId = $requestData['equipId'];
    $equipName = $requestData['equipName'];
    $equipClass = $requestData['equipClass'];
    $quantity = $requestData['quantity'];
    $description = $requestData['description'];

    // Prepare the SQL query to update the equipment data
    $sql = "UPDATE tbl_equipmentlist SET 
            fld_equipname = '$equipName', 
            fld_equipclass = '$equipClass', 
            fld_quantity = '$quantity', 
            fld_equipdescription = '$description'
            WHERE fld_equipid = '$equipId'";

    // Execute the SQL query
    if ($conn->query($sql) === TRUE) {
        // Query successful, fetch the updated equipment data
        $selectSql = "SELECT * FROM tbl_equipmentlist WHERE fld_equipid = '$equipId'";
        $result = $conn->query($selectSql);
        $updatedEquipment = $result->fetch_assoc();

        // Return the updated equipment data as a JSON response
        header('Content-Type: application/json');
        echo json_encode($updatedEquipment); // Ensure the data is properly encoded as JSON
    } else {
        // If the query fails, return an error response
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Failed to update equipment']); // Ensure the error response is also in JSON format
    }
} else {
    // If the request method is not POST, return an error response
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid request method']); // Ensure the error response is in JSON format
}

// Close the database connection
$conn->close();
?>
