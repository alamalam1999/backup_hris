<?php

include 'app-load.php';

$q = get_input('q');
$DATA = db_fetch(" SELECT * FROM posisi WHERE (UCASE(POSISI) LIKE UCASE('$q%')) ORDER BY POSISI ASC LIMIT 10 ");

$t = array();
if(count($DATA)>0)
{
	foreach($DATA as $key => $row){
		$t[$key]['id'] = $row->POSISI_ID;
		$t[$key]['text'] = $row->POSISI;
	}
}

$res['results'] = $t;
echo json_encode($res);