<?php

include 'app-load.php';

$CU = current_user();
$PROJECT_ID = isset($CU->PROJECT_ID) ? $CU->PROJECT_ID : '';

$where = '';
if( ! empty($PROJECT_ID))
{
	$where = " PROJECT_ID='$PROJECT_ID' AND ST_KERJA='AKTIF' AND ";
}

$q = get_input('q');

$DATA = db_fetch(" SELECT * FROM karyawan WHERE $where (UCASE(NAMA) LIKE UCASE('$q%')) OR (UCASE(NIK) LIKE UCASE('$q%')) ORDER BY NAMA ASC LIMIT 10 ");

$t = array();
if(count($DATA)>0)
{
	foreach($DATA as $key => $row){
		$t[$key]['id'] = $row->KARYAWAN_ID;
		$t[$key]['text'] = $row->NIK . ' - ' . $row->NAMA;
	}
}

$res['results'] = $t;

echo json_encode($res);
