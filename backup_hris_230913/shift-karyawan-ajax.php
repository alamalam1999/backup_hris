<?php

include 'app-load.php';

$KARYAWAN_ID = db_escape(get_input('KARYAWAN_ID'));
$TANGGAL = db_escape(get_input('TANGGAL'));
$where = '';
$wh = array();
$wh[] = " KARYAWAN_ID = '$KARYAWAN_ID' ";
$wh[] = " DATE = '$TANGGAL' ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_first("
	SELECT START_TIME,FINISH_TIME
	FROM shift_karyawan K
	LEFT JOIN shift S ON S.SHIFT_CODE=K.SHIFT_CODE
	{$where}
");

echo json_encode($rs);
