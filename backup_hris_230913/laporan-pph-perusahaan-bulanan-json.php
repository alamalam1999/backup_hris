<?php

include 'app-load.php';

is_login('laporan-pph-perusahaan-bulanan.view');

$MODULE = 'LAPORAN-PPH-PERUSAHAAN-BULANAN';


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
if($PERIODE_ID = get_search($MODULE,'PERIODE_ID') AND !empty($PERIODE_ID)) $wh[] = " PP.PERIODE_ID = '$PERIODE_ID' ";
if($PROJECT_ID = get_search($MODULE,'PROJECT_ID') AND !empty($PROJECT_ID)) $wh[] = " PP.PROJECT_ID = '$PROJECT_ID' ";

if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);


// echo $PERIODE_ID;
// echo $PROJECT_ID;
// print_r($where);
// die();
	
$rs = db_fetch_limit("
	SELECT  PP.*,P.PERIODE AS PERIODE, 
	PR.PROJECT AS UNIT, SUM(PP.PPH21) AS TOTAL_PPH,
	SUM(PP.TOTAL_PENGHASILAN) AS TOTAL_PENGHASILAN_,
	SUM(PP.GAJI_POKOK) AS GAJI_POKOK_,
	SUM(PP.GAJI_PRORATA) AS GAJI_PRORATA_,
	SUM(PP.TUNJANGAN_KELUARGA) AS TUNJANGAN_KELUARGA_,
	SUM(PP.THR) AS THR_,
	SUM(PP.JKK_KARYAWAN) AS JKK_KARYAWAN_,
	SUM(PP.JHT_KARYAWAN) AS JHT_KARYAWAN_,
	SUM(PP.JKM_KARYAWAN) AS JKM_KARYAWAN_,
	SUM(PP.BPJS_KESEHATAN_KARYAWAN) AS BPJS_KESEHATAN_KARYAWAN_,
	SUM(PP.TOTAL_POTONGAN) AS TOTAL_POTONGAN_
	FROM pph21 PP
	LEFT JOIN periode P ON (P.PERIODE_ID=PP.PERIODE_ID) 
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=PP.KARYAWAN_ID) 
	LEFT JOIN project PR ON (PR.PROJECT_ID=PP.PROJECT_ID) 
	
	
	{$where}
	GROUP BY PR.PROJECT_ID

	ORDER BY $SORT $ORDER", $PER_PAGE, $OFFSET);
	$NUM_ROWS = count($rs);
/*echo "SELECT P.*, PE.PERIODE, K.NAMA, K.NIK, K.TGL_MASUK, K.TIPE_GAJI, K.STATUS_PTKP,PO.POSISI FROM penggajian P LEFT JOIN periode PE ON (PE.PERIODE_ID=P.PERIODE_ID) LEFT JOIN karyawan K ON (K.KARYAWAN_ID=P.KARYAWAN_ID) LEFT JOIN posisi PO ON (PO.POSISI_ID=K.POSISI_ID) {$where} ORDER BY $SORT $ORDER";*/

$rows = $t = array();
$key = 0;
if(count($rs)>0){ foreach($rs as $row){
	
	
	$t['TOTAL_PPH'] = currency($row->TOTAL_PPH);
	$t['TOTAL_PENGHASILAN_'] = currency($row->TOTAL_PENGHASILAN_);
	$t['GAJI_POKOK_'] = currency($row->GAJI_POKOK_);
	$t['GAJI_PRORATA_'] = currency($row->GAJI_PRORATA_);
	$t['TUNJANGAN_KELUARGA_'] = currency($row->TUNJANGAN_KELUARGA_);
	$t['THR_'] = currency($row->THR_);
	$t['JKK_KARYAWAN_'] = currency($row->JKK_KARYAWAN_);
	$t['JHT_KARYAWAN_'] = currency($row->JHT_KARYAWAN_);
	$t['JKM_KARYAWAN_'] = currency($row->JKM_KARYAWAN_);
	$t['BPJS_KESEHATAN_KARYAWAN_'] = currency($row->BPJS_KESEHATAN_KARYAWAN_);
	$t['TOTAL_POTONGAN_'] = currency($row->TOTAL_POTONGAN_);
	
	
	
	$rows[$key] = (object) array_merge((array) $row, $t);
	$key++;
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);