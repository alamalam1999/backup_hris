<?php

include 'app-load.php';

$q = get_input('q');
$DATA = db_fetch(" SELECT * FROM kategori_keahlian WHERE (UCASE(KATEGORI_KEAHLIAN) LIKE UCASE('$q%')) ORDER BY KATEGORI_KEAHLIAN ASC LIMIT 10 ");

$t = array();
if(count($DATA)>0)
{
	foreach($DATA as $key => $row){
		$t[$key]['id'] = $row->KATEGORI_KEAHLIAN_ID;
		$t[$key]['text'] = $row->KATEGORI_KEAHLIAN;
	}
}

$res['results'] = $t;
echo json_encode($res);