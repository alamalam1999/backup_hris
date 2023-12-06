<?php

include 'app-load.php';

is_login('pinjaman.view');
	
$ST = array(
		'PENDING' => '<span style="color:#0000ff;">PENDING</span>',
		'APPROVED' => '<span style="color:#00c000;">APPROVED</span>',
		'VOID' => '<span style="color:#ff0000;">VOID</span>',
	);

set_search('PINJAMAN', array('sort','order','PROJECT_ID','NAMA'));
if( get_input('clear') ) clear_search('PINJAMAN', array('PROJECT_ID','NAMA'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'NIK';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
if($PROJECT_ID = get_input('PROJECT_ID') AND !empty($PROJECT_ID)) $wh[] = " E.PROJECT_ID = '$PROJECT_ID' ";
if($NAMA = get_input('NAMA') AND !empty($NAMA)) $wh[] = " UPPER(NAMA) LIKE UPPER('%$NAMA%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM pinjaman_master E 
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=E.KARYAWAN_ID)
	{$where}
");
$NUM_ROWS = $rs->cnt;

$rs_periode = db_fetch(" SELECT PERIODE_ID,PERIODE FROM periode ");
$PERIODE = array();
if(count($rs_periode)>0){
	foreach($rs_periode as $row){
		$PERIODE[$row->PERIODE_ID] = $row->PERIODE;
	}
}

	
$rs = db_fetch_limit("
	SELECT E.*, K.NAMA, K.NIK
	FROM pinjaman_master E 
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=E.KARYAWAN_ID)
	{$where}
	ORDER BY $SORT $ORDER
	", $PER_PAGE, $OFFSET);

$rows = $t = array();
$key = 0;
if(count($rs)>0){ foreach($rs as $row){
	
	$t['GRAND_TOTAL'] = currency($row->GRAND_TOTAL);
	$t['ANGSURAN'] = currency($row->ANGSURAN);
	$t['STATUS'] = isset($ST[$row->STATUS]) ? '<div style="font-weight:bold;">'.$ST[$row->STATUS].'</div>' : '';
	$t['STATUS_KEY'] = $row->STATUS;
	$t['JENIS_POTONGAN'] = $row->JENIS_POTONGAN;
	$t['PERIODE_MULAI'] = isset($PERIODE[$row->PERIODE_MULAI]) ? $PERIODE[$row->PERIODE_MULAI] : '';
	$t['PERIODE_SELESAI'] = isset($PERIODE[$row->PERIODE_SELESAI]) ? $PERIODE[$row->PERIODE_SELESAI] : '';
	
	$t['CREATED_ON'] = tgl($row->CREATED_ON,1);
	$t['UPDATED_ON'] = tgl($row->UPDATED_ON,1);
	
	$rows[$key] = (object) array_merge((array) $row, $t);
	$key++;
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);