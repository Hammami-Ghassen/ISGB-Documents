<?php
require __DIR__ . '/vendor/autoload.php';  // TCPDF autoloader
include 'database.php';  // Your DB connection ($pdo)

// 1) Get the demande ID from URL
if (empty($_GET['demande_id'])) {
    http_response_code(400);
    exit('Paramètre demande_id manquant');
}
$demandeId = (int) $_GET['demande_id'];

// 2) Fetch demande + utilisateur details
$sql = "SELECT d.type_document, d.date_soumission, d.identifiant_suivi,
               u.nom, u.prenom
        FROM demande d
        JOIN utilisateur u ON d.id_utilisateur = u.id_utilisateur
        WHERE d.id_demande = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $demandeId]);
$demande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$demande) {
    http_response_code(404);
    exit('Demande non trouvée');
}

// 3) Generate QR code content as a verification link
$qrData = "Demande #{$demandeId}\n"
        . "Type: {$demande['type_document']}\n"
        . "Date: " . (new DateTime($demande['date_soumission']))->format('d/m/Y') . "\n"
        . "Suivi: {$demande['identifiant_suivi']}";

// 4) Generate PDF
$pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();

// Header
$pdf->SetFont('dejavusans', 'B', 16);
$pdf->Cell(0, 10, "Attestation — Demande #{$demandeId}", 0, 1, 'C');

$pdf->Ln(4);
$pdf->SetFont('dejavusans', '', 12);
$pdf->Cell(50, 6, 'Nom étudiant :', 0, 0);
$pdf->Cell(0, 6, "{$demande['prenom']} {$demande['nom']}", 0, 1);
$pdf->Cell(50, 6, 'Type de document :', 0, 0);
$pdf->Cell(0, 6, $demande['type_document'], 0, 1);
$pdf->Cell(50, 6, 'Date de soumission :', 0, 0);
$pdf->Cell(0, 6, (new DateTime($demande['date_soumission']))->format('d/m/Y'), 0, 1);
$pdf->Cell(50, 6, 'Réf. suivi :', 0, 0);
$pdf->Cell(0, 6, $demande['identifiant_suivi'], 0, 1);

$pdf->Ln(6);
$pdf->MultiCell(0, 5, "Nous confirmons que votre demande a bien été reçue et traitée. Vous pouvez conserver ce document comme preuve officielle.", 0, 'L');

// Label above QR code
$pdf->Ln(10);
$pdf->SetFont('dejavusans', '', 10);
$pdf->Cell(0, 6, "Scannez ce code pour vérifier la validité du document :", 0, 1, 'C');

// Position QR Code
$size = 30; // mm
$yPos = $pdf->GetY();
$pageWidth = $pdf->getPageWidth() - $pdf->getMargins()['left'] - $pdf->getMargins()['right'];
$xPos = $pdf->GetX() + ($pageWidth - $size) / 2;

$style = [
    'border' => 0,
    'vpadding' => 'auto',
    'hpadding' => 'auto',
    'fgcolor' => [0, 0, 0],
    'bgcolor' => false,
    'module_width' => 1,
    'module_height' => 1,
];

$pdf->write2DBarcode($qrData, 'QRCODE,H', $xPos, $yPos, $size, $size, $style, 'N');

// Output PDF
$pdf->Output("demande-{$demandeId}.pdf", 'D');  // D = force download
