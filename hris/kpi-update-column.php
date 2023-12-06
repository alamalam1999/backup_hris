<?php
include 'app-load.php';

if( ! isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] != 'POST' )
{
	exit();
}

$COL = get_input('COL');
$ID = get_input('ID');
$VAL = get_input('VAL');

db_execute(" UPDATE kpi_karyawan SET $COL='$VAL' WHERE KPI_KARYAWAN_ID='$ID' ");

if( $DB->Affected_Rows() )
{
	$res = array('status' => '1', 'msg' => 'Updated');
}
else
{
	$res = array('status' => '0', 'msg' => 'Not update');
}
echo json_encode($res);