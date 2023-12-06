<?php
include 'app-load.php';

if( ! isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] != 'POST' )
{
	exit();
}

$KPI_KARYAWAN_ID = get_input('KPI_KARYAWAN_ID');

db_execute(" DELETE FROM kpi_karyawan WHERE KPI_KARYAWAN_ID='$KPI_KARYAWAN_ID' ");

if( $DB->Affected_Rows() )
{
	$res = array('status' => '1', 'msg' => 'Deleted');
}
else
{
	$res = array('status' => '0', 'msg' => 'Not delete');
}
echo json_encode($res);