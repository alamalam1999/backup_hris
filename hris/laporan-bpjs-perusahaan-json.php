<?php

include 'app-load.php';

is_login('laporan-penggajian.view');
$MODULE = 'LAPORAN-PENGGAJIAN';

set_search($MODULE, array('sort','order','PERIODE_ID','PROJECT_ID','NAMA'));
if( get_input('clear') ) clear_search($MODULE, array('PERIODE_ID','PROJECT_ID','NAMA'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'NAMA';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 500 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
if($PERIODE_ID = get_search($MODULE,'PERIODE_ID') AND !empty($PERIODE_ID)) $wh[] = " P.PERIODE_ID = '$PERIODE_ID' ";
if($PROJECT_ID = get_search($MODULE,'PROJECT_ID') AND !empty($PROJECT_ID)) $wh[] = " P.PROJECT_ID = '$PROJECT_ID' ";
if($NAMA = get_search($MODULE,'NAMA') AND !empty($NAMA)) $wh[] = " UCASE(K.NAMA) LIKE UCASE('$NAMA%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

// $rs = db_first("
// 	SELECT COUNT(1) as cnt
// 	FROM penggajian P 
// 	LEFT JOIN periode PE ON (PE.PERIODE_ID=P.PERIODE_ID) 
// 	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=P.KARYAWAN_ID) 
// 	{$where}
// ");
$NUM_ROWS = $rs->cnt;
	
$rs = db_fetch_limit("
	SELECT P.*, JB.JABATAN, K.NAMA, K.BPJS_KESEHATAN AS NO_BPJS_KESEHATAN, K.BPJS_KETENAGAKERJAAN AS NO_BPJS_KETENAGAKERJAAN
	FROM bpjs P 
	LEFT JOIN periode PE ON (PE.PERIODE_ID=P.PERIODE_ID) 
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=P.KARYAWAN_ID)
	LEFT JOIN jabatan JB ON (JB.JABATAN_ID=K.JABATAN_ID)
	
	{$where}
	ORDER BY $SORT $ORDER", $PER_PAGE, $OFFSET);

/*echo "SELECT P.*, PE.PERIODE, K.NAMA, K.NIK, K.TGL_MASUK, K.TIPE_GAJI, K.STATUS_PTKP,PO.POSISI FROM penggajian P LEFT JOIN periode PE ON (PE.PERIODE_ID=P.PERIODE_ID) LEFT JOIN karyawan K ON (K.KARYAWAN_ID=P.KARYAWAN_ID) LEFT JOIN posisi PO ON (PO.POSISI_ID=K.POSISI_ID) {$where} ORDER BY $SORT $ORDER";*/

$rows = $t = array();
$key = 0;
if(count($rs)>0){ foreach($rs as $row){
	
	//$t['NAMA'] = '<span class="tip" title="NIK : '.$row->NIK.', Tipe Gaji : '.$row->TIPE_GAJI.'">'.$row->NAMA.'</span>';
	$t['NAMA'] = $row->NAMA;
	$t['NO_BPJS_KESEHATAN'] = $row->NO_BPJS_KESEHATAN;
	$t['NO_BPJS_KETENAGAKERJAAN'] = $row->NO_BPJS_KETENAGAKERJAAN;
	$t['JABATAN'] = $row->JABATAN;
	$t['GAJI_POKOK'] = currency($row->GAJI_POKOK);
	$t['GAJI_POKOK_PRORATA'] = currency($row->GAJI_POKOK_PRORATA);
	$t['BPJS_JHT'] = currency($row->BPJS_JHT);
	$t['BPJS_JP'] = currency($row->BPJS_JP);
	$t['BPJS_JKK'] = currency($row->BPJS_JKK);
	$t['BPJS_JKM'] = currency($row->BPJS_JKM);
	$t['BPJS_KES'] = currency($row->BPJS_KES);

	$t['BPJS_JHT_PERUSAHAAN'] = currency($row->BPJS_JHT_PERUSAHAAN);
	$t['BPJS_JP_PERUSAHAAN'] = currency($row->BPJS_JP_PERUSAHAAN);
	$t['BPJS_JKK_PERUSAHAAN'] = currency($row->BPJS_JKK_PERUSAHAAN);
	$t['BPJS_JKM_PERUSAHAAN'] = currency($row->BPJS_JKM_PERUSAHAAN);
	$t['BPJS_KES_PERUSAHAAN'] = currency($row->BPJS_KES_PERUSAHAAN);
	
	$t['TOTAL_BPJS_KARYAWAN'] = currency($row->TOTAL_BPJS_KARYAWAN);
	$t['TOTAL_BPJS_PERUSAHAAN'] = currency($row->TOTAL_BPJS_PERUSAHAAN);


	
	
	$rows[$key] = (object) array_merge((array) $row, $t);
	$key++;
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);