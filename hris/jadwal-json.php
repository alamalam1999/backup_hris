<?php

include 'app-load.php';

is_login('jadwal.view');

set_search('JADWAL', array('sort','order','PERIODE_ID','PROJECT_ID','TGL_MULAI','TGL_SELESAI','VIEW_MODE','NAMA'));
if( get_input('clear') ) clear_search('JADWAL', array('PERIODE_ID','PROJECT_ID','TGL_MULAI','TGL_SELESAI','VIEW_MODE','NAMA'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'NAMA';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$TGL_MULAI = get_input('TGL_MULAI');
$TGL_SELESAI = get_input('TGL_SELESAI');
$VIEW_MODE = get_input('VIEW_MODE');
$RANGE = date_range($TGL_MULAI,$TGL_SELESAI);

$where = '';
$wh = array();
$wh[] = " NIK <> '' ";
if($PROJECT_ID = get_input('PROJECT_ID') AND !empty($PROJECT_ID)) $wh[] = " J.PROJECT_ID = '$PROJECT_ID' ";
if($NAMA = get_input('NAMA') AND !empty($NAMA)) $wh[] = " UPPER(NAMA) LIKE UPPER('%$NAMA%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_fetch("
	SELECT KARYAWAN_ID
	FROM karyawan A
	LEFT JOIN jabatan J ON J.JABATAN_ID=A.JABATAN_ID
	{$where}
");

$KARYAWAN_ID = array();
if(count($rs)>0){
	foreach($rs as $row){
		$KARYAWAN_ID[] = $row->KARYAWAN_ID;
	}
}

if(count($KARYAWAN_ID)>0){
	$rs = db_fetch("
		SELECT *, K.SHIFT_CODE as SHIFT_CODE
		FROM shift_karyawan K
			LEFT JOIN shift S ON S.SHIFT_CODE = K.SHIFT_CODE
		WHERE
			K.PROJECT_ID='$PROJECT_ID' AND
			KARYAWAN_ID IN (".implode(',',$KARYAWAN_ID).") AND
			(DATE >= '$TGL_MULAI' AND DATE <= '$TGL_SELESAI')
	");
	$SHIFT = array();
	if(count($rs)>0){
		foreach($rs as $row){
			$SHIFT[$row->KARYAWAN_ID][$row->DATE] = $row;
		}
	}
}

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM karyawan A
	LEFT JOIN jabatan J ON J.JABATAN_ID=A.JABATAN_ID
	LEFT JOIN posisi P ON P.POSISI_ID=A.POSISI_ID
	{$where}
");
$NUM_ROWS = $rs->cnt;
	
$rs = db_fetch_limit("
	SELECT KARYAWAN_ID,NIK,NAMA,P.POSISI
	FROM karyawan A
	LEFT JOIN jabatan J ON J.JABATAN_ID=A.JABATAN_ID
	LEFT JOIN posisi P ON P.POSISI_ID=A.POSISI_ID
	{$where}
	ORDER BY $SORT $ORDER
", $PER_PAGE, $OFFSET);

$rows = $t = array();
$key = 0;
if(count($rs)>0){ foreach($rs as $row){
	if(count($RANGE)>0){
		foreach($RANGE as $date){
			$SH = isset($SHIFT[$row->KARYAWAN_ID][$date]) ? $SHIFT[$row->KARYAWAN_ID][$date] : '';
			if (get_brightness($SH->SHIFT_COLOR) > 130){
				$font = '#000000';
			}else{
				$font = '#ffffff';
			}
			if($VIEW_MODE=='CODE'){
				$t['TGL_'.date('Ymd',strtotime($date))] = '<div class="tip" title="'.$SH->START_TIME.'-'.$SH->FINISH_TIME.'" style="color:'.$font.';background-color:'.$SH->SHIFT_COLOR.';">'.$SH->SHIFT_CODE.'</div>';
			}else{
				$t['TGL_'.date('Ymd',strtotime($date))] = '<div class="tip" title="'.$SH->SHIFT_CODE.'" style="color:'.$font.';background-color:'.$SH->SHIFT_COLOR.';">'.$SH->START_TIME.'-'.$SH->FINISH_TIME.'</div>';
			}
			if(in_array($SH->SHIFT_CODE,array('X','OFF'))){
				$t['TGL_'.date('Ymd',strtotime($date))] = '<div style="color:#ff0000;text-align:center;">OFF</div>';
			}
		}
	}
	$rows[$key] = (object) array_merge((array) $row, $t);
	$key++;
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);