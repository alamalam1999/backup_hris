<?php

include 'app-load.php';

is_login('lembur.view');

$JENIS = array(
		'LHK' => '<span style="color:#0000ff;font-weight:bold;">LHK</span>',
		'LHL' => '<span style="color:#ff00ff;font-weight:bold;">LHL</span>',
		'IHB' => '<span style="color:#ff0000;font-weight:bold;">IHB</span>',
	);
	
$ST = array(
		'PENDING' => '<span style="color:#0000ff;">PENDING</span>',
		'APPROVED' => '<span style="color:#00c000;">APPROVED</span>',
		'VOID' => '<span style="color:#ff0000;">VOID</span>',
	);

set_search('LEMBUR', array('sort','order','PERIODE_ID','PROJECT_ID','NAMA'));
if( get_input('clear') ) clear_search('LEMBUR', array('PERIODE_ID','PROJECT_ID','NAMA'));

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
if($PROJECT_ID = get_input('PROJECT_ID') AND !empty($PROJECT_ID)) $wh[] = " J.PROJECT_ID = '$PROJECT_ID' ";
if($NAMA = get_input('NAMA') AND !empty($NAMA)) $wh[] = " UPPER(NAMA) LIKE UPPER('%$NAMA%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM lembur E 
	LEFT JOIN periode R ON (R.PERIODE_ID=E.PERIODE_ID) 
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=E.KARYAWAN_ID) 
	LEFT JOIN jabatan J ON (J.JABATAN_ID=K.JABATAN_ID)
	{$where}
");
$NUM_ROWS = $rs->cnt;
	
$rs = db_fetch_limit("
	SELECT E.*, R.PERIODE, K.NIK, K.NAMA, K.GAJI_POKOK
	FROM lembur E 
	LEFT JOIN periode R ON (R.PERIODE_ID=E.PERIODE_ID) 
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=E.KARYAWAN_ID) 
	LEFT JOIN jabatan J ON (J.JABATAN_ID=K.JABATAN_ID)
	{$where}
	ORDER BY $SORT $ORDER
	", $PER_PAGE, $OFFSET);

$rows = $t = array();
$key = 0;
if(count($rs)>0){ foreach($rs as $row){
	if($row->JENIS=='LHK'){
		$GAJI_PERJAM = round((1/173)*$row->GAJI_POKOK,0);
		$LEMBUR = hitung_lembur_LHK($row->TOTAL_JAM,$row->GAJI_POKOK,$row->ADJUSMENT);
	}
	if($row->JENIS=='LHL'){
		$GAJI_PERJAM = round((1/173)*$row->GAJI_POKOK,0);
		$LEMBUR = hitung_lembur_LHL($row->TOTAL_JAM,$row->GAJI_POKOK,$row->ADJUSMENT);
	}
	if($row->JENIS=='IHB'){
		$GAJI_PERJAM = round((1/173)*$row->GAJI_POKOK,0);
		$LEMBUR = hitung_lembur_IHB($row->TOTAL_JAM,$row->GAJI_POKOK,$row->ADJUSMENT);
	}
	
	$t['UANG_LEMBUR'] = currency($row->UANG_LEMBUR);
	$t['ADJ'] = $LEMBUR['ADJ'];
	$t['PENGALI1'] = $LEMBUR['PENGALI1'];
	$t['PENGALI2'] = $LEMBUR['PENGALI2'];
	$t['POINT1'] = $LEMBUR['POINT1'];
	$t['POINT2'] = $LEMBUR['POINT2'];
	$t['TOTAL_POINT'] = $LEMBUR['TOTAL_POINT'];
	$t['GAJI_PERJAM'] = currency($GAJI_PERJAM);
	$t['TOTAL'] = currency($LEMBUR['TOTAL']);
	$t['TANGGAL'] = tgl($row->TANGGAL);
	$t['JENIS'] = isset($JENIS[$row->JENIS]) ? '<div style="font-weight:bold;">'.$JENIS[$row->JENIS].'</div>' : '';
	$t['STATUS'] = isset($ST[$row->STATUS]) ? '<div style="font-weight:bold;">'.$ST[$row->STATUS].'</div>' : '';
	$t['STATUS_KEY'] = $row->STATUS;
	$t['FILE'] = empty($row->FILE) ? '' : '<a href="'.base_url().'uploads/skd/'.$row->FILE.'" target="_blank">view</a>';
	$t['APPROVED_ON'] = tgl($row->APPROVED_ON,1);
	$t['CREATED_ON'] = tgl($row->CREATED_ON,1);
	$t['UPDATED_ON'] = tgl($row->UPDATED_ON,1);
	
	$rows[$key] = (object) array_merge((array) $row, $t);
	$key++;
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);