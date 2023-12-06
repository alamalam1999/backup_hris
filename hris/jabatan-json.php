<?php

include 'app-load.php';

is_login('jabatan.view');

$CHECK = array(
		'1' => '<span style="font-size:14px;color:#00cf00;"><i class="fa fa-check"></i></span>',
		'0' => '',
	);
	
set_search('JABATAN', array('sort','order','COMPANY_ID','PROJECT_ID','JABATAN'));
if( get_input('clear') ) clear_search('JABATAN', array('COMPANY_ID','PROJECT_ID','JABATAN'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'JABATAN';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
if($COMPANY_ID = get_input('COMPANY_ID') AND !empty($COMPANY_ID)) $wh[] = " C.COMPANY_ID = '$COMPANY_ID' ";
if($PROJECT_ID = get_input('PROJECT_ID') AND !empty($PROJECT_ID)) $wh[] = " P.PROJECT_ID = '$PROJECT_ID' ";
if($JABATAN = get_input('JABATAN') AND !empty($JABATAN)) $wh[] = " UPPER(JABATAN) LIKE UPPER('%$JABATAN%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM jabatan A
	LEFT JOIN project P ON P.PROJECT_ID=A.PROJECT_ID
	LEFT JOIN company C ON C.COMPANY_ID=P.COMPANY_ID
	{$where}
");
$NUM_ROWS = $rs->cnt;
	
$rs = db_fetch_limit("
	SELECT *
	FROM jabatan A
	LEFT JOIN project P ON P.PROJECT_ID=A.PROJECT_ID
	LEFT JOIN company C ON C.COMPANY_ID=P.COMPANY_ID
	{$where}
	ORDER BY $SORT $ORDER
	", $PER_PAGE, $OFFSET);

$rows = $t = array();
$key = 0;
if(count($rs)>0){ foreach($rs as $row){
	
	$t['GP'] = isset($CHECK[$row->R_GAJI_POKOK]) ? $CHECK[$row->R_GAJI_POKOK] : '';
	$t['LM'] = isset($CHECK[$row->R_LEMBUR]) ? $CHECK[$row->R_LEMBUR] : '';
	$t['CT'] = isset($CHECK[$row->R_CUTI]) ? $CHECK[$row->R_CUTI] : '';
	$t['JHT'] = isset($CHECK[$row->R_BPJS_JHT]) ? $CHECK[$row->R_BPJS_JHT] : '';
	$t['JP'] = isset($CHECK[$row->R_BPJS_JP]) ? $CHECK[$row->R_BPJS_JP] : '';
	$t['KES'] = isset($CHECK[$row->R_BPJS_KES]) ? $CHECK[$row->R_BPJS_KES] : '';
	$t['MED'] = isset($CHECK[$row->R_MEDICAL_CASH]) ? $CHECK[$row->R_MEDICAL_CASH] : '';
	$t['BAC'] = isset($CHECK[$row->R_TUNJ_BACKUP]) ? $CHECK[$row->R_TUNJ_BACKUP] : '';
	$t['TRN'] = isset($CHECK[$row->R_TUNJ_TRANSPORT]) ? $CHECK[$row->R_TUNJ_TRANSPORT] : '';
	$t['MKN'] = isset($CHECK[$row->R_TUNJ_MAKAN]) ? $CHECK[$row->R_TUNJ_MAKAN] : '';
	$t['AHLI'] = isset($CHECK[$row->R_TUNJ_KEAHLIAN]) ? $CHECK[$row->R_TUNJ_KEAHLIAN] : '';
	$t['KMN'] = isset($CHECK[$row->R_TUNJ_KOMUNIKASI]) ? $CHECK[$row->R_TUNJ_KOMUNIKASI] : '';
	$t['JAB'] = isset($CHECK[$row->R_TUNJ_JABATAN]) ? $CHECK[$row->R_TUNJ_JABATAN] : '';
	$t['PENG'] = isset($CHECK[$row->R_TUNJ_KEHADIRAN]) ? $CHECK[$row->R_TUNJ_KEHADIRAN] : '';
	$t['PROY'] = isset($CHECK[$row->R_TUNJ_PROYEK]) ? $CHECK[$row->R_TUNJ_PROYEK] : '';
	$t['SHFT'] = isset($CHECK[$row->R_TUNJ_SHIFT]) ? $CHECK[$row->R_TUNJ_SHIFT] : '';
	$t['THR_PRO'] = isset($CHECK[$row->R_THR_PRORATA]) ? $CHECK[$row->R_THR_PRORATA] : '';
	$t['THR_FULL'] = isset($CHECK[$row->R_THR]) ? $CHECK[$row->R_THR] : '';
	
	$rows[$key] = (object) array_merge((array) $row, $t);
	$key++;
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);