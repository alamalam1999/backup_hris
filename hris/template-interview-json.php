<?php

include 'app-load.php';

is_login('template-interview.view');

set_search('TEMPLATE-INTERVIEW', array('sort','order','TEMPLATE-INTERVIEW'));
if( get_input('clear') ) clear_search('TEMPLATE-INTERVIEW', array('TEMPLATE-INTERVIEW'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'TEMPLATE_ID';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
if($TEMPLATE = get_input('TEMPLATE') AND !empty($TEMPLATE)) $wh[] = " UPPER(TEMPLATE) LIKE UPPER('%$TEMPLATE%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM template 
	{$where}
");
$NUM_ROWS = $rs->cnt;
	
$rs = db_fetch_limit("
	SELECT T.*,GROUP_CONCAT(P.PERTANYAAN SEPARATOR '<hr>') AS PERTANYAAN
	FROM template T
	LEFT JOIN template_pertanyaan TI ON (TI.TEMPLATE_ID=T.TEMPLATE_ID) 
	LEFT JOIN pertanyaan P ON (P.PERTANYAAN_ID=TI.PERTANYAAN_ID) 
	{$where} GROUP BY T.TEMPLATE_ID
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

//print_r($tmp['rows']); die();

echo json_encode($tmp);