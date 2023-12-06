<?php

include 'app-load.php';

is_login('thr.view');

$MODULE = 'THR';
set_search($MODULE, array('sort','order','PERIODE_ID','PROJECT_ID','NAMA'));
if( get_input('clear') ) clear_search($MODULE, array('PERIODE_ID','PROJECT_ID','NAMA'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'NAMA';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
if($PERIODE_ID = get_search($MODULE,'PERIODE_ID') AND !empty($PERIODE_ID)) $wh[] = " P.PERIODE_ID = '$PERIODE_ID' ";
if($PROJECT_ID = get_search($MODULE,'PROJECT_ID') AND !empty($PROJECT_ID)) $wh[] = " P.PROJECT_ID = '$PROJECT_ID' ";
if($NAMA = get_search($MODULE,'NAMA') AND !empty($NAMA)) $wh[] = " UCASE(K.NAMA) LIKE UCASE('$NAMA%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM thr P 
	LEFT JOIN periode PE ON (PE.PERIODE_ID=P.PERIODE_ID) 
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=P.KARYAWAN_ID) 
	{$where}
");
$NUM_ROWS = $rs->cnt;
	
$rs = db_fetch_limit("
	SELECT P.*, PE.PERIODE, K.NAMA, K.NIK, K.TGL_MASUK
	FROM thr P 
	LEFT JOIN periode PE ON (PE.PERIODE_ID=P.PERIODE_ID) 
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=P.KARYAWAN_ID)
	{$where}
	ORDER BY $SORT $ORDER
	", $PER_PAGE, $OFFSET);

$rows = $t = array();
$key = 0;
if(count($rs)>0){ foreach($rs as $row){
	
	$t['TGL_MASUK'] = tgl($row->TGL_MASUK);
	$t['GAJI_POKOK'] = currency($row->GAJI_POKOK);
	$t['THR'] = currency($row->THR);
	
	$rows[$key] = (object) array_merge((array) $row, $t);
	$key++;
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);