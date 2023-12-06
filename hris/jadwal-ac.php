<?php

include 'app-load.php';
$q = get_input('q');

$DATA = db_fetch(" SELECT * FROM shift WHERE (UCASE(SHIFT_CODE) LIKE UCASE('$q%')) ORDER BY SHIFT_CODE ASC LIMIT 20 ");

$t = array();
if (count($DATA) > 0) {
	$t[0]['id'] = 'X';
	$t[0]['text'] = '-- LIBUR --';
	foreach ($DATA as $key => $row) {
		$t[$key + 1]['id'] = $row->SHIFT_CODE;
		$t[$key + 1]['text'] = $row->SHIFT_CODE . ' (' . $row->STATUS . ')';
	}
}

$res['results'] = $t;

echo json_encode($res);
