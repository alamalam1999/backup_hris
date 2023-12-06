<?php

include 'app-load.php';

$q = get_input('q');
$PROJECT_ID = get_input('project_id');

$PAGE = get_input('page_limit');

$DATA = db_fetch(" SELECT KARYAWAN_ID,NAMA,NIK FROM karyawan WHERE PROJECT_ID='$PROJECT_ID' AND ((UCASE(NAMA) LIKE UCASE('$q%')) OR (UCASE(NIK) LIKE UCASE('$q%'))) ORDER BY NAMA ASC LIMIT $PAGE ");

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
