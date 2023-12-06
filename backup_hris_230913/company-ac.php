<?php

include 'app-load.php';

$q = get_input('q');
$DATA = db_fetch(" SELECT * FROM company WHERE (UCASE(COMPANY) LIKE UCASE('$q%')) ORDER BY COMPANY ASC LIMIT 10 ");

$t = array();
if(count($DATA)>0)
{
	foreach($DATA as $key => $row){
		$t[$key]['id'] = $row->COMPANY_ID;
		$t[$key]['text'] = $row->COMPANY;
	}
}

$res['results'] = $t;
echo json_encode($res);