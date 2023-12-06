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

if ($PERIODE->BULAN == '01') $BULAN = 'JANUARI';
if ($PERIODE->BULAN == '02') $BULAN = 'FEBRUARI';
if ($PERIODE->BULAN == '03') $BULAN = 'MARET';
if ($PERIODE->BULAN == '04') $BULAN = 'APRIL';
if ($PERIODE->BULAN == '05') $BULAN = 'MEI';
if ($PERIODE->BULAN == '06') $BULAN = 'JUNI';
if ($PERIODE->BULAN == '07') $BULAN = 'JULI';
if ($PERIODE->BULAN == '08') $BULAN = 'AGUSTUS';
if ($PERIODE->BULAN == '09') $BULAN = 'SEPTEMBER';
if ($PERIODE->BULAN == '10') $BULAN = 'OKTOBER';
if ($PERIODE->BULAN == '11') $BULAN = 'NOVEMBER';
if ($PERIODE->BULAN == '12') $BULAN = 'DESEMBER';

$TBS = new clsTinyButStrong;
$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
$debug = (isset($_GET['debug'])) ? intval($_GET['debug']) : 0;
$template = 'static/export-tmp.xlsx';

if (!file_exists($template)) exit("Template tidak ditemukan.");
$TBS->LoadTemplate($template);
if ($debug == 2) {
    $TBS->Plugin(OPENTBS_DEBUG_XML_CURRENT);
    exit;
} elseif ($debug == 1) {
    $TBS->Plugin(OPENTBS_DEBUG_INFO);
    exit;
}
$rs = db_fetch("
	SELECT P.*, K.NAMA, K.KARYAWAN_ID, K.NIK, J.JABATAN
	FROM tmp P
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=P.KARYAWAN_ID)
	LEFT JOIN jabatan J ON J.JABATAN_ID=K.JABATAN_ID
	WHERE P.PERIODE_ID='$PERIODE_ID' AND P.PROJECT_ID='$PROJECT_ID'
	ORDER BY J.JABATAN ASC
");

$no = 1;
$data = array();

foreach ($rs as $krow => $row) {
    $data[] = array(
        'NO' => $no++,
        'NAMA' => $row->NAMA,
        'NIK' => $row->NIK,
        'JABATAN' => $row->JABATAN,
        'TOTAL_HK' => $row->TOTAL_HK,
        'TOTAL_ATT' => $row->TOTAL_ATT,
        'TOTAL_ALL_SAKIT' => $row->TOTAL_ALL_SAKIT,
        'TOTAL_ALL_IJIN_CUTI' => $row->TOTAL_ALL_IJIN_CUTI,
        'TOTAL_ABS' => $row->TOTAL_ABS,
        'TOTAL_FP1' => $row->TOTAL_FP1,
        'TOTAL_LATE_EARLY' => $row->TOTAL_LATE_EARLY,
        'TOTAL_REAL_ATT' => $row->TOTAL_REAL_ATT,
        'TOTAL_DINAS' => $row->TOTAL_DINAS,
        'TUNJ_KOMUNIKASI' => $row->TUNJ_KOMUNIKASI,
        'TUNJ_MAKAN' => $row->TUNJ_MAKAN,
        'TUNJ_TRANSPORT' => $row->TUNJ_TRANSPORT,
        'TOTAL_TUNJ_MAKAN' => $row->TOTAL_TUNJ_MAKAN,
        'TOTAL_TUNJ_TRANSPORT' => $row->TOTAL_TUNJ_TRANSPORT,
        'TOTAL_TUNJ_KOMUNIKASI' => $row->TOTAL_TUNJ_KOMUNIKASI,
        'TOTAL_TUNJANGAN_AWAL' => $row->TOTAL_TUNJANGAN_AWAL,
        'TOTAL_PELANGGARAN' => $row->TOTAL_PELANGGARAN,
        'TRANSPORT_DINAS' => $row->TRANSPORT_DINAS,
        'LEMBUR' => $row->LEMBUR,
        'DITRANSFER' => $row->DITRANSFER,
        'KETERANGAN' => $row->KETERANGAN,
    );
}

$etc = array();
$UNIT = strtoupper($PROJECT->PROJECT);
$JUDUL = "DAFTAR MAKAN & TRANSPORT " . $UNIT;
$PERIODE = $BULAN . " " . $TAHUN;
$etc[] = array(
    'JUDUL' => $JUDUL,
    'PERIODE' => $PERIODE,
    'TEMPAT_WAKTU' => "Jakarta, " . tgl(date('Y-m-d')),
);

$filename = $JUDUL . '-' . $PERIODE . '.xlsx';
$TBS->PlugIn(OPENTBS_SELECT_SHEET, 1);
$TBS->MergeBlock('A', $data);
$TBS->MergeBlock('B', $etc);
if ($debug == 3) {
    $TBS->Plugin(OPENTBS_DEBUG_XML_SHOW);
} else {
    $TBS->Show(OPENTBS_DOWNLOAD, $filename);
    $TBS->Show(OPENTBS_FILE + TBS_EXIT, $filename);
}
