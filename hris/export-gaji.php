<?php

require 'app-load.php';
require 'lib/excel_writer.php';
require 'lib/tbs/plugins/tbs_plugin_opentbs.php';
require 'lib/tbs/tbs_class.php';

$PERIODE_ID = db_escape(get_input('PERIODE_ID'));
$PROJECT_ID = db_escape(get_input('PROJECT_ID'));

$PERIODE = db_first(" SELECT TAHUN, BULAN FROM periode WHERE PERIODE_ID='$PERIODE_ID' ");
$PROJECT = db_first(" SELECT PROJECT FROM project WHERE PROJECT_ID='$PROJECT_ID' ");
$TAHUN = $PERIODE->TAHUN;

if($PERIODE->BULAN == '01')$BULAN = 'JANUARI';
if($PERIODE->BULAN == '02')$BULAN = 'FEBRUARI';
if($PERIODE->BULAN == '03')$BULAN = 'MARET';
if($PERIODE->BULAN == '04')$BULAN = 'APRIL';
if($PERIODE->BULAN == '05')$BULAN = 'MEI';
if($PERIODE->BULAN == '06')$BULAN = 'JUNI';
if($PERIODE->BULAN == '07')$BULAN = 'JULI';
if($PERIODE->BULAN == '08')$BULAN = 'AGUSTUS';
if($PERIODE->BULAN == '09')$BULAN = 'SEPTEMBER';
if($PERIODE->BULAN == '10')$BULAN = 'OKTOBER';
if($PERIODE->BULAN == '11')$BULAN = 'NOVEMBER';
if($PERIODE->BULAN == '12')$BULAN = 'DESEMBER';

$TBS = new clsTinyButStrong;
$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
$debug = (isset($_GET['debug'])) ? intval($_GET['debug']) : 0;
$template = 'static/laporan-penggajian.xlsx';
//print_r($template); die();
if (!file_exists($template)) exit("Template tidak ditemukan.");
$TBS->LoadTemplate($template);
if ($debug==2){ $TBS->Plugin(OPENTBS_DEBUG_XML_CURRENT); exit; }
elseif ($debug==1){ $TBS->Plugin(OPENTBS_DEBUG_INFO); exit; }
$rs = db_fetch("
	SELECT *
	FROM penggajian P
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=P.KARYAWAN_ID)
	
	WHERE P.PERIODE_ID='$PERIODE_ID' AND P.PROJECT_ID='$PROJECT_ID'
	ORDER BY K.KARYAWAN_ID ASC
");

// echo "<pre>";
// print_r($rs); die();

$no = 1;
		$tmp = array();

foreach($rs as $krow => $row){
	$KARYAWAN_ID = $row->KARYAWAN_ID;
	$BPJS = db_first(" SELECT * FROM bpjs B WHERE B.PERIODE_ID='$PERIODE_ID' AND B.KARYAWAN_ID='$KARYAWAN_ID' ");
	$TOTAL_TUNJANGAN = $row->TUNJ_LAINNYA_1+$row->TUNJ_LAINNYA_2+$row->TUNJ_LAINNYA_3+$row->TUNJ_LAINNYA_4+$row->TUNJ_OTTW;
	$TOTAL_PINJAMAN = $row->BIAYA_LAPTOP+$row->BIAYA_PEND_ANAK+$row->EKSES_KLAIM+$row->PINJAMAN_BANK_BWS+$row->PINJAMAN_KOPERASI_DINATERA+$row->PINJAMAN_KOPERASI_AVICENNA+$row->IURAN_KOPERASI_DINATERA+$row->IURAN_KOPERASI_AVICENNA;
	$TOTAL_BPJS = $BPJS->BPJS_JHT+$BPJS->BPJS_JP+$BPJS->BPJS_KES+$BPJS->BPJS_JKK+$BPJS->BPJS_JKM+$BPJS->BPJS_JHT_PERUSAHAAN+$BPJS->BPJS_JP_PERUSAHAAN+$BPJS->BPJS_JKK_PERUSAHAAN+$BPJS->BPJS_JKM_PERUSAHAAN+$BPJS->BPJS_KES_PERUSAHAAN;
	$TOTAL_BPJS_KARYAWAN = $BPJS->BPJS_JHT+$BPJS->BPJS_JP+$BPJS->BPJS_KES+$BPJS->BPJS_JKK+$BPJS->BPJS_JKM;
	$TOTAL_POTONGAN = $TOTAL_PINJAMAN + $TOTAL_BPJS_KARYAWAN;
	$tmp[] = array(
					'NO' => $no++,
					'NAMA' => $row->NAMA,
					'GAJI_POKOK' => currency($row->GAJI_POKOK),
					'TUNJ_KELUARGA' => currency($row->TUNJ_KELUARGA),

					'TUNJ_LAINNYA_1' => currency($row->TUNJ_LAINNYA_1),
					'TUNJ_LAINNYA_2' => currency($row->TUNJ_LAINNYA_2),
					'TUNJ_LAINNYA_3' => currency($row->TUNJ_LAINNYA_3),
					'TUNJ_LAINNYA_4' => currency($row->TUNJ_LAINNYA_4),
					'TUNJ_LAINNYA_4' => currency($row->TUNJ_LAINNYA_4),
					'TUNJ_OTTW' => currency($row->TUNJ_OTTW),
					'NIK' => $row->NIK,
					'TOTAL_TUNJANGAN' => currency($TOTAL_TUNJANGAN),

					'PINJAMAN_KOPERASI_DINATERA' => currency($row->PINJAMAN_KOPERASI_DINATERA),
					'IURAN_KOPERASI_DINATERA' => currency($row->IURAN_KOPERASI_DINATERA),		
					'PINJAMAN_KOPERASI_AVICENNA' => currency($row->PINJAMAN_KOPERASI_AVICENNA),
					'IURAN_KOPERASI_AVICENNA' => currency($row->IURAN_KOPERASI_AVICENNA),
					'PINJAMAN_BANK_BWS' => currency($row->PINJAMAN_BANK_BWS),
					'EKSES_KLAIM' => currency($row->EKSES_KLAIM),
					'BIAYA_PEND_ANAK' => currency($row->BIAYA_PEND_ANAK),
					'BIAYA_LAPTOP' => currency($row->BIAYA_LAPTOP),
					'TOTAL_POTONGAN' => currency($TOTAL_PINJAMAN),

					'BPJS_JHT' => currency($BPJS->BPJS_JHT),
					'BPJS_JP' => currency($BPJS->BPJS_JP),
					'BPJS_KES' => currency($BPJS->BPJS_KES),
					'BPJS_JKK' => currency($BPJS->BPJS_JKK),
					'BPJS_JKM' => currency($BPJS->BPJS_JKM),
					'BPJS_JHT_PERUSAHAAN' => currency($BPJS->BPJS_JHT_PERUSAHAAN),
					'BPJS_JP_PERUSAHAAN' => currency($BPJS->BPJS_JP_PERUSAHAAN),
					'BPJS_JKK_PERUSAHAAN' => currency($BPJS->BPJS_JKK_PERUSAHAAN),
					'BPJS_JKM_PERUSAHAAN' => currency($BPJS->BPJS_JKM_PERUSAHAAN),
					'BPJS_KES_PERUSAHAAN' => currency($BPJS->BPJS_KES_PERUSAHAAN),
					'TOTAL_BPJS' => currency($TOTAL_BPJS),
					'ADJUSMENT_PLUS' => currency($row->ADJUSMENT_PLUS),
					'ADJUSMENT_MINUS' => currency($row->ADJUSMENT_MINUS),


					'TOTAL_TRANSFER' => currency( 
													$row->TOTAL_GAJI_BERSIH
												),
						
				);

}

// echo "<pre>";
// print_r($tmp); die();
$etc = array();
$JUDUL 	= "REKAP PAYROLL ".$BULAN." ".$TAHUN;
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

