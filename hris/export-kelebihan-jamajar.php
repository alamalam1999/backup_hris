<?php

require 'app-load.php';
require 'lib/excel_writer.php';
require 'lib/tbs/plugins/tbs_plugin_opentbs.php';
require 'lib/tbs/tbs_class.php';



$PROJECT_ID = db_escape(get_input('DOWN_PROJECT_ID'));
//$PERIODE_ID = db_escape(get_input('DOWN_PERIODE_ID'));
//echo $PROJECT_ID;
//echo $PERIODE_ID;

// echo "string";
// print_r($PROJECT_ID); die();

$PROJECT = db_first(" SELECT PROJECT FROM project WHERE PROJECT_ID='$PROJECT_ID' ");
$WHERE = '';

// echo "<pre>";
// print_r($PROJECT_ID); die();

$TBS = new clsTinyButStrong;
$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
$debug = (isset($_GET['debug'])) ? intval($_GET['debug']) : 0;
$template = 'static/template-kelebihan-ja.xlsx';
//print_r($template); die();
if (!file_exists($template)) exit("Template tidak ditemukan.");
$TBS->LoadTemplate($template);
if ($debug==2){ $TBS->Plugin(OPENTBS_DEBUG_XML_CURRENT); exit; }
elseif ($debug==1){ $TBS->Plugin(OPENTBS_DEBUG_INFO); exit; }
//die();

$rs = db_fetch("
	SELECT *
	FROM karyawan 	
	
	WHERE PROJECT_ID = '$PROJECT_ID'
	ORDER BY KARYAWAN_ID ASC
");


// echo "<pre>";
// print_r($rs);
// die();

$no = 1;
$tmp = array();

foreach($rs as $krow => $row){
		
	// print_r($row->NAMA);
	// die();
	$tmp[] = array(
		'NO' => $no++,			
		'PIN' => $row->KARYAWAN_ID,
		'NIK' => $row->NIK,
		'NAMA' => $row->NAMA,
		);

}

// echo "<pre>";
// print_r($tmp); die();
$etc = array();
$JUDUL 	= "KELEBIHAN_JAMAJAR";
$UNIT = strtoupper($PROJECT->PROJECT);
$etc[] = array(
					'JUDUL'=>$JUDUL,
					'JUDUL2'=>$UNIT,
				);
//print_r($etc); die;
$filename = $JUDUL.'.xls';
$TBS->PlugIn(OPENTBS_SELECT_SHEET, 1);
$TBS->MergeBlock('A', $tmp);
$TBS->MergeBlock('B', $etc);
if ($debug==3) {
	$TBS->Plugin(OPENTBS_DEBUG_XML_SHOW);
} else {
	$TBS->Show(OPENTBS_DOWNLOAD, $filename);
	$TBS->Show(OPENTBS_FILE+TBS_EXIT, $filename);
}

