<?php

// $DB_HOST	= 'localhost';
// $DB_USER	= 'root';
// $DB_PASS	= '';
//$DB_NAME	= 'drapps_hris';

$DB_HOST	= 'localhost';
$DB_USER	= 'root';
$DB_PASS	= '@Hris_22!?**';
$DB_NAME	= 'ypap_hris_db';
$DB_DRIVER	= 'mysqli';
$DB_LOGGING	= TRUE;
$SQL_LOG	= FALSE;
$ENABLE_SSL	= 0;

$JENIS_EKSEPSI = array(
	'SAKIT' => '<div style="background-color:#fff000;font-weight:bold;">SAKIT</div>',
	'IJIN' => '<div style="background-color:#fff000;font-weight:bold;">IJIN</div>',
	'SKD' => '<div style="background-color:#00ff00;font-weight:bold;">SKD</div>',
	'CI' => '<div style="background-color:#ff00ff;font-weight:bold;">CI</div>',
	'CT' => '<div style="background-color:#ff00ff;font-weight:bold;">CT</div>',
	'TO_IN' => '<div style="background-color:#ff00ff;font-weight:bold;">TO-IN</div>',
	'TO_OUT' => '<div style="background-color:#ff00ff;font-weight:bold;">TO-OUT</div>',
	'TS' => '<div style="background-color:#ff0000;font-weight:bold;">TS</div>',
	'R' => '<div style="background-color:#ff0000;font-weight:bold;">R</div>',
	'BM' => '<div style="background-color:#ff0000;font-weight:bold;">BM</div>',
);


error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);
//error_reporting(0);
date_default_timezone_set('Asia/Jakarta');

if ($ENABLE_SSL == '1' and $_SERVER['SERVER_PORT'] != '443') {
	header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	exit();
}

include_once 'lib/adodb5/adodb.inc.php';
$ADODB_CACHE_DIR = dirname(__FILE__) . '/cache';
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
$DB = NewADOConnection($DB_DRIVER);
$DB->Connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME) or die($DB->ErrorMsg());
$DB->Execute("SET GLOBAL sql_mode = ''");
$DB->Execute("SET SESSION sql_mode = ''");

include_once 'lib/session.php';
include_once 'lib/auth.php';
include_once 'lib/mail.php';
include_once 'lib/finger-print.php';
include_once 'lib/absensi.php';
include_once 'lib/project-filter.php';
include_once 'lib/gravatar.php';

function db_execute($sql, $limit = FALSE, $start = '', $per_page = '')
{
	global $DB, $DB_LOGGING, $SQL_LOG;
	if ($limit) $rs = $DB->SelectLimit($sql, $start, $per_page);
	else $rs = $DB->Execute($sql);
	if ($DB_LOGGING and !$rs) {
		$sql = str_replace(array("\n", "\t", "\r"), "", $sql);
		$msg = date('H:i') . "  Error : " . $DB->ErrorMsg() . " at query : " . $sql . "\n";
		error_log($msg, 3, dirname(__FILE__) . "/logs/error_" . date('Y-m-d') . ".log");
	}
	if ($SQL_LOG) {
		db_set_sql_log($sql);
	}
	return $rs;
}

function sql_show($sql)
{
	echo  $sql;
}

$_SQL_LOG = array();
function db_set_sql_log($sql)
{
	global $_SQL_LOG;
	$_SQL_LOG[] = $sql;
}

function db_get_sql_log()
{
	global $_SQL_LOG;
	if (count($_SQL_LOG) > 0) {
		echo '<div style="padding:15px;"><table style="width:100%;" class="pure-table pure-table-horizontal"><thead><th>SQL LOG</th></thead><tbody>';
		foreach ($_SQL_LOG as $sql) {
			echo '<tr><td style="padding:4px;">' . $sql . '</td></tr>';
		}
		echo '</tbody></table></div>';
	}
}

function db_first($sql)
{
	$rs = db_execute($sql);
	//return $rs->FetchObj();
	if ($rs) {
		return $rs->FetchObj();
	}
}

function db_exists($sql)
{
	$rs = db_execute($sql);
	return ($rs->NumRows() > 0) ? TRUE : FALSE;
}

function db_fetch($sql)
{
	$rs = db_execute($sql);
	$t = array();
	while (!$rs->EOF) {
		$row = $rs->FetchObj();
		$t[] = $row;
		$rs->MoveNext();
	}
	return $t;
}

function db_fetch_limit($sql, $start, $per_page)
{
	$rs = db_execute($sql, 1, $start, $per_page);
	$t = array();
	while (!$rs->EOF) {
		$row = $rs->FetchObj();
		$t[] = $row;
		$rs->MoveNext();
	}
	return $t;
}

function db_escape($data, $CLEAN = FALSE)
{
	if (!isset($data) or empty($data)) return '';
	if (is_numeric($data)) return $data;

	$non_displayables = array(
		'/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
		'/%1[0-9a-f]/',             // url encoded 16-31
		'/[\x00-\x08]/',            // 00-08
		'/\x0b/',                   // 11
		'/\x0c/',                   // 12
		'/[\x0e-\x1f]/'             // 14-31
	);
	foreach ($non_displayables as $regex) {
		$data = preg_replace($regex, '', $data);
	}

	if ($CLEAN) {
		$data = preg_replace('/[^A-Za-z0-9\-\_\.\(\)\ ]/i', '', $data);
		$data = strip_tags($data);
	}

	$data = str_replace("'", "\'", $data);
	if ($data == '.') $data = '';
	return @rtrim(@ltrim(@trim($data)));
}

function base_url()
{
	if (isset($_SERVER['HTTP_HOST'])) {
		$base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
		$base_url .= '://' . $_SERVER['HTTP_HOST'];
		$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
	} else {
		$base_url = 'http://localhost/';
	}
	return $base_url;
}

function self()
{
	return $_SERVER['PHP_SELF'];
}

function get_input($s)
{
	return isset($_REQUEST[$s]) ? $_REQUEST[$s] : '';
}

function input_currency($s)
{
	$s = str_replace(',', '', $s);
	return $s;
	//return preg_replace('/[^0-9\.]*/','',$s);
}

function set_value($s, $init = '')
{
	return isset($_REQUEST[$s]) ? trim($_REQUEST[$s]) : $init;
}

function view_date($date)
{
	if (empty($date)) return '';
	return date('Y-m-d H:i', strtotime($date));
}

function cdate($date, $format = 'Y-m-d')
{
	if (in_array($date, array('', '0000-00-00'))) return '';
	return date($format, strtotime($date));
}

function dropdown($NAME, $ARRAY, $VALUE = '', $STYLE = '')
{
	$res = '<select name="' . $NAME . '" ' . $STYLE . '>';
	if (is_array($ARRAY) and count($ARRAY) > 0) {
		foreach ($ARRAY as $k => $v) {
			$sel = ($k == $VALUE) ? ' selected' : '';
			$res .= '<option value="' . $k . '"' . $sel . '>' . $v . '</option>';
		}
	}
	$res .= '</select>';
	return $res;
}

function dropdown_option($TABLE, $VALUE, $COLUMN, $EXTRA = '')
{
	$rs = db_fetch(" SELECT $VALUE,$COLUMN FROM $TABLE $EXTRA ");
	$t = array();
	if (count($rs) > 0) {
		foreach ($rs as $row) {
			$t[$row->$VALUE] = $row->$COLUMN;
		}
	}
	return $t;
}

function dropdown_option_default($TABLE, $VALUE, $COLUMN, $EXTRA = '', $DEFAULT)
{
	$rs = db_fetch(" SELECT $VALUE,$COLUMN FROM $TABLE $EXTRA ");
	$t = array();
	if (count($rs) > 0) {
		foreach ($rs as $row) {
			$t[''] = $DEFAULT;
			$t[$row->$VALUE] = $row->$COLUMN;
		}
	}
	return $t;
}

function menu()
{
	$rs = db_execute(" SELECT * FROM TPSONLINE_MENU WHERE ENABLE='1' ORDER BY POS ASC ");
	return $rs;
}

function get_search($module, $field)
{
	global $SESS;
	$userdata = $SESS->userdata('search_' . $module . '_' . $field);
	return $userdata;
}

function set_search($module, $field)
{
	global $SESS;
	if (is_array($field)) {
		if (count($field) > 0) {
			foreach ($field as $f) {
				if (isset($_REQUEST[$f])) {
					$SESS->set_userdata('search_' . $module . '_' . $f, $_REQUEST[$f]);
				}
			}
		}
	} else {
		if (isset($_REQUEST[$field])) {
			$SESS->set_userdata('search_' . $module . '_' . $field, $_REQUEST[$field]);
		}
	}
}

function clear_search($module, $field)
{
	global $SESS;
	if (is_array($field)) {
		if (count($field) > 0) {
			foreach ($field as $f) {
				$SESS->set_userdata('search_' . $module . '_' . $f, '');
			}
		}
	} else {
		$SESS->set_userdata('search_' . $module . '_' . $field, '');
	}
}

function get_option($var)
{
	static $v;

	if (!is_array($v)) {
		$v = array();
		$rs = db_fetch(" SELECT * FROM options ");
		if (count($rs) > 0) {
			foreach ($rs as $row) {
				$v[$row->var] = ($row->val == '') ? $row->default : $row->val;
			}
		}
	}

	return isset($v[$var]) ? $v[$var] : NULL;
}

function set_option($var, $val = '')
{
	if (is_array($var)) {
		$tmp = array();
		if (count($var) > 0) {
			foreach ($var as $k => $v) {
				$k = db_escape($k, FALSE);
				$v = db_escape($v, FALSE);
				$tmp[] = "('$k','$v')";
			}

			$data = @implode(',', $tmp);
			return db_execute(" INSERT INTO `options` (`var`,`val`) VALUES $data ON DUPLICATE KEY UPDATE val=VALUES(val) ");
		}

		return FALSE;
	} else {
		$var = db_escape($var, FALSE);
		$val = db_escape($val, FALSE);
		$query = db_fetch(" SELECT * FROM `options` WHERE var='$var' ORDER BY var ASC LIMIT 1 ");
		if ($query->num_rows() > 0) {
			return db_execute(" UPDATE `options` SET val='$val' WHERE var='$var' ");
		} else {
			return db_execute(" INSERT INTO `options` (`var`,`val`) VALUES ('$var','$val') ");
		}
	}
}

function currency($num)
{
	return number_format($num, 0, '.', ',');
}

function hirearchy($arr, $max_depth = 11, $current_depth = 0, $result = array())
{
	if (!is_array($arr)) return $result;
	if (count($arr) < 1) return $result;
	$current_depth = $current_depth + 1;
	if ($max_depth != 0) {
		if ($current_depth > $max_depth) return $result;
	}
	foreach ($arr as $a) {
		if (!isset($result)) $result = array();
		$new_data = array();
		$new_data = (object) $a;
		$new_data->DEPTH = $current_depth;
		$tmp = isset($new_data->child) ? $new_data->child : '';
		unset($new_data->child);
		$result[] = $new_data;
		if (is_array($tmp)) {
			$result = hirearchy($tmp, $max_depth, $current_depth, $result);
		}
	}

	return $result;
}

function print_role($arr, $SAVED)
{
	echo '<ul class="list-group">';
	foreach ($arr as $a) {
		if (isset($a['child']) and is_array($a['child'])) {
			echo '<li><h4>' . $a['MODULE'] . '</h4>';
			print_role($a['child'], $SAVED);
			'</li>';
		} else {
			$MODULE = $a['MODULE'];
			echo '<li class="list-group-item"><div class="row"><div class="col-sm-2">' . $MODULE . '</div><div class="col-sm-10"><div class="row">';
			$ROLE = json_decode(stripslashes($a['PARAMS']));
			if (count($ROLE) > 0) {
				foreach ($ROLE as $role) {
					$checked = '';
					if (in_array(strtolower($MODULE . '.' . $role), $SAVED)) $checked = 'checked';
					echo '<div class="col-xs-2"><label style="margin-bottom:0;font-weight:normal;"><input type="checkbox" name="ROLE[]" value="' . strtolower($MODULE) . '.' . strtolower($role) . '" ' . $checked . '> ' . $role . '</label></div>';
				}
			}
			echo '</div></div></li>';
		}
	}
	echo '</ul>';
}

function uri($segment = NULL, $qs = false)
{
	$uri = $_SERVER['REQUEST_URI'];
	if (!$qs || $segment !== NULL) {
		if (strpos($uri, '?')) {
			list($uri, $query) = explode('?', $uri);
		}
		if ($segment !== NULL) {
			if (is_string($segment)) {
				if ($segment != 'last') {
					return (strlen($uri) >= strlen($segment) && substr($uri, 0, strlen($segment)));
				}
			}
			$str = trim($uri, '/');
			$segments = (strpos($str, '/')) ? explode('/', $str) : array($str);
			$ttl = count($segments);
			if (is_string($segment) && $segment == 'last') {
				$seg = array_pop($segments);
			} elseif ($segment <= $ttl) {
				if ($segment < 0) $segment = $ttl - abs($segment) + 1;
				$seg = $segments[$segment - 1];
			} else {
				return '';
			}

			return $seg;
		}
	}
	return $uri;
}

function tgl($date, $time = FALSE, $day = FALSE)
{
	if ($date == '' || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') return '';
	$str = strtotime($date);
	$m = array('', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des');
	$aday = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
	$_time = '';
	$_day = '';
	if ($time) $_time = date(', H:i', $str);
	if ($day) $_day = isset($aday[date('w', $str)]) ? $aday[date('w', $str)] . ', ' : '';
	return $_day . date('d', $str) . '-' . $m[intval(date('m', $str))] . '-' . date('Y', $str) . $_time;
}

function bg($i, $c1, $c2)
{
	if (($i % 2) == 1) return $c1;
	else return $c2;
}

function number($num)
{
	$value = number_format($num, 2, ',', '.');
	return $value;
}

function url_exists($url)
{
	$headers = get_headers($url);
	return stripos($headers[0], "200 OK") ? TRUE : FALSE;
}

function valid_email($address)
{
	return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $address)) ? FALSE : TRUE;
}

function create_notif($UID, $msg)
{
	global $DB;
	$CU = current_user();
	db_execute(" INSERT INTO notif (`KARYAWAN_ID`,`TEKS`,`CREATED_ON`,`FG_READ`) VALUES ('$UID','$msg',NOW(),'0') ");
	return $DB->Affected_Rows();
}

function out($text)
{
	$array = array(
		'{BASE_URL}' => base_url()
	);
	return str_replace(array_keys($array), array_values($array), $text);
}

function get_brightness($hex)
{
	// returns brightness value from 0 to 255
	// strip off any leading #
	$hex = str_replace('#', '', $hex);

	$c_r = hexdec(substr($hex, 0, 2));
	$c_g = hexdec(substr($hex, 2, 2));
	$c_b = hexdec(substr($hex, 4, 2));

	return (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
}

function project_eksepsi_option($ALL = 1)
{
	$CU = current_user();
	$PROJECT_ID = isset($CU->PROJECT_ID) ? $CU->PROJECT_ID : '';

	$where = '';
	if (!empty($PROJECT_ID)) {
		$where = " WHERE PROJECT_ID='$PROJECT_ID' ";
		$t = array();
	} else {
		$t = array('' => '--all project--');
		if (empty($ALL)) $t = array();
	}

	$ek = db_fetch("
			SELECT PROJECT_ID,COUNT(1) as cnt FROM eksepsi A
			LEFT JOIN karyawan K ON K.KARYAWAN_ID=A.KARYAWAN_ID
			WHERE STATUS='PENDING' GROUP BY PROJECT_ID
		");
	$EX = array();
	if (count($ek) > 0) {
		foreach ($ek as $row) {
			$EX[$row->PROJECT_ID] = $row->cnt;
		}
	}

	$rs = db_fetch(" SELECT PROJECT_ID,PROJECT FROM project $where ORDER BY PROJECT ASC ");

	if (count($rs) > 0) {
		foreach ($rs as $row) {
			$ek = isset($EX[$row->PROJECT_ID]) ? ' ---- (' . $EX[$row->PROJECT_ID] . ')' : '';
			$t[$row->PROJECT_ID] = $row->PROJECT . $ek;
		}
	}
	return $t;
}

function project_lembur_option($ALL = 1)
{
	$CU = current_user();
	$PROJECT_ID = isset($CU->PROJECT_ID) ? $CU->PROJECT_ID : '';

	$where = '';
	if (!empty($PROJECT_ID)) {
		$where = " WHERE PROJECT_ID='$PROJECT_ID' ";
		$t = array();
	} else {
		$t = array('' => '--all project--');
		if (empty($ALL)) $t = array();
	}

	$ek = db_fetch("
			SELECT PROJECT_ID,COUNT(1) as cnt FROM lembur A
			LEFT JOIN karyawan K ON K.KARYAWAN_ID=A.KARYAWAN_ID
			WHERE STATUS='PENDING' GROUP BY PROJECT_ID
		");
	$EX = array();
	if (count($ek) > 0) {
		foreach ($ek as $row) {
			$EX[$row->PROJECT_ID] = $row->cnt;
		}
	}

	$rs = db_fetch(" SELECT PROJECT_ID,PROJECT FROM project $where ORDER BY PROJECT ASC ");

	if (count($rs) > 0) {
		foreach ($rs as $row) {
			$ek = isset($EX[$row->PROJECT_ID]) ? ' ---- (' . $EX[$row->PROJECT_ID] . ')' : '';
			$t[$row->PROJECT_ID] = $row->PROJECT . $ek;
		}
	}
	return $t;
}

/* masih perlu penyesuaian untuk tabel log_online mesti dipisah dari tabel tabel_absen */
function project_approval_absensi_option($ALL = 1)
{
	$CU = current_user();
	$PROJECT_ID = isset($CU->PROJECT_ID) ? $CU->PROJECT_ID : '';

	$where = '';
	if (!empty($PROJECT_ID)) {
		$where = " WHERE PROJECT_ID='$PROJECT_ID' ";
		$t = array();
	} else {
		$t = array('' => '--all project--');
		if (empty($ALL)) $t = array();
	}

	$ek = db_fetch(" SELECT PROJECT_ID, COUNT(1) as cnt 
		FROM log_online A
		LEFT JOIN karyawan K ON K.KARYAWAN_ID=A.PIN
		WHERE STATUS='PENDING' GROUP BY PROJECT_ID
	");

	$EX = array();
	if (count($ek) > 0) {
		foreach ($ek as $row) {
			$EX[$row->PROJECT_ID] = $row->cnt;
		}
	}

	$rs = db_fetch(" SELECT PROJECT_ID, PROJECT FROM project $where ORDER BY PROJECT ASC ");

	if (count($rs) > 0) {
		foreach ($rs as $row) {
			$ek = isset($EX[$row->PROJECT_ID]) ? ' ---- (' . $EX[$row->PROJECT_ID] . ')' : '';
			$t[$row->PROJECT_ID] = $row->PROJECT . $ek;
		}
	}
	return $t;
}


//ADD AGUNG
//cocok untuk tampilan string kalimat
function arrayToString2($data = array(), $karakter = ",")
{
	$hasil  = '';
	if ($data != null) {
		foreach ($data as $key => $a) {
			if ($key == 0) $hasil .= $a;
			else $hasil .= $karakter . $a;
		}
	}


	return $hasil;
}
