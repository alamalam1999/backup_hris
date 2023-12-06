<?php
include 'app-load.php';

if( ! isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] != 'POST' )
{
	exit();
}

$KPI_KARYAWAN_ID = get_input('ID');

$row = db_first(" SELECT TARGET,REALISASI FROM kpi_karyawan WHERE KPI_KARYAWAN_ID='$KPI_KARYAWAN_ID' ");
$TARGET = isset($row->TARGET) ? $row->TARGET : '';
$REALISASI = isset($row->REALISASI) ? $row->REALISASI : '';

$KPI = round($REALISASI/$TARGET,2) * 100;

$res = array('KPI' => $KPI.' %');

echo json_encode($res);