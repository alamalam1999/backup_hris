<?php

include 'app-load.php';

is_login('lowongan.view');

set_search('LOWONGAN', array('sort','order','LOWONGAN'));
if( get_input('clear') ) clear_search('LOWONGAN', array('LOWONGAN'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'LOWONGAN';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
if($LOWONGAN = get_input('LOWONGAN') AND !empty($LOWONGAN)) $wh[] = " UPPER(LOWONGAN) LIKE UPPER('%$LOWONGAN%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM lowongan
	{$where}
");
$NUM_ROWS = $rs->cnt;
	
$rs = db_fetch_limit("
	SELECT L.*,P.POSISI
	FROM lowongan L
	LEFT JOIN posisi P ON (P.POSISI_ID=L.POSISI_ID)
	{$where}
	ORDER BY $SORT $ORDER
	", $PER_PAGE, $OFFSET);

$rows = $t = array();
$key = 0;
if(count($rs)>0){ foreach($rs as $row){
	$t['TGL_BERAKHIR'] = tgl($row->TGL_BERAKHIR);
	$rows[$key] = (object) array_merge((array) $row, $t);
	$key++;
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);