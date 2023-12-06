<?php

include 'app-load.php';

$q = get_input('q');

$DATA = db_fetch(" SELECT * FROM shift WHERE UCASE(SHIFT_CODE) LIKE UCASE('%$q%') ");

$t = array();
if(count($DATA)>0)
{
	foreach($DATA as $key => $row){
		$t[$key]['id'] = $row->SHIFT_CODE;
		$t[$key]['text'] = $row->SHIFT_CODE . ' (' . $row->START_TIME . '-' . $row->FINISH_TIME . ')';
	}
}

$res['results'] = $t;

echo json_encode($res);
