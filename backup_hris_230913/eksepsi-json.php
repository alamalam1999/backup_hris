<?php

include 'app-load.php';

is_login('eksepsi.view');

$JENIS = array(
		'SAKIT' => '<div style="background-color:'.get_option('C_SAKIT').';font-weight:bold;color:'.get_option('F_SAKIT').';">SAKIT</div>',
		'IJIN' => '<div style="background-color:'.get_option('C_IJIN').';font-weight:bold;color:'.get_option('F_IJIN').';">IJIN</div>',
		'IJIN_LE' => '<div style="background-color:'.get_option('C_IJIN').';font-weight:bold;color:'.get_option('F_IJIN').';">IJIN LATE/EARLY</div>',
		'SKD' => '<div style="background-color:'.get_option('C_SKD').';font-weight:bold;color:'.get_option('F_SKD').';">SKD</div>',
		'CI' => '<div style="background-color:'.get_option('C_CI').';font-weight:bold;color:'.get_option('F_CI').';">CI</div>',
		'CT' => '<div style="background-color:'.get_option('C_CT').';font-weight:bold;color:'.get_option('F_CT').';">CT</div>',
		/*
		'BACKUP' => '<div style="background-color:' . get_option('C_BACKUP') . ';font-weight:bold;color:' . get_option('F_BACKUP') . ';">BACKUP</div>',
		'TO_IN' => '<div style="background-color:'.get_option('C_TO').';font-weight:bold;color:'.get_option('F_TO').';">TO-IN</div>',
		'TO_OUT' => '<div style="background-color:'.get_option('C_TO').';font-weight:bold;color:'.get_option('F_TO').';">TO-OUT</div>',
		'TS' => '<div style="background-color:'.get_option('C_TS').';font-weight:bold;color:'.get_option('F_TS').';">TS</div>',
		'TS_IN' => '<div style="background-color:'.get_option('C_TS').';font-weight:bold;color:'.get_option('F_TS').';">TS-IN</div>',
		'TS_OUT' => '<div style="background-color:'.get_option('C_TS').';font-weight:bold;color:'.get_option('F_TS').';">TS-OUT</div>',
		*/
		'R' => '<div style="background-color:'.get_option('C_R').';font-weight:bold;color:'.get_option('F_R').';">R</div>',
		'BM' => '<div style="background-color:'.get_option('C_BM').';font-weight:bold;color:'.get_option('F_BM').';">BM</div>',
		'CM' => '<div style="background-color:'.get_option('C_CM').';font-weight:bold;color:'.get_option('F_CM').';">CM</div>',
		'DINAS' => '<div style="background-color:'.get_option('C_DINAS').';font-weight:bold;color:'.get_option('F_DINAS').';">DINAS</div>',
		'UL' => '<div style="background-color:'.get_option('C_UL').';font-weight:bold;color:'.get_option('F_UL').';">UL</div>',
		'SM' => '<div style="background-color:'.get_option('C_SM').';font-weight:bold;color:'.get_option('F_SM').'">SM</div>',
	);
	
$ST = array(
		'PENDING' => '<span style="color:#0000ff;">PENDING</span>',
		'APPROVED' => '<span style="color:#00c000;">APPROVED</span>',
		'VOID' => '<span style="color:#ff0000;">VOID</span>',
	);

set_search('EKSEPSI', array('sort','order','PERIODE_ID','PROJECT_ID','NAMA'));
if( get_input('clear') ) clear_search('EKSEPSI', array('PERIODE_ID','PROJECT_ID','NAMA'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'TGL_MULAI';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'desc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
if($PERIODE_ID = get_input('PERIODE_ID') AND !empty($PERIODE_ID)) $wh[] = " E.PERIODE_ID = '$PERIODE_ID' ";
if($PROJECT_ID = get_input('PROJECT_ID') AND !empty($PROJECT_ID)) $wh[] = " J.PROJECT_ID = '$PROJECT_ID' ";
if($STATUS = get_input('STATUS') AND !empty($STATUS)) $wh[] = " E.STATUS = '$STATUS' ";
if($NAMA = get_input('NAMA') AND !empty($NAMA)) $wh[] = " UPPER(NAMA) LIKE UPPER('%$NAMA%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM eksepsi E 
	LEFT JOIN periode R ON (R.PERIODE_ID=E.PERIODE_ID) 
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=E.KARYAWAN_ID) 
	LEFT JOIN jabatan J ON (J.JABATAN_ID=K.JABATAN_ID)
	{$where}
");
$NUM_ROWS = $rs->cnt;
	
$rs = db_fetch_limit("
	SELECT E.*, R.PERIODE, K.NAMA
	FROM eksepsi E 
	LEFT JOIN periode R ON (R.PERIODE_ID=E.PERIODE_ID) 
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=E.KARYAWAN_ID) 
	LEFT JOIN jabatan J ON (J.JABATAN_ID=K.JABATAN_ID)
	LEFT JOIN posisi P ON (P.POSISI_ID=K.POSISI_ID)
	{$where}
	ORDER BY STATUS DESC, $SORT $ORDER
	", $PER_PAGE, $OFFSET);

$rows = $t = array();
$key = 0;
if(count($rs)>0){ foreach($rs as $row){
	
	$t['TGL_MULAI'] = tgl($row->TGL_MULAI);
	$t['TGL_SELESAI'] = tgl($row->TGL_SELESAI);
	$t['JENIS'] = isset($JENIS[$row->JENIS]) ? $JENIS[$row->JENIS] : '';
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