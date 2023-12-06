<?php

include 'app-load.php';

is_login('shift.view');

$OVERNIGHT = array(
	'NO' => '<span style="color:#0000ff;">NO</span>',
	'YES' => '<span style="color:#ff0000;">YES</span>',
);

set_search('SHIFT', array('sort','order','SHIFT_CODE'));
if( get_input('clear') ) clear_search('SHIFT', array('SHIFT_CODE'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'SHIFT_CODE';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
if($SHIFT_CODE = get_input('SHIFT_CODE') AND !empty($SHIFT_CODE)) $wh[] = " UPPER(SHIFT_CODE) LIKE UPPER('%$SHIFT_CODE%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM shift A {$where}
	");
$NUM_ROWS = $rs->cnt;
	
$rs = db_fetch_limit("
	SELECT *
	FROM shift A {$where}
	ORDER BY $SORT $ORDER
	", $PER_PAGE, $OFFSET);

$rows = $t = array();
$key = 0;
if(count($rs)>0){ foreach($rs as $row){
	$t['SHIFT_COLOR'] = '<div style="background-color:'.$row->SHIFT_COLOR.';">&nbsp;</div>';
	$t['OVERNIGHT'] = isset($OVERNIGHT[$row->OVERNIGHT]) ? $OVERNIGHT[$row->OVERNIGHT] : '';
	$rows[$key] = (object) array_merge((array) $row, $t);
	$key++;
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);