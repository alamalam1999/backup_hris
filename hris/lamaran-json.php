<?php

include 'app-load.php';

is_login('lamaran.view');

set_search('LAMARAN', array('sort','order','NAMA','JK','POSISI_ID','LOWONGAN_ID','USIA'));
if( get_input('clear') ) clear_search('LAMARAN', array('NAMA','JK','POSISI_ID','LOWONGAN_ID','USIA'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'APPLICATION_NO';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
$wh[] = " STATUS_LAMARAN != 'PANGGILAN INTERVIEW' ";
$wh[] = " KEPUTUSAN = '' ";
if($NAMA = get_input('NAMA') AND !empty($NAMA)) $wh[] = " UPPER(NAMA) LIKE UPPER('%$NAMA%') ";
if($JK = get_input('JK') AND !empty($JK)) $wh[] = " L.JK = '$JK' ";
if($POSISI_ID = get_input('POSISI_ID') AND !empty($POSISI_ID)) $wh[] = " L.POSISI_ID = '$POSISI_ID' ";
if($LOWONGAN_ID = get_input('LOWONGAN_ID') AND !empty($LOWONGAN_ID)) $wh[] = " L.LOWONGAN_ID = '$LOWONGAN_ID' ";
if($USIA = get_input('USIA') AND !empty($USIA)){
	$TH = intval(date('Y'))-get_input('USIA');
	$wh[] = " YEAR(TGL_LAHIR) >= $TH ";
}
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM lamaran L
	LEFT JOIN lowongan LO ON LO.LOWONGAN_ID=L.LOWONGAN_ID
	LEFT JOIN posisi P ON P.POSISI_ID=L.POSISI_ID
	LEFT JOIN calon_karyawan K ON K.CALON_KARYAWAN_ID=L.CALON_KARYAWAN_ID
	{$where}
");
$NUM_ROWS = $rs->cnt;
	
$rs = db_fetch_limit("
	SELECT L.*,K.NAMA,K.JK,K.CALON_KARYAWAN_ID,LO.LOWONGAN,LO.LOWONGAN_ID,P.POSISI 
	FROM lamaran L
	LEFT JOIN lowongan LO ON LO.LOWONGAN_ID=L.LOWONGAN_ID
	LEFT JOIN posisi P ON P.POSISI_ID=L.POSISI_ID
	LEFT JOIN calon_karyawan K ON K.CALON_KARYAWAN_ID=L.CALON_KARYAWAN_ID
	{$where}
	ORDER BY $SORT $ORDER
	", $PER_PAGE, $OFFSET);

$STATUS = array(
	'PENGAJUAN'				=> '<span class="text-info"><b>PENGAJUAN</b></span>',
	'PANGGILAN INTERVIEW'	=> '<span class="text-primary"><b>PANGGILAN INTERVIEW</b></span>',
	'POSISI LAIN'			=> '<span class="text-warning"><b>POSISI LAIN</b></span>',
);
$rows = $t = array();
$key = 0;
if(count($rs)>0){ foreach($rs as $row){
	$t['STATUS_LAMARAN'] = $STATUS[$row->STATUS_LAMARAN];
	$rows[$key] = (object) array_merge((array) $row, $t);
	$key++;
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);