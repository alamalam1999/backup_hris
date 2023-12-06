<?php

require 'app-load.php';
require 'lib/tbs/tbs_class.php';
require 'lib/tbs/plugins/tbs_plugin_opentbs.php';

$PERIODE_ID = db_escape(get_input('PERIODE_ID'));
$PROJECT_ID = db_escape(get_input('PROJECT_ID'));

$periode = db_first("
	SELECT *
	FROM periode
	WHERE PERIODE_ID='$PERIODE_ID'
");

$PERIODE = isset($periode->PERIODE) ? $periode->PERIODE : '';
$BULAN = isset($periode->BULAN) ? $periode->BULAN : '';
$TAHUN = isset($periode->TAHUN) ? $periode->TAHUN : '';

$project = db_first("
	SELECT *
	FROM project
	WHERE PROJECT_ID='$PROJECT_ID'
");

$PROJECT = isset($project->PROJECT) ? $project->PROJECT : '';

$rs = db_fetch("
	SELECT *
	FROM penggajian P
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=P.KARYAWAN_ID)
	WHERE P.PERIODE_ID='$PERIODE_ID' AND P.PROJECT_ID='$PROJECT_ID'
	ORDER BY K.NAMA ASC
");			 									

$TBS = new clsTinyButStrong;
$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); /* load OpenTBS plugin */

// Read user choices
$debug = (isset($_GET['debug'])) ? intval($_GET['debug']) : 0;

// Retrieve the template to open
$template = 'static/export-dbank.xlsx';
if (!file_exists($template)) exit("Template tidak ditemukan.");

// Load the template
$TBS->LoadTemplate($template);

if ($debug==2){
	$TBS->Plugin(OPENTBS_DEBUG_XML_CURRENT);
	exit;
} elseif ($debug==1){
	$TBS->Plugin(OPENTBS_DEBUG_INFO);
	exit;
}

$total_transfer = 0;
$tmp = array();
if(count($rs)>0){
	foreach($rs as $key => $row){

		$tmp[] = array(
			'NOMOR' => $key+1, //str_pad($key+1,4,'0',STR_PAD_LEFT),
			'NO_REKENING' => $row->NO_REKENING,
			'TOTAL_GAJI_BERSIH' => $row->TOTAL_GAJI_BERSIH,
			'NAMA' => $row->NAMA,
			'NIK' => $row->NIK,
			'KETERANGAN' => 'Gaji '.ucfirst(strtolower($PERIODE)),
		);
		$total_transfer = $total_transfer + $row->TOTAL_GAJI_BERSIH;
	}
}

$GLOBALS['COMPANY'] = strtoupper('PT. AIRKON PRATAMA');
$GLOBALS['NO_REKENING'] = strtoupper('123-00-04143931');
$GLOBALS['TOTAL_TRANSFER'] = $total_transfer;
$GLOBALS['TOTAL'] = $total_transfer;

// Merge data
$TBS->PlugIn(OPENTBS_SELECT_SHEET, 1);
$TBS->MergeBlock('a', $tmp);
$TBS->PlugIn(OPENTBS_SELECT_SHEET, 1);

// Output as a download file (some automatic fields are merged here)
if ($debug==3) { // debug mode 3
	$TBS->Plugin(OPENTBS_DEBUG_XML_SHOW);
} else {
	//$TBS->Show(OPENTBS_FILE, $file_name);
	$TBS->Show(OPENTBS_DOWNLOAD, 'DBANK '.$PROJECT.' '.$PERIODE.'.xlsx');
	//$TBS->Show(OPENTBS_FILE+TBS_EXIT, 'AIR CARGO MANIFEST '.$MAWB.'.xlsx');
}