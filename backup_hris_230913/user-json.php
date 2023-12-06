<?php

include 'app-load.php';

is_login('user.view');
	
$ST = array(
		'PENDING' => '<span style="color:#0000ff;">PENDING</span>',
		'APPROVED' => '<span style="color:#00c000;">APPROVED</span>',
		'CANCEL' => '<span style="color:#ff0000;">CANCEL</span>',
	);

set_search('USER', array('sort','order','LEVEL_ID','PROJECT_ID','NAMA'));
if( get_input('clear') ) clear_search('USER', array('LEVEL_ID','PROJECT_ID','NAMA'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'NAMA';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
if($LEVEL_ID = get_input('LEVEL_ID') AND !empty($LEVEL_ID)) $wh[] = " A.LEVEL_ID = '$LEVEL_ID' ";
if($PROJECT_ID = get_input('PROJECT_ID') AND !empty($PROJECT_ID)) $wh[] = " A.PROJECT_ID = '$PROJECT_ID' ";
if($NAMA = get_input('NAMA') AND !empty($NAMA)) $wh[] = " (UPPER(USERNAME) LIKE UPPER('%$NAMA%')) OR (UPPER(NAMA) LIKE UPPER('%$NAMA%')) ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);
	
$rs = db_first("
	SELECT COUNT(1) as cnt 
	FROM user A 
	LEFT JOIN user_level B ON A.LEVEL_ID=B.LEVEL_ID 
	LEFT JOIN project P ON P.PROJECT_ID=A.PROJECT_ID
	{$where}
");
$NUM_ROWS = $rs->cnt;
	
$rs = db_fetch_limit("
	SELECT *
	FROM user A 
	LEFT JOIN user_level B ON (A.LEVEL_ID=B.LEVEL_ID) 
	LEFT JOIN project P ON (P.PROJECT_ID=A.PROJECT_ID) 
	{$where}
	ORDER BY $SORT $ORDER
	", $PER_PAGE, $OFFSET);

$rows = $t = array();
$key = 0;
if(count($rs)>0){ foreach($rs as $row){
	
	$t['PROJECT'] = empty($row->PROJECT) ? 'All Unit' : $row->PROJECT;
	#$t['TANGGAL'] = tgl($row->TANGGAL);
	#$t['STATUS'] = isset($ST[$row->STATUS]) ? '<div style="font-weight:bold;">'.$ST[$row->STATUS].'</div>' : '';
	$rows[$key] = (object) array_merge((array) $row, $t);
	$key++;
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);