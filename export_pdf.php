<?php
require_once(__DIR__ . '/tcpdf/tcpdf.php');
include __DIR__ . '/db.php';

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 10, 'User List', 0, 1, 'C');

$html = '<table border="1" cellpadding="4">
<tr style="background-color:#f2f2f2;">
<th>ID</th><th>Name</th><th>Email</th><th>Mobile</th><th>Nationality</th><th>Created</th>
</tr>';

$stmt = $db->query("SELECT * FROM users ORDER BY id DESC");
foreach ($stmt as $row) {
    $mobile = isset($row['mobile']) ? htmlspecialchars($row['mobile']) : '';
    $nationality = isset($row['nationality']) ? htmlspecialchars($row['nationality']) : '';

    $html .= '<tr>
        <td>' . $row['id'] . '</td>
        <td>' . htmlspecialchars($row['name']) . '</td>
        <td>' . htmlspecialchars($row['email']) . '</td>
        <td>' . $mobile . '</td>
        <td>' . $nationality . '</td>
        <td>' . $row['created_at'] . '</td>
    </tr>';
}

$html .= '</table>';
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('user_list.pdf', 'D');
