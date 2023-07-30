<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbsystem";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['equipId'])) {
    $equipId = $_GET['equipId'];
    $stmt = $conn->prepare('SELECT * FROM tbl_equipmentlist WHERE fld_equipid = ?');
    $stmt->bind_param('s', $equipId);
} else {
    $stmt = $conn->prepare('SELECT * FROM tbl_equipmentlist');
}

$stmt->execute();
$result = $stmt->get_result();

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$stmt->close();
$conn->close();

if (!$data) {
    die('Failed to fetch equipment data.');
}

$equipmentData = array_map(function ($row) {
    return array(
        'fld_equipid' => $row['fld_equipid'],
        'fld_equipname' => $row['fld_equipname'],
        'fld_equipclass' => $row['fld_equipclass'],
        'fld_quantity' => $row['fld_quantity'],
        'fld_equipimage' => base64_encode($row['fld_equipimage']),
        'fld_equipdescription' => $row['fld_equipdescription']
    );
}, $data);

echo json_encode($equipmentData);
?>
