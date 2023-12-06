<?php

function project_option_filter($ALL = 1)
{
	$CU = current_user();
	$PROJECT_ID = isset($CU->PROJECT_ID) ? $CU->PROJECT_ID : '';

	$where = '';
	if( ! empty($PROJECT_ID))
	{
		$where = " WHERE PROJECT_ID='$PROJECT_ID' AND SHOWING=1";
		$t = array();
	}
	else
	{
		$where = " WHERE SHOWING=1";
		$t = array(''=>'--all project--');
		if(empty($ALL)) $t = array();
	}
	
	$rs = db_fetch(" SELECT PROJECT_ID,PROJECT,STATUS FROM project $where ORDER BY PROJECT ASC ");
	
	if(count($rs)>0){
		foreach($rs as $row){
			if($row->STATUS=='CLOSE'){
				$t[$row->PROJECT_ID] = $row->PROJECT.' [CLOSE]';
			}else{
				$t[$row->PROJECT_ID] = $row->PROJECT;
			}
		}
	}
	return $t;
}

function project_option_filter_by_company($ALL = 1)
{
	$CU = current_user();
	$PROJECT_ID = isset($CU->PROJECT_ID) ? $CU->PROJECT_ID : '';

	$where = '';
	if( ! empty($PROJECT_ID))
	{
		$where = " WHERE PROJECT_ID='$PROJECT_ID' AND SHOWING=1";
		$t = array();
	}
	else
	{
		$where = " WHERE SHOWING=1";
		$t = array(''=>'--all unit--');
		if(empty($ALL)) $t = array();
	}
	
	$rs = db_fetch(" SELECT PROJECT_ID,PROJECT,STATUS FROM project $where ORDER BY PROJECT ASC ");
	
	if(count($rs)>0){
		foreach($rs as $row){
			if($row->STATUS=='CLOSE'){
				$t[$row->PROJECT_ID] = $row->PROJECT.' [CLOSE]';
			}else{
				$t[$row->PROJECT_ID] = $row->PROJECT;
			}
		}
	}
	return $t;
}