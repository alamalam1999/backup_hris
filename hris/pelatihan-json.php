<?php

include 'app-load.php';

is_login('pelatihan.view');

set_search('PELATIHAN', array('sort','order','NAMA','STATUS'));
if( get_input('clear') ) clear_search('PELATIHAN', array('NAMA','STATUS'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'INDICATOR';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
if($STATUS = get_input('STATUS') AND !empty($STATUS)) $wh[] = " STATUS = '$STATUS' ";
if($NAMA = get_input('NAMA') AND !empty($NAMA)) $wh[] = " UPPER(INDICATOR) LIKE UPPER('%$NAMA%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM pelatihan
	{$where}
");
$NUM_ROWS = $rs->cnt;
	
$rs = db_fetch_limit("
	SELECT *
	FROM pelatihan
	{$where}
	ORDER BY $SORT $ORDER
	", $PER_PAGE, $OFFSET);

$STATUS = array(
	'PENGAJUAN' => '<span style="color:#0000ff;">PENGAJUAN</span>',
	'TIDAK DISETUJUI' => '<span style="color:#ff0000;">TIDAK DISETUJUI</span>',
	'DISETUJUI' => '<span style="color:#00fc00;">DISETUJUI</span>',
	'TERLAKSANA' => '<span style="color:#fcc000;">TERLAKSANA</span>',
);

$rows = $t = array();
$key = 0;
if(count($rs)>0){ foreach($rs as $row){
	
	$t['TGL_MULAI'] = cdate($row->TGL_MULAI);
	$t['TGL_SELESAI'] = cdate($row->TGL_SELESAI);
	$t['STATUS'] = isset($STATUS[$row->STATUS]) ? $STATUS[$row->STATUS] : '';
	
	$rows[$key] = (object) array_merge((array) $row, $t);
	$key++;
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);