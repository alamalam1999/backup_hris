<?php

include 'app-load.php';

is_login('struktur.view');

set_search('STRUKTUR', array('sort','order','STRUKTUR'));
if( get_input('clear') ) clear_search('STRUKTUR', array('STRUKTUR'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'STRUKTUR';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
if($STRUKTUR = get_input('STRUKTUR') AND !empty($STRUKTUR)) $wh[] = " UPPER(STRUKTUR) LIKE UPPER('%$STRUKTUR%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_fetch("
	SELECT *
	FROM struktur
	{$where}
	ORDER BY $SORT $ORDER
");

$list = array();
if(count($rs) > 0){
	foreach($rs as $row){
		$thisref = & $refs[ $row->STRUKTUR_ID ];
		$thisref = array_merge((array) $thisref,(array) $row);
		if ($row->PARENT_ID == 0) {
			$list[] = & $thisref;
		} else {
			$refs[$row->PARENT_ID]['children'][] = & $thisref;
		}
	}
}

echo json_encode($list);