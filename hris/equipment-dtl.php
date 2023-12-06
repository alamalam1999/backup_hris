<?php

include 'app-load.php';

$q = get_input('q');
$COMPANY_ID = get_input('company_id');
$PAGE = get_input('page_limit');

//$DATA = db_fetch(" SELECT * FROM equipment WHERE COMPANY_ID='$COMPANY_ID' AND (UCASE(NAMA) LIKE UCASE('$q%')) ORDER BY NAMA ASC LIMIT $PAGE ");
$DATA = db_fetch(" SELECT * FROM equipment WHERE (UCASE(NAMA) LIKE UCASE('$q%')) ORDER BY NAMA ASC LIMIT $PAGE ");
//echo "SELECT KARYAWAN_ID,NAMA,NIK FROM karyawan WHERE COMPANY_ID='$COMPANY_ID' AND ((UCASE(NAMA) LIKE UCASE('$q%')) OR (UCASE(NIK) LIKE UCASE('$q%'))) ORDER BY NAMA ASC LIMIT $PAGE";

$t = array();
if(count($DATA)>0)
{
	foreach($DATA as $key => $row){
		$t[$key]['id'] = $row->EQUIPMENT_ID;
		$t[$key]['text'] = $row->NAMA;
	}
}

$res['results'] = $t;
echo json_encode($res);
