<?php

include 'app-load.php';

is_login('kpi.view');

set_search('KPI', array('sort','order','NAMA','STRUKTUR_ID'));
if( get_input('clear') ) clear_search('KPI', array('NAMA','STRUKTUR_ID'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'INDICATOR';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
if($STRUKTUR_ID = get_input('STRUKTUR_ID') AND !empty($STRUKTUR_ID)) $wh[] = " K.STRUKTUR_ID = '$STRUKTUR_ID' ";
if($NAMA = get_input('NAMA') AND !empty($NAMA)) $wh[] = " UPPER(INDICATOR) LIKE UPPER('%$NAMA%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM kpi K
	LEFT JOIN struktur S ON (S.STRUKTUR_ID=K.STRUKTUR_ID)
	{$where}
");
$NUM_ROWS = $rs->cnt;
	
$rs = db_fetch_limit("
	SELECT *
	FROM kpi K
	LEFT JOIN struktur S ON (S.STRUKTUR_ID=K.STRUKTUR_ID)
	{$where}
	ORDER BY S.STRUKTUR asc, $SORT $ORDER
	", $PER_PAGE, $OFFSET);

$rows = $t = array();
$key = 0;
if(count($rs)>0){ foreach($rs as $row){
	$rows[$key] = (object) array_merge((array) $row, $t);
	$key++;
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);