<?php

include 'app-load.php';

$q = get_input('q');
$COMPANY_ID = get_input('company_id');

$PAGE = get_input('page_limit');

$DATA = db_fetch(" SELECT * FROM project WHERE COMPANY_ID='$COMPANY_ID' AND (UCASE(PROJECT) LIKE UCASE('$q%')) ORDER BY PROJECT ASC LIMIT $PAGE ");

$t = array();
if(count($DATA)>0)
{
	foreach($DATA as $key => $row){
		$t[$key]['id'] = $row->PROJECT_ID;
		$t[$key]['text'] = $row->PROJECT;
	}
}

$res['results'] = $t;
echo json_encode($res);
