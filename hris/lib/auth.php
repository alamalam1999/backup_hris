<?php

$SESS = new session();

function is_login($MODULE_ACCESS = '')
{
	global $SESS;
	$UID = $SESS->userdata('UID');
	if(empty($UID))
	{
		header('location: login.php');
		exit;
	}
	
	if( $MODULE_ACCESS != '' )
	{
		$CU = current_user();
		$USER_ACCESS = isset($CU->ROLE) ? json_decode(strtolower(stripslashes($CU->ROLE)),TRUE) : array();
		if( ! in_array(strtolower($MODULE_ACCESS),$USER_ACCESS))
		{
			echo '
			<html><head><title>Access Denied</title></head>
			<body style="background-color:#f2f2f2;">
			<div class="jumbotron" style="background-color:#ffffff;font-family:Verdana,Arial;margin:80px 20px 20px;padding:40px;border:3px solid #cccccc;">
				<h2 style="border-bottom:1px dashed #000080;padding-bottom:10px;margin-bottom:10px;color:#ff0000;">Access Denied</h2>
				<p>You don\'t have permit to view or access on this page.</p>
				<p>Please contact your administrator to grants access privileges for this page.</p>
				<p><a href="javascript:history.back()">Click here</a> to back to the previous page.</p>
			</div>
			</body></html>';
			die();
		}
	}
}

function login_exist()
{
	global $SESS;
	$UID = $SESS->userdata('UID');
	if( ! empty($UID))
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}

function login($u,$p)
{
	global $SESS;
	$u = escape($u);
	$p = md5($p);
	$row = db_first(" SELECT USER_ID FROM user WHERE EMAIL='$u' AND PASSWORD='$p' ");
	$UID = isset($row->USER_ID) ? $row->USER_ID : '';
	if(empty($UID))
	{
		return FALSE;
	}
	else
	{
		$SESS->set_userdata('UID',$UID);
		return TRUE;
	}
}


function current_user()
{
	global $SESS;
	$UID = $SESS->userdata('UID');
	$row = db_first(" SELECT * FROM user WHERE USER_ID = '$UID' ");
	$LEVEL_ID = isset($row->LEVEL_ID) ? $row->LEVEL_ID : '';
	
	$level = db_first(" SELECT * FROM user_level WHERE LEVEL_ID = '$LEVEL_ID' ");
	$row->LEVEL = isset($level->LEVEL) ? $level->LEVEL : '';
	$row->ROLE = isset($level->ROLE) ? $level->ROLE : '';
	
	if(!isset($row->USER_ID)){
		$row = new stdClass;
		$row->NAME = 'System Root';
	}
	
	return $row;
}

function logout()
{
	global $SESS;
	$SESS->_destroy();
}

function escape($s)
{
	return trim(addslashes($s));
}

function remove_ext($filename)
{
	return preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);
}

$_GROUP_MENU = array();
function set_menu_group($group,$path)
{
	global $_GROUP_MENU;
	$_GROUP_MENU[$group][] = $path;
}

function get_menu_group($group)
{
	global $_GROUP_MENU;
	return $_GROUP_MENU[$group];
}

function access_button($PARENT,$MODULE_ACCESS,$BUTTON)
{
	global $MODULE;
	$p = explode('.',$MODULE_ACCESS);
	$MOD = isset($p[0]) ? strtolower($p[0]) : '';
	set_menu_group($PARENT,$MOD);
	
	$CU = current_user();
	$USER_ACCESS = isset($CU->ROLE) ? json_decode(strtolower(stripslashes($CU->ROLE)),TRUE) : array();
	if( ! in_array(strtolower($MODULE_ACCESS),$USER_ACCESS))
	{
		$BUTTON = '';
	}
	return $BUTTON;
}

function has_access($MODULE_ACCESS)
{
	global $MODULE;
	$p = explode('.',$MODULE_ACCESS);
	
	$CU = current_user();
	$USER_ACCESS = isset($CU->ROLE) ? json_decode(strtolower(stripslashes($CU->ROLE)),TRUE) : array();
	if( ! in_array(strtolower($MODULE_ACCESS),$USER_ACCESS))
	{
		return FALSE;
	}
	else
	{
		return TRUE;
	}
}