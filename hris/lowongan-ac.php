<?php

include 'app-load.php';

$q = get_input('q');
$POSISI_ID = get_input('posisi_id');
$PAGE = get_input('page_limit');

$DATA = db_fetch(" SELECT * FROM lowongan WHERE POSISI_ID='$POSISI_ID' AND (UCASE(LOWONGAN) LIKE UCASE('$q%')) ORDER BY TGL_BERAKHIR ASC LIMIT $PAGE ");

$t = array();
if(count($DATA)>0)
{
	foreach($DATA as $key => $row){
		$t[$key]['id'] = $row->LOWONGAN_ID;
		$t[$key]['text'] = $row->LOWONGAN.' ('.$row->KODE_REFERENSI.')';
		$t[$key]['kode'] = $row->KODE_REFERENSI;
	}
}

$res['results'] = $t;
echo json_encode($res);
