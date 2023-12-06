<?php

include 'app-load.php';

$where = '';
$wh = array();
if($PERIODE_ID = get_input('PERIODE_ID') AND !empty($PERIODE_ID)) $wh[] = " PERIODE_ID = '$PERIODE_ID' ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_first("
	SELECT *
	FROM periode
	{$where}
");

echo json_encode($rs);
