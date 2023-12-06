<?php

include 'app-load.php';

#is_login('rekap.view');

set_search('REKAP', array('sort','order','PERIODE_ID','NAMA'));
if( get_input('clear') ) clear_search('REKAP', array('PERIODE_ID','NAMA'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'PROJECT';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
if($PERIODE_ID = get_input('PERIODE_ID') AND !empty($PERIODE_ID)) $wh[] = " A.PERIODE_ID = '$PERIODE_ID' ";
if($NAMA = get_input('NAMA') AND !empty($NAMA)) $wh[] = " UPPER(PROJECT) LIKE UPPER('%$NAMA%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);
	
$rs = db_fetch("
	SELECT
		P.PROJECT,
		SUM(GAJI_POKOK_NET) as GAPOK_NET,
		SUM(TOTAL_TUNJANGAN) as TUNJANGAN,
		SUM(BPJS_JHT) as TOTAL_BPJS_JHT,
		SUM(BPJS_JP) as TOTAL_BPJS_JP,
		SUM(BPJS_KES) as TOTAL_BPJS_KES,
		SUM(TOTAL_GAJI_BERSIH) as GAJI_BERSIH
	FROM penggajian A
	LEFT JOIN periode R ON (R.PERIODE_ID=A.PERIODE_ID) 
	LEFT JOIN project P ON (P.PROJECT_ID=A.PROJECT_ID)
	{$where}
	GROUP BY A.PROJECT_ID, P.PROJECT
	ORDER BY $SORT $ORDER
");

$rows = $t = array();
$key = 0;
if(count($rs)>0){ foreach($rs as $row){
	
	$t['GAJI_POKOK_NET'] = currency($row->GAPOK_NET);
	$t['TUNJANGAN'] = currency($row->TUNJANGAN);
	$t['TOTAL_BPJS_JHT'] = currency($row->TOTAL_BPJS_JHT);
	$t['TOTAL_BPJS_JP'] = currency($row->TOTAL_BPJS_JP);
	$t['TOTAL_BPJS_KES'] = currency($row->TOTAL_BPJS_KES);
	$t['GAJI_BERSIH'] = currency($row->GAJI_BERSIH);
	
	$rows[$key] = (object) array_merge((array) $row, $t);
	$key++;
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);