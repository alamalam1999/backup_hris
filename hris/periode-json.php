<?php

include 'app-load.php';

is_login('periode.view');

$CHECK = array(
		'1' => '<span style="font-size:14px;color:#00cf00;"><i class="fa fa-check"></i></span>',
		'0' => '',
	);
$ST = array(
		'OPEN' => '<span style="color:#00cf00;">OPEN</span>',
		'CLOSED' => '<span style="color:#ff0000;">CLOSED</span>',
	);
	
set_search('PERIODE', array('sort','order','NAMA'));
if( get_input('clear') ) clear_search('PERIODE', array('NAMA'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'TANGGAL_MULAI';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'desc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
if($NAMA = get_input('NAMA') AND !empty($NAMA)) $wh[] = " UPPER(PERIODE) LIKE UPPER('%$NAMA%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM periode A
	{$where}
");
$NUM_ROWS = $rs->cnt;
	
$rs = db_fetch_limit("
	SELECT *
	FROM periode A 
	{$where}
	ORDER BY $SORT $ORDER
	", $PER_PAGE, $OFFSET);

$BULAN = array();
$BLN = array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
foreach( $BLN as $key => $bln ){
	$i = $key + 1;
	$BULAN[$i] = isset($BLN[$key]) ? strtoupper($BLN[$key]) : '';
}

$rows = $t = array();
$key = 0;
if(count($rs)>0){ foreach($rs as $row){
	
	$t['BULAN'] = isset($BULAN[intval($row->BULAN)]) ? $BULAN[intval($row->BULAN)] : '';
	$t['TANGGAL_MULAI'] = tgl($row->TANGGAL_MULAI);
	$t['TANGGAL_SELESAI'] = tgl($row->TANGGAL_SELESAI);
	$t['TANGGAL_MULAI2'] = tgl($row->TANGGAL_MULAI2);
	$t['TANGGAL_SELESAI2'] = tgl($row->TANGGAL_SELESAI2);
	$t['TGL_IDUL_FITRI'] = tgl($row->TGL_IDUL_FITRI);
	$t['TGL_KUNINGAN'] = tgl($row->TGL_KUNINGAN);
	$t['THR_IDUL_FITRI'] = isset($CHECK[$row->THR_IDUL_FITRI]) ? $CHECK[$row->THR_IDUL_FITRI] : '';
	$t['THR_KUNINGAN'] = isset($CHECK[$row->THR_KUNINGAN]) ? $CHECK[$row->THR_KUNINGAN] : '';
	$t['STATUS_PERIODE'] = isset($ST[$row->STATUS_PERIODE]) ? '<div style="font-weight:bold;">'.$ST[$row->STATUS_PERIODE].'</div>' : '';
	$rows[$key] = (object) array_merge((array) $row, $t);
	$key++;
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);