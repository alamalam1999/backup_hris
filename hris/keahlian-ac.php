<?php

include 'app-load.php';

$q = get_input('q');
$KATEGORI_KEAHLIAN_ID = get_input('kategori_keahlian_id');

$PAGE = get_input('page_limit');

$DATA = db_fetch(" SELECT * FROM keahlian WHERE KATEGORI_KEAHLIAN_ID='$KATEGORI_KEAHLIAN_ID' AND (UCASE(KEAHLIAN) LIKE UCASE('$q%')) ORDER BY KEAHLIAN ASC LIMIT $PAGE ");

$t = array();
if(count($DATA)>0)
{
	foreach($DATA as $key => $row){
		$t[$key]['id'] = $row->KEAHLIAN_ID;
		$t[$key]['text'] = $row->KEAHLIAN;
	}
}

$res['results'] = $t;
echo json_encode($res);
