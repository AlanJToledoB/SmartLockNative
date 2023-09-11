<?php
require_once("TCPDF/Tcpdf.php");
include_once "functions.php"; // Asegúrate de incluir el archivo que contiene la función getEmployeesWithAttendanceCount

$start = isset($_GET["start"]) ? $_GET["start"] : date("Y-m-d");
$end = isset($_GET["end"]) ? $_GET["end"] : date("Y-m-d");

$employees = getEmployeesWithAttendanceCount($start, $end);

$pdf = new TCPDF();
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Attendance Report', 0, 1, 'C');
$pdf->Ln(10);

// Crea la tabla HTML con los datos de los empleados
$html = '
<table border="1">
    <thead>
        <tr>
            <th>Employee</th>
            <th>Presence count</th>
            <th>Absence count</th>
        </tr>
    </thead>
    <tbody>';

foreach ($employees as $employee) {
    $html .= '
        <tr>
            <td>' . $employee->name . '</td>
            <td>' . $employee->presence_count . '</td>
            <td>' . $employee->absence_count . '</td>
        </tr>';
}

$html .= '
    </tbody>
</table>';

// Agrega la tabla al contenido del PDF
$pdf->writeHTML($html, true, false, false, false, '');

$pdfContent = $pdf->Output("", "S");

header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=attendance_report.pdf");
header("Content-Length: " . strlen($pdfContent));
echo $pdfContent;
?>
