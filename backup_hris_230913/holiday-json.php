<?php

include 'app-load.php';

is_login('holiday.view');

set_search('HOLIDAY', array('sort','order','YEAR','NAMA'));
if( get_input('clear') ) clear_search('HOLIDAY', array('YEAR','NAMA'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'DATE';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'desc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
if($YEAR = get_input('YEAR') AND !empty($YEAR)) $wh[] = " YEAR = '$YEAR' ";
if($NAMA = get_input('NAMA') AND !empty($NAMA)) $wh[] = " UCASE(HOLIDAY) LIKE UCASE('%$NAMA%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM holiday A
	{$where}
");
$NUM_ROWS = $rs->cnt;
	
$rs = db_fetch_limit("
	SELECT *
	FROM holiday A
	{$where}
	ORDER BY $SORT $ORDER
	", $PER_PAGE, $OFFSET);

$rows = $t = array();
$key = 0;
if(count($rs)>0){ foreach($rs as $row){
	
	$t['DATE'] = tgl($row->DATE);
	$rows[$key] = (object) array_merge((array) $row, $t);
	$key++;
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);