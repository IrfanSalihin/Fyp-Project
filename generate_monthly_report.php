<?php
require_once __DIR__ . '/vendor/autoload.php'; // Include the Composer autoloader

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Assuming you have already established a connection to your database
$dbHost = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "dbsystem";

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$studentId = $_GET["studentId"];

// Retrieve past inactive bookings for the student
$pastInactiveBookingQuery = "SELECT id AS booking_id, equipment_name, quantity, preferred_date, 
                                    student_id, first_name, last_name, email, phone_number
                             FROM tbl_booking
                             WHERE student_id = '$studentId' AND status = 'inactive' AND preferred_end_time <= NOW()
                             ORDER BY preferred_date DESC, preferred_start_time DESC";
$pastInactiveBookingResult = $conn->query($pastInactiveBookingQuery);

if ($pastInactiveBookingResult === false) {
    die("Error fetching past inactive bookings: " . $conn->error);
}

if ($pastInactiveBookingResult->num_rows > 0) {
    // Create a new Spreadsheet object
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set the column headers
    $sheet->setCellValue('A1', 'Booking ID');
    $sheet->setCellValue('B1', 'Equipment Name');
    $sheet->setCellValue('C1', 'Quantity');
    $sheet->setCellValue('D1', 'Preferred Date');
    $sheet->setCellValue('E1', 'Student ID');
    $sheet->setCellValue('F1', 'First Name');
    $sheet->setCellValue('G1', 'Last Name');
    $sheet->setCellValue('H1', 'Email');
    $sheet->setCellValue('I1', 'Phone Number');

    // Fetch data from the database and populate the spreadsheet
    $row = 2;
    while ($booking = $pastInactiveBookingResult->fetch_assoc()) {
        $sheet->setCellValue('A' . $row, $booking["booking_id"]);
        $sheet->setCellValue('B' . $row, $booking["equipment_name"]);
        $sheet->setCellValue('C' . $row, $booking["quantity"]);
        $sheet->setCellValue('D' . $row, $booking["preferred_date"]);
        $sheet->setCellValue('E' . $row, $booking["student_id"]);
        $sheet->setCellValue('F' . $row, $booking["first_name"]);
        $sheet->setCellValue('G' . $row, $booking["last_name"]);
        $sheet->setCellValue('H' . $row, $booking["email"]);
        $sheet->setCellValueExplicit('I' . $row, "\t" . $booking["phone_number"], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $row++;
    }

    // Group bookings by month
    $groupedData = [];
    foreach ($sheet->getRowIterator(2) as $row) {
        $cellValue = $sheet->getCell('D' . $row->getRowIndex())->getValue();
        $month = date('F', strtotime($cellValue));
        if (!isset($groupedData[$month])) {
            $groupedData[$month] = [];
        }
        $groupedData[$month][] = $row;
    }

    // Set the filename for the downloaded Excel file
    $filename = 'monthly_report_' . date('Y-m-d_H-i-s') . '.xlsx';

    // Create a new Excel Writer object
    $writer = new Xlsx($spreadsheet);

    // Adjust the column width for each month
    $columnIndex = 0;
    foreach ($groupedData as $month => $rows) {
        $sheet->setCellValueByColumnAndRow($columnIndex, 1, $month);
        foreach ($rows as $index => $row) {
            $cellValue = $sheet->getCellByColumnAndRow($columnIndex, $index + 2)->getValue();
            $sheet->setCellValueByColumnAndRow($columnIndex, $index + 2, $cellValue);
        }
        $sheet->getColumnDimensionByColumn($columnIndex)->setAutoSize(true);
        $columnIndex++;
    }

    // Send headers to the browser to initiate the download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    // Output the spreadsheet to the browser
    $writer->save('php://output');
} else {
    echo "No past inactive bookings found for this student.";
}
