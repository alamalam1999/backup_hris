<?php

include 'app-load.php';

$q = get_input('q');
$DATA = db_fetch(" SELECT * FROM provinsi WHERE (UCASE(PROVINSI) LIKE UCASE('$q%')) ORDER BY PROVINSI ASC LIMIT 10 ");

$t = array();
if(count($DATA)>0)
{
	foreach($DATA as $key => $row){
		$t[$key]['id'] = $row->PROVINSI;
		$t[$key]['text'] = $row->PROVINSI;
		$t[$key]['kode'] = $row->PROVINSI_ID;
	}
}

$res['results'] = $t;
echo json_encode($res);