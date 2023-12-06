<?php
use \setasign\Fpdi\Fpdi;

require 'app-load.php';
require_once('lib/fpdf181/fpdf.php');
require_once('lib/fpdi/src/autoload.php');

$PERIODE_ID = db_escape(get_input('PERIODE_ID'));
$COMPANY_ID = db_escape(get_input('COMPANY_ID'));
//$PROJECT_ID = db_escape(get_input('PROJECT_ID'));

$periode = db_first("
	SELECT *
	FROM periode
	WHERE PERIODE_ID='$PERIODE_ID'
");

$company = db_first("
	SELECT *
	FROM company
	WHERE COMPANY_ID='$COMPANY_ID'
");

$PERIODE = isset($periode->PERIODE) ? $periode->PERIODE : '';
$BULAN = isset($periode->BULAN) ? $periode->BULAN : '';
$TAHUN = isset($periode->TAHUN) ? $periode->TAHUN : '';

$COMPANY = isset($company->COMPANY) ? $company->COMPANY : '';
$NPWP = isset($company->NPWP) ? $company->NPWP : '';
$NPWP1 = substr($NPWP, 0, -6);
$NPWP2 = substr($NPWP, 9, -3);
$NPWP3 = substr($NPWP, 12);
$ALAMAT = isset($company->ALAMAT) ? $company->ALAMAT : '';
$TELPON = isset($company->TELPON) ? $company->TELPON : '';
$EMAIL = isset($company->EMAIL) ? $company->EMAIL : '';

// initiate FPDI
$pdf = new Fpdi();

// set the source file
$path = 'static/spt_induk.pdf';
$pdf->setSourceFile($path);

$CON = 0.25; 

// import page 1
$tplIdx = $pdf->importPage(1);

// add a page
$pdf->AddPage();
// use the imported page and adjust the page size
$pdf->useTemplate($tplIdx, ['adjustPageSize' => true]);

// now write some text above the imported page
$pdf->SetFont('Arial');
$pdf->SetFontSize('10');
//$pdf->SetTextColor(0, 0, 0);

// Bulan
$pdf->SetXY(30, 52);
$pdf->Write(0, $BULAN);

// Tahun
$pdf->SetXY(43, 52);
$pdf->Write(0, $TAHUN);

// Type SPT
$X = 273 * $CON; $Y = 220 * $CON;
$pdf->SetXY($X, $Y);
$pdf->Write(0, 'X');

// No NPWP
$X = 160 * $CON; $Y = 285 * $CON;
$pdf->SetXY($X, $Y);
$pdf->Write(0, $NPWP1);

$X = 382 * $CON; $Y = 285 * $CON;
$pdf->SetXY($X, $Y);
$pdf->Write(0, $NPWP2);

$X = 453 * $CON; $Y = 285 * $CON;
$pdf->SetXY($X, $Y);
$pdf->Write(0, $NPWP3);

// Nama Company
$X = 160 * $CON; $Y = 312 * $CON;
$pdf->SetXY($X, $Y);
$pdf->Write(0, $COMPANY);

// Alamat Company
$X = 160 * $CON; $Y = 340 * $CON;
$pdf->SetXY($X, $Y);
$pdf->Write(0, $ALAMAT);

// No Telepon Company
$X = 160 * $CON; $Y = 392 * $CON;
$pdf->SetXY($X, $Y);
$pdf->Write(0, $TELPON);

// Email Company
$X = 576 * $CON; $Y = 392 * $CON;
$pdf->SetXY($X, $Y);
$pdf->Write(0, $EMAIL);

/*$tplIdx = $pdf->importPage(2);

// add a page
$pdf->AddPage();
// use the imported page and adjust the page size
$pdf->useTemplate($tplIdx, ['adjustPageSize' => true]);

// use the imported page and place it at point 10,10 with a width of 100 mm
//$pdf->useImportedPage($tplIdx, 10, 10, 100);

// now write some text above the imported page
$pdf->SetFont('Helvetica');
$pdf->SetTextColor(0, 0, 0);

$pdf->SetXY(30, 30);
$pdf->Write(8, 'This is just a simple text');*/

$pdf->Output();
?>