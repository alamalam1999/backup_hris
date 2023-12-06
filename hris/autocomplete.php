<?php
require 'app-load.php';

$MASTER = get_input('m');
//$LIMIT = (int) get_input('limit');
if(empty($LIMIT)) $LIMIT = 50;

if($MASTER == 'provinsi')
{
	$q = isset($_REQUEST['q']) ? $_REQUEST['q'] : '';
	$rs = db_execute(" SELECT * FROM provinsi WHERE UCASE(PROVINSI) LIKE UCASE('%".$q."%') ORDER BY PROVINSI LIMIT $LIMIT ");

	$JSON = '';
	$tmp = array();
	while (! $rs->EOF)
	{
		$tmp = $rs->FetchObj();
		$rs->MoveNext();
		$JSON .= json_encode($tmp) . "\n";
	}
	echo $JSON;
}

if($MASTER == 'kota')
{
	$PROVINSI = isset($_REQUEST['PROVINSI']) ? db_escape($_REQUEST['PROVINSI']) : '';
	$q = isset($_REQUEST['q']) ? $_REQUEST['q'] : '';
	$rs = db_execute("
		SELECT * FROM kota A LEFT JOIN provinsi B ON (A.PROVINSI_ID=B.PROVINSI_ID)
		WHERE UCASE(KOTA) LIKE UCASE('".$q."%') AND B.PROVINSI='$PROVINSI' ORDER BY KOTA ASC LIMIT $LIMIT
	");
//PROVINSI='$PROVINSI' AND
	$JSON = '';
	$tmp = array();
	while (! $rs->EOF)
	{
		$tmp = $rs->FetchObj();
		$rs->MoveNext();
		$JSON .= json_encode($tmp) . "\n";
	}
	echo $JSON;
}