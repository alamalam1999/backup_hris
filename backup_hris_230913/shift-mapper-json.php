<?php

include 'app-load.php';

is_login('shift-mapper.view');

$OVERNIGHT = array(
	'NO' => '<span style="color:#0000ff;">NO</span>',
	'YES' => '<span style="color:#ff0000;">YES</span>',
);

set_search('SHIFT-MAPPER', array('sort','order','PROJECT_ID','SHIFT_CODE'));
if( get_input('clear') ) clear_search('SHIFT-MAPPER', array('PROJECT_ID','SHIFT_CODE'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'PROJECT';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
if($PROJECT_ID = get_input('PROJECT_ID') AND !empty($PROJECT_ID)) $wh[] = " A.PROJECT_ID = '$PROJECT_ID' ";
if($SHIFT_CODE = get_input('SHIFT_CODE') AND !empty($SHIFT_CODE)) $wh[] = " (A.VAR = '$SHIFT_CODE' OR A.VAL = '$SHIFT_CODE') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM shift_mapper A
	{$where}
");
$NUM_ROWS = $rs->cnt;
	
$rs = db_fetch_limit("
	SELECT *
	FROM shift_mapper A
	LEFT JOIN project P ON P.PROJECT_ID=A.PROJECT_ID
	LEFT JOIN shift S ON S.SHIFT_CODE=A.VAL
	{$where}
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