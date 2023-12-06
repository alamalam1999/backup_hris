<?php
include 'app-load.php';

if( ! isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] != 'POST' )
{
	exit();
}

$KARYAWAN_ID = get_input('KARYAWAN_ID');
$TAHUN = get_input('TAHUN');
$KPI_ID = get_input('KPI_ID');

$ex = db_first(" SELECT COUNT(1) as cnt FROM kpi_karyawan WHERE TAHUN='$TAHUN' AND KARYAWAN_ID='$KARYAWAN_ID' AND KPI_ID='$KPI_ID' ");
$EXISTS = isset($ex->cnt) ? $ex->cnt : 0;

if( $EXISTS > 0 )
{
	echo json_encode(array('status' => '0', 'msg' => 'Indicator sudah ada'));
	exit();
}

$kpi = db_first(" SELECT * FROM kpi WHERE KPI_ID='$KPI_ID' ");
$INDICATOR = isset($kpi->INDICATOR) ? $kpi->INDICATOR : '';
$UNIT = isset($kpi->UNIT) ? $kpi->UNIT : '';

db_execute(" INSERT INTO kpi_karyawan (KARYAWAN_ID,KPI_ID,TAHUN,INDICATOR,UNIT) VALUES ('$KARYAWAN_ID','$KPI_ID','$TAHUN','$INDICATOR','$UNIT') ");

if( $DB->Affected_Rows() )
{
	$res = array('status' => '1', 'msg' => 'Saved');
}
else
{
	$res = array('status' => '0', 'msg' => 'Not saved');
}
echo json_encode($res);