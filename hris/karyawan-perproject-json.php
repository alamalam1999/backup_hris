<?php

include 'app-load.php';

is_login('laporan-karyawan-perproject.view');
	
set_search('KARYAWAN', array('sort','order','JABATAN_ID','PROJECT_ID','COMPANY_ID','NAMA','ST_KERJA'));
if( get_input('clear') ) clear_search('KARYAWAN', array('JABATAN_ID','PROJECT_ID','COMPANY_ID','NAMA','ST_KERJA'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'NAMA';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
$wh[] = " (A.ST_KERJA != '') ";
if($JABATAN_ID = get_input('JABATAN_ID') AND !empty($JABATAN_ID)) $wh[] = " J.JABATAN_ID = '$JABATAN_ID' ";
if($COMPANY_ID = get_input('COMPANY_ID') AND !empty($COMPANY_ID)) $wh[] = " C.COMPANY_ID = '$COMPANY_ID' ";
if($PROJECT_ID = get_input('PROJECT_ID') AND !empty($PROJECT_ID)) $wh[] = " P.PROJECT_ID = '$PROJECT_ID' ";
if($ST_KERJA = get_input('ST_KERJA') AND !empty($ST_KERJA)) $wh[] = " ST_KERJA = '$ST_KERJA' ";
if($NAMA = get_input('NAMA') AND !empty($NAMA)) $wh[] = " UPPER(NAMA) LIKE UPPER('%$NAMA%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM karyawan A
	LEFT JOIN jabatan J ON (J.JABATAN_ID=A.JABATAN_ID)
	LEFT JOIN project P ON (P.PROJECT_ID=J.PROJECT_ID)
	LEFT JOIN company C ON (C.COMPANY_ID=A.COMPANY_ID)
	{$where}
");
$NUM_ROWS = $rs->cnt;
	
$rs = db_fetch_limit("
	SELECT A.*, J.JABATAN, C.COMPANY, P.PROJECT
	FROM karyawan A
	LEFT JOIN jabatan J ON (J.JABATAN_ID=A.JABATAN_ID)
	LEFT JOIN project P ON (P.PROJECT_ID=J.PROJECT_ID)
	LEFT JOIN company C ON (C.COMPANY_ID=A.COMPANY_ID)
	{$where}
	ORDER BY $SORT $ORDER
	", $PER_PAGE, $OFFSET);

$rows = $t = array();
$key = 0;
if(count($rs)>0){ foreach($rs as $row){
	
	$t['GAJI_POKOK'] = currency($row->GAJI_POKOK);
	$t['TGL_MASUK'] = tgl($row->TGL_MASUK);
	$rows[$key] = (object) array_merge((array) $row, $t);
	$key++;
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);