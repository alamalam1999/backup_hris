<?php

include 'app-load.php';

is_login('laporan-summary-penggajian.view');

set_search('LAPORAN-PENGGAJIAN', array('sort','order','START_DATE','FINISH_DATE','COMPANY_ID','PROJECT_ID'));
if( get_input('clear') ) clear_search('LAPORAN-PENGGAJIAN', array('START_DATE','FINISH_DATE','COMPANY_ID','PROJECT_ID'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'P.PROJECT';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 2 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
if($COMPANY_ID = get_input('COMPANY_ID') AND !empty($COMPANY_ID)) $wh[] = " P.COMPANY_ID = '$COMPANY_ID' ";
if($PROJECT_ID = get_input('PROJECT_ID') AND !empty($PROJECT_ID)) $wh[] = " A.PROJECT_ID = '$PROJECT_ID' ";
if($START_DATE = get_input('START_DATE') AND !empty($START_DATE) AND $FINISH_DATE = get_input('FINISH_DATE') AND !empty($FINISH_DATE)) 
	{
		$wh[] = " R.TANGGAL_MULAI >= '$START_DATE' OR '$FINISH_DATE' <= R.TANGGAL_SELESAI2 ";
	}

if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rsa = db_first("
	SELECT COUNT(1) as cnt
	FROM penggajian A
	LEFT JOIN periode R ON (R.PERIODE_ID=A.PERIODE_ID) 
	LEFT JOIN project P ON (P.PROJECT_ID=A.PROJECT_ID)
	LEFT JOIN company C ON (C.COMPANY_ID=P.COMPANY_ID)
	{$where}
	GROUP BY A.PROJECT_ID, P.PROJECT
");
$NUM_ROWS = $rsa->cnt;
	
$rs = db_fetch("
	SELECT
		P.PROJECT,R.PERIODE,C.COMPANY,
		SUM(GAJI_POKOK_NET) as GAPOK_NET,
		SUM(TOTAL_TUNJANGAN) as TUNJANGAN,
		SUM(BPJS_JHT) as TOTAL_BPJS_JHT,
		SUM(BPJS_JP) as TOTAL_BPJS_JP,
		SUM(BPJS_KES) as TOTAL_BPJS_KES,
		SUM(TOTAL_GAJI_BERSIH) as GAJI_BERSIH
	FROM penggajian A
	LEFT JOIN periode R ON (R.PERIODE_ID=A.PERIODE_ID) 
	LEFT JOIN project P ON (P.PROJECT_ID=A.PROJECT_ID)
	LEFT JOIN company C ON (C.COMPANY_ID=P.COMPANY_ID)
	{$where}
	GROUP BY A.PROJECT_ID, P.PROJECT
	ORDER BY $SORT $ORDER", $PER_PAGE, $OFFSET);

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