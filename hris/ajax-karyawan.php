<?php
include 'app-load.php';

$q = get_input('q');
$DATA = db_fetch(" SELECT * FROM karyawan WHERE NIK <> '' AND UCASE(NAMA) LIKE UCASE('$q%') ");

$t = array();
if(count($DATA)>0)
{
	foreach($DATA as $key => $row){
		$t[$key]['id'] = $row->KARYAWAN_ID;
		$t[$key]['text'] = $row->NAMA;
	}
}

$res['results'] = $t;

echo json_encode($res);