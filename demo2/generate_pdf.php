<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include FPDF library and database connection
require(__DIR__.'/../fpdf/fpdf.php'); // Correct relative path
include '/../db_connect.php' ; // Correct relative path to db_connect.php

// Create instance of FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Set table header
$pdf->Cell(30, 10, 'Roll Number', 1);
$pdf->Cell(60, 10, 'Dealing Hand Comment', 1);
$pdf->Cell(60, 10, 'Section Incharge Comment', 1);
$pdf->Cell(60, 10, 'Deputy Registrar Comment', 1);
$pdf->Cell(30, 10, 'Dean Approval', 1);
$pdf->Ln();

// Fetch approved students
$sql = "SELECT * FROM students WHERE dean_approval = 'Approved'";
$result = $conn->query($sql);

if ($result === false) {
    die("Error in query: " . $conn->error);
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(30, 10, htmlspecialchars($row['roll_number']), 1);
        $pdf->Cell(60, 10, htmlspecialchars($row['level1_comment']), 1);
        $pdf->Cell(60, 10, htmlspecialchars($row['level2_comment']), 1);
        $pdf->Cell(60, 10, htmlspecialchars($row['level3_comment']), 1);
        $pdf->Cell(30, 10, htmlspecialchars($row['dean_approval']), 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(0, 10, 'No approved students found.', 0, 1);
}

// Close database connection
$conn->close();

// Output the PDF
$pdf->Output();
?>
