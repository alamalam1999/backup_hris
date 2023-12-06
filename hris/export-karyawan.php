<?php

require 'app-load.php';
require 'lib/excel_writer.php';
require 'lib/tbs/plugins/tbs_plugin_opentbs.php';
require 'lib/tbs/tbs_class.php';


$PROJECT_ID = db_escape(get_input('PROJECT_ID'));
// echo "string";
// print_r($PROJECT_ID); die();

$PROJECT = db_first(" SELECT PROJECT FROM project WHERE PROJECT_ID='$PROJECT_ID' ");
$WHERE = '';
if($PROJECT_ID != ''){
$WHERE = " WHERE PROJECT_ID ='$PROJECT_ID' ";
}
// echo "<pre>";
// print_r($PROJECT_ID); die();

$TBS = new clsTinyButStrong;
$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
$debug = (isset($_GET['debug'])) ? intval($_GET['debug']) : 0;
$template = 'static/laporan-karyawan.xlsx';
//print_r($template); die();
if (!file_exists($template)) exit("Template tidak ditemukan.");
$TBS->LoadTemplate($template);
if ($debug==2){ $TBS->Plugin(OPENTBS_DEBUG_XML_CURRENT); exit; }
elseif ($debug==1){ $TBS->Plugin(OPENTBS_DEBUG_INFO); exit; }
//die();

$rs = db_fetch("
	SELECT *
	FROM karyawan 	
	
	$WHERE
	ORDER BY KARYAWAN_ID ASC
");




$no = 1;
$tmp = array();

foreach($rs as $krow => $row){
	$JABATAN_ID = $row->JABATAN_ID;
	$KARYAWAN_ID = $row->KARYAWAN_ID;
	$K_PROJECT_ID = $row->PROJECT_ID;
	$PROJECT = db_first(" SELECT PROJECT FROM project WHERE PROJECT_ID='$K_PROJECT_ID' ");
	$JABATAN = db_first(" SELECT JABATAN FROM jabatan WHERE JABATAN_ID='$JABATAN_ID' ");
	$UNIT = $PROJECT->PROJECT;
	$JABATAN = $JABATAN->JABATAN;
	$HP = $row->HP;
	$tmp[] = array(
					'NO' => $no++,
					'NAMA' => $row->NAMA,
					'NIK' => $row->NIK,
					'JK' => $row->JK,
					'TGL_MASUK' => $row->TGL_MASUK,
					'TGL_SK' => $row->TGL_SK,
					'TGL_MULAI_KONTRAK' => $row->TGL_MULAI_KONTRAK,
					'TGL_SELESAI_KONTRAK' => $row->TGL_SELESAI_KONTRAK,
					'TP_LAHIR' => $row->TP_LAHIR,
					'TGL_LAHIR' => $row->TGL_LAHIR,
					'NO_IDENTITAS' => $row->NO_IDENTITAS,
					'NPWP' => $row->NPWP,
					'UNIT' => $UNIT,
					'JABATAN' => $JABATAN,
					'ALAMAT_KTP' => $row->ALAMAT_KTP,
					'ALAMAT' => $row->ALAMAT,
					'HP' => '',
					'ST_KAWIN' => $row->ST_KAWIN,
					'LULUSAN' => $row->LULUSAN,
					'JURUSAN' => $row->JURUSAN,
					'IBU_KANDUNG' => $row->IBU_KANDUNG,
					'TAHUN_LULUS' => $row->TAHUN_LULUS,
					'PENGALAMAN' => $row->PENGALAMAN,
					'NO_REKENING' => $row->NO_REKENING,
					'NAMA_BANK' => $row->NAMA_BANK,
					//'AKUN_BANK' => $AKUN_BANK,
					'EMAIL' => $row->EMAIL,
					'NO_KK' => $row->NO_KK,
					'AGAMA' => $row->AGAMA,
					'GOL_DARAH' => $row->GOL_DARAH,
					
						
				);

}

/*echo "<pre>";
print_r($tmp); die();*/
$etc = array();
$JUDUL 	= "REKAP KARYAWAN";
$UNIT = strtoupper($PROJECT->PROJECT);
$etc[] = array(
					'JUDUL'=>$JUDUL,
					'JUDUL2'=>$UNIT,
				);
//print_r($etc); die;
$filename = $JUDUL.'-'.$UNIT.'.xlsx';
$TBS->PlugIn(OPENTBS_SELECT_SHEET, 1);
$TBS->MergeBlock('A', $tmp);
$TBS->MergeBlock('B', $etc);
if ($debug==3) {
	$TBS->Plugin(OPENTBS_DEBUG_XML_SHOW);
} else {
	$TBS->Show(OPENTBS_DOWNLOAD, $filename);
	$TBS->Show(OPENTBS_FILE+TBS_EXIT, $filename);
}

