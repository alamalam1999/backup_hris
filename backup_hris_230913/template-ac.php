<?php

include 'app-load.php';

$q = get_input('q');
$DATA = db_fetch(" SELECT * FROM template T WHERE (UCASE(TEMPLATE) LIKE UCASE('$q%')) ORDER BY TEMPLATE ASC LIMIT 10 ");

$t = array();
if(count($DATA)>0)
{
	foreach($DATA as $key => $row){
		$t[$key]['id'] = $row->TEMPLATE_ID;
		$t[$key]['text'] = $row->TEMPLATE;
	}
}

$res['results'] = $t;
echo json_encode($res);