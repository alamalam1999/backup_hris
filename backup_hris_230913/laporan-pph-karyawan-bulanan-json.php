<?php

include 'app-load.php';

is_login('laporan-pph-karyawan-bulanan.view');

$MODULE = 'LAPORAN-PPH-KARYAWAN-BULANAN';

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
if($PROJECT_ID = get_search($MODULE,'PROJECT_ID') AND !empty($PROJECT_ID)) $wh[] = " K.PROJECT_ID = '$PROJECT_ID' ";
if($NAMA = get_search($MODULE,'NAMA') AND !empty($NAMA)) $wh[] = " UCASE(K.NAMA) LIKE UCASE('$NAMA%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);


$rs = db_fetch_limit("
	SELECT  PP.*,P.PERIODE AS PERIODE, PO.POSISI, K.NIK AS NIK, K.NAMA AS NAMA, K.TGL_MASUK
	FROM pph21 PP
	LEFT JOIN periode P ON (P.PERIODE_ID=PP.PERIODE_ID) 
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=PP.KARYAWAN_ID) 
	LEFT JOIN posisi PO ON (PO.POSISI_ID=K.POSISI_ID)
	
	{$where}
	ORDER BY $SORT $ORDER", $PER_PAGE, $OFFSET);
	$NUM_ROWS = count($rs);

//print_r($rs); die();
//echo $NUM_ROWS; die();
	


/*echo "SELECT P.*, PE.PERIODE, K.NAMA, K.NIK, K.TGL_MASUK, K.TIPE_GAJI, K.STATUS_PTKP,PO.POSISI FROM penggajian P LEFT JOIN periode PE ON (PE.PERIODE_ID=P.PERIODE_ID) LEFT JOIN karyawan K ON (K.KARYAWAN_ID=P.KARYAWAN_ID) LEFT JOIN posisi PO ON (PO.POSISI_ID=K.POSISI_ID) {$where} ORDER BY $SORT $ORDER";*/

$rows = $t = array();

if(count($rs)>0){ 
	foreach($rs as $key=>$row){
	
	$t['NAMA'] = $row->NAMA." (".$row->NIK.")";
	$t['TGL_MASUK'] = tgl($row->TGL_MASUK);
	$t['POSISI'] = ($row->POSISI);
	$t['PTKP'] = ($row->PTKP);
	$t['PERIODE'] = ($row->PERIODE)." (".$row->NIK.")";
	
	$t['TOTAL_PENGHASILAN'] = currency($row->TOTAL_PENGHASILAN);
	$t['TUNJ_JABATAN'] = currency($row->TUNJ_JABATAN);
	$t['TUNJ_OTTW'] = currency($row->TUNJ_OTTW);
	$t['TUNJ_LAINNYA_1'] = currency($row->TUNJ_LAINNYA_1);
	$t['TUNJ_LAINNYA_2'] = currency($row->TUNJ_LAINNYA_2);
	$t['TUNJ_LAINNYA_3'] = currency($row->TUNJ_LAINNYA_3);
	$t['TUNJ_LAINNYA_4'] = currency($row->TUNJ_LAINNYA_4);
	$t['TUNJ_LAINNYA_5'] = currency($row->TUNJ_LAINNYA_5);
	$t['GAJI_POKOK'] = currency($row->GAJI_POKOK);
	$t['GAJI_PRORATA'] = currency($row->GAJI_PRORATA);
	$t['TUNJANGAN_KELUARGA'] = currency($row->TUNJANGAN_KELUARGA);
	$t['THR'] = currency($row->THR);
	$t['JKK_KARYAWAN'] = currency($row->JKK_KARYAWAN);
	$t['JHT_KARYAWAN'] = currency($row->JHT_KARYAWAN);
	$t['JKM_KARYAWAN'] = currency($row->JKM_KARYAWAN);
	// $t['BPJS_KESEHATAN_KARYAWAN'] = currency($row->BPJS_KESEHATAN_KARYAWAN);
	$t['TOTAL_POTONGAN'] = currency($row->TOTAL_POTONGAN);
	$t['PPH21'] = currency($row->PPH21);
	
	$rows[$key] = (object) array_merge((array) $row, $t);
	
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);