<?php

include 'app-load.php';

$q = get_input('q');
$PROVINSI_ID = get_input('provinsi_id');

$PAGE = get_input('page_limit');

$DATA = db_fetch(" SELECT * FROM kota WHERE PROVINSI_ID='$PROVINSI_ID' AND (UCASE(KOTA) LIKE UCASE('$q%')) ORDER BY KOTA ASC LIMIT $PAGE ");

$t = array();
if(count($DATA)>0)
{
	foreach($DATA as $key => $row){
		$t[$key]['id'] = $row->KOTA;
		$t[$key]['text'] = $row->KOTA;
	}
}

$res['results'] = $t;
echo json_encode($res);
