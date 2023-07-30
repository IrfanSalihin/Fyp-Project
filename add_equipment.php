<?php
$host = 'localhost';
$db   = 'dbsystem';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

function generateEquipmentID($equipClass, $pdo)
{
    $prefix = strtoupper(substr($equipClass, 0, 2));

    $stmt = $pdo->prepare('SELECT MAX(fld_equipid) AS max_id FROM tbl_equipmentlist WHERE fld_equipid LIKE ?');
    $stmt->execute([$prefix . '%']);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $maxID = $result['max_id'];

    if ($maxID) {
        $numericPart = (int) substr($maxID, 2);
        $newNumericPart = $numericPart + 1;
        $newID = $prefix . sprintf("%03d", $newNumericPart);
    } else {
        $newID = $prefix . '001';
    }

    return $newID;
}

$equipName = $_POST['equipName'];
$equipClass = $_POST['equipClass'];
$quantity = $_POST['quantity'];
$imageData = file_get_contents($_FILES['equipImage']['tmp_name']);
$description = $_POST['description'];

$equipmentID = generateEquipmentID($equipClass, $pdo);

$stmt = $pdo->prepare('INSERT INTO tbl_equipmentlist (fld_equipid, fld_equipname, fld_equipclass, fld_quantity, fld_equipimage, fld_equipdescription) VALUES (?, ?, ?, ?, ?, ?)');
$stmt->execute([$equipmentID, $equipName, $equipClass, $quantity, $imageData, $description]);

$newEquipmentStmt = $pdo->prepare('SELECT * FROM tbl_equipmentlist WHERE fld_equipid = ?');
$newEquipmentStmt->execute([$equipmentID]);
$newEquipment = $newEquipmentStmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($newEquipment);
