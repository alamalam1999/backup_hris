<?php

include 'app-load.php';

is_login('kpi-karyawan.view');

set_search('KPI_KARYAWAN', array('sort','order','KARYAWAN_ID','TAHUN'));
if( get_input('clear') ) clear_search('KPI_KARYAWAN', array('KARYAWAN_ID','TAHUN'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'A.INDICATOR';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
$wh[] = " A.KARYAWAN_ID = '".db_escape(get_input('KARYAWAN_ID'))."' ";
$wh[] = " A.TAHUN = '".db_escape(get_input('TAHUN'))."' ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM kpi_karyawan A
	LEFT JOIN kpi B ON (A.KPI_ID=B.KPI_ID)
	{$where}
");
$NUM_ROWS = $rs->cnt;
	
$rs = db_fetch_limit("
	SELECT *
	FROM kpi_karyawan A
	LEFT JOIN kpi B ON (A.KPI_ID=B.KPI_ID)
	{$where}
	ORDER BY $SORT $ORDER
	", $PER_PAGE, $OFFSET);

$rows = $t = array();
$key = 0;
if(count($rs)>0){ foreach($rs as $row){
	$t['TARGET'] = '<div class="UPDATE_COL" data-col="TARGET" data-id="'.$row->KPI_KARYAWAN_ID.'" data-val="'.$row->TARGET.'">'.$row->TARGET.'</div>';
	$t['REALISASI'] = '<div class="UPDATE_COL" data-col="REALISASI" data-id="'.$row->KPI_KARYAWAN_ID.'" data-val="'.$row->REALISASI.'">'.$row->REALISASI.'</div>';
	$KPI = round($row->REALISASI/$row->TARGET,2) * 100;
	$t['KPI'] = '<span class="KPI_'.$row->KPI_KARYAWAN_ID.'">'.$KPI.' %</span>';
	$rows[$key] = (object) array_merge((array) $row, $t);
	$key++;
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);