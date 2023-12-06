<?php

include 'app-load.php';

is_login('project.view');

set_search('PROJECT', array('sort','order','COMPANY_ID','NAMA'));
if( get_input('clear') ) clear_search('PROJECT', array('COMPANY_ID','NAMA'));

$CUTOFF = array(
	'0' => 'Absensi = Payroll',
	'1' => '<span style="color:red;">Absensi <> Payroll</span>'
);

$SHOWING = array(
	'0' => '<span style="color:red;">TIDAK</span>',
	'1' => '<span style="color:green;">YA</span>'
);

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'PROJECT';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
if($COMPANY_ID = get_input('COMPANY_ID') AND !empty($COMPANY_ID)) $wh[] = " C.COMPANY_ID = '$COMPANY_ID' ";
if($NAMA = get_input('NAMA') AND !empty($NAMA)) $wh[] = " UPPER(PROJECT) LIKE UPPER('%$NAMA%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM project A
	LEFT JOIN company C ON (C.COMPANY_ID=A.COMPANY_ID) 
	{$where}
");
$NUM_ROWS = $rs->cnt;
	
$rs = db_fetch_limit("
	SELECT *
	FROM project A 
	LEFT JOIN company C ON (C.COMPANY_ID=A.COMPANY_ID) 
	{$where}
	ORDER BY $SORT $ORDER
	", $PER_PAGE, $OFFSET);

$rows = $t = array();
$key = 0;
if(count($rs)>0){ foreach($rs as $row){
	
	$t['CUTOFF'] = isset($CUTOFF[$row->CUTOFF]) ? $CUTOFF[$row->CUTOFF] : '';
	$t['SHOWING'] = isset($SHOWING[$row->SHOWING]) ? $SHOWING[$row->SHOWING] : '';
	$t['START_DATE'] = tgl($row->START_DATE);
	$t['FINISH_DATE'] = tgl($row->FINISH_DATE);
	$rows[$key] = (object) array_merge((array) $row, $t);
	$key++;
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);