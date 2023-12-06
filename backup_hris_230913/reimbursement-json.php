<?php

include 'app-load.php';

is_login('reimbursement.view');
	
$ST = array(
		'PENDING' => '<span style="color:#0000ff;">PENDING</span>',
		'APPROVED' => '<span style="color:#00c000;">APPROVED</span>',
		'VOID' => '<span style="color:#ff0000;">VOID</span>',
	);

set_search('REIMBURSEMENT', array('sort','order','PERIODE_ID','PROJECT_ID','NAMA'));
if( get_input('clear') ) clear_search('REIMBURSEMENT', array('PERIODE_ID','PROJECT_ID','NAMA'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'TANGGAL';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'desc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
if($PERIODE_ID = get_input('PERIODE_ID') AND !empty($PERIODE_ID)) $wh[] = " E.PERIODE_ID = '$PERIODE_ID' ";
if($PROJECT_ID = get_input('PROJECT_ID') AND !empty($PROJECT_ID)) $wh[] = " E.PROJECT_ID = '$PROJECT_ID' ";
if($NAMA = get_input('NAMA') AND !empty($NAMA)) $wh[] = " UPPER(NAMA) LIKE UPPER('%$NAMA%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM reimbursement E 
	LEFT JOIN periode R ON (R.PERIODE_ID=E.PERIODE_ID) 
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=E.KARYAWAN_ID)
	{$where}
");
$NUM_ROWS = $rs->cnt;
	
$rs = db_fetch_limit("
	SELECT E.*, R.PERIODE, K.NAMA, K.NIK
	FROM reimbursement E 
	LEFT JOIN periode R ON (R.PERIODE_ID=E.PERIODE_ID) 
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=E.KARYAWAN_ID)
	{$where}
	ORDER BY $SORT $ORDER
	", $PER_PAGE, $OFFSET);

$rows = $t = array();
$key = 0;
if(count($rs)>0){ foreach($rs as $row){
	
	$t['TOTAL'] = currency($row->TOTAL);
	$t['TANGGAL'] = tgl($row->TANGGAL);
	$t['STATUS'] = isset($ST[$row->STATUS]) ? '<div style="font-weight:bold;">'.$ST[$row->STATUS].'</div>' : '';
	$t['FILE'] = empty($row->FILE) ? '' : '<a href="'.base_url().'uploads/skd/'.$row->FILE.'" target="_blank">view</a>';
	$t['STATUS_KEY'] = $row->STATUS;
	
	$t['CREATED_ON'] = tgl($row->CREATED_ON,1);
	$t['UPDATED_ON'] = tgl($row->UPDATED_ON,1);
	
	$rows[$key] = (object) array_merge((array) $row, $t);
	$key++;
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);