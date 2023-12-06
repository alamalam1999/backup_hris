<?php

include 'app-load.php';

is_login('pertanyaan-interview.view');

set_search('PERTANYAAN-INTERVIEW', array('sort','order','PERTANYAAN-INTERVIEW'));
if( get_input('clear') ) clear_search('PERTANYAAN-INTERVIEW', array('PERTANYAAN-INTERVIEW'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'PERTANYAAN_ID';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
if($PERTANYAAN = get_input('PERTANYAAN') AND !empty($PERTANYAAN)) $wh[] = " UPPER(PERTANYAAN) LIKE UPPER('%$PERTANYAAN%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM pertanyaan
	{$where}
");
$NUM_ROWS = $rs->cnt;
	
$rs = db_fetch_limit("
	SELECT P.*
	FROM pertanyaan P
	{$where}
	ORDER BY $SORT $ORDER
	", $PER_PAGE, $OFFSET);

$rows = $t = array();
$key = 0;
if(count($rs)>0){ foreach($rs as $row){
	//$t['TGL_BERAKHIR'] = tgl($row->TGL_BERAKHIR);
	$rows[$key] = (object) array_merge((array) $row, $t);
	$key++;
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);