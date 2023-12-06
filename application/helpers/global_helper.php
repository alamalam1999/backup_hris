<?php

/**
 * Global function for application
 *
 */
//AGUNG
function getUUID(){
	$CI =& get_instance();
    $data = $CI->db->query("SELECT UUID() AS ID")->row()->ID;
    return $data;
 }
// NEW
function cek_null($data = null,$output = ''){
	if(var_dump($data) == null){
		$out = $output;
	}else {
		$out = '';
	}
	return $out;
}

// old

function _nl2br($s){
	return str_replace(array("\n\r","\n","\r"),array("","<br>",""),$s);
}
function _br2nl($s){
	return str_ireplace('<br>',"\n",$s);
}

function active($index = '', $style = FALSE){
	$array_plain = array(0 => 'Tidak Aktif', 1 => 'Aktif');
	$array_style = array(0 => '<span style="color:#ff0000;">Tidak Aktif</span>', 1 => '<span style="color:#00aa00;">Aktif</span>');
	if($style) $array = $array_style;
	else $array = $array_plain;
	if($index == 'LIST'){
		return $array;
	}else{
		return isset($array[$index]) ? $array[$index] : '';
	}
}

function yesno($index = '', $style = FALSE){
	if($index==0) $index = (string) $index;
	$array_plain = array('0' => 'No', '1' => 'Yes');
	$array_style = array('0' => '<span style="color:#ff0000;">No</span>', '1' => '<span style="color:#00aa00;">Yes</span>');
	if($style) $array = $array_style;
	else $array = $array_plain;
	if($index == 'LIST'){
		return $array;
	}else{
		return isset($array[$index]) ? $array[$index] : $array[0];
	}
}

function access_menu($group='',$path,$title,$access=array(),$template='<a href="{URL}">{TITLE}</a>')
{
	set_menu_group($group,$path);
	$ci					= get_instance();
	$default_controller = isset($ci->router->routes['default_controller']) ? $ci->router->routes['default_controller'] : 'welcome';
	$current_controller	= $ci->uri->segment(1,$default_controller);
	$current_method		= $ci->uri->segment(2,'index');
	$p					= explode('/',$path);
	$controller			= $p[0];
	$method				= isset($p[1]) ? $p[1] : 'index';
	$class_on			= '';

	if(strtoupper($current_controller)==strtoupper($p[0])) $class_on = 'on';

	$replacer = array(
		'{URL}'			=> site_url($path),
		'{TITLE}'		=> stripslashes(strip_tags($title,'<span>')),
		'{CLASS_ON}'	=> $class_on
	);

	if(isset($access[$controller.'/'.$method])){
		return str_replace(array_keys($replacer),array_values($replacer),$template);
	}
}

$_GROUP_MENU = array();
function set_menu_group($group,$path){
	global $_GROUP_MENU;
	$_GROUP_MENU[$group][] = $path;
}

function get_menu_group($group){
	global $_GROUP_MENU;
	return $_GROUP_MENU[$group];
}

function rupiah($s){
	if($s=='') return '';
	return number_format($s,0,',','.');
}
function currency($s){
	if($s=='') return '';
	return number_format($s,0,'.',',');
}

function time_to_unixts($time){
	$p = explode(':',$time);
	$h = isset($p[0]) ? (int) $p[0] : 0;
	$m = isset($p[1]) ? (int) $p[1] : 0;
	$s = isset($p[2]) ? (int) $p[2] : 0;
	return ($h*3600)+($m*60)+$s;
}

function tgl($date, $time = FALSE, $day = FALSE){
	if($date=='' OR $date == '0000-00-00' OR $date == '0000-00-00 00-00-00') return '';
	$str = strtotime($date);
	#$m = array('','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des');
	$m = array('','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
	$aday = array('Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu');
	$_time = '';
	$_day = '';
	if($time) $_time = date(', H:i',$str);
	if($day) $_day = isset($aday[date('w',$str)]) ? $aday[date('w',$str)].', ' : '';
	return $_day.date('d', $str).' '.$m[intval(date('m', $str))].' '.date('Y', $str).$_time;
}

function tanggal($date, $time = FALSE, $day = FALSE){
	if($date=='' OR $date == '0000-00-00' OR $date == '0000-00-00 00:00:00') return '';
	$str = strtotime($date);
	$m = array('','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
	$aday = array('Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu');
	$_time = '';
	$_day = '';
	if($time) $_time = date(', H:i',$str);
	if($day) $_day = isset($aday[date('w',$str)]) ? $aday[date('w',$str)].', ' : '';
	return $_day.date('d', $str).' '.$m[intval(date('m', $str))].' '.date('Y', $str).$_time;
}

function hari($date){
	if($date=='' OR $date == '0000-00-00' OR $date == '0000-00-00 00-00-00') return '';
	$str = strtotime($date);
	$aday = array('Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu');
	$_day = '';
	$_day = isset($aday[date('w',$str)]) ? $aday[date('w',$str)] : '';
	return $_day;
}

function ctgl($date,$format='d M Y'){
	if($date == '' || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') return '';
	$str = strtotime($date);
	return date($format,$str);
}

function cdate($format='d M Y',$date){
	return ctgl($date,$format);
}

function getMonth(){
	$m = array('01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus',
	'09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember');
	return $m;
}

function toMonth($month,$short = 0){
	$month = intval($month);
	$m = array('','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
	if($short == 1){
		#$m = array('','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des');
		$m = array('','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
	}
	return $m[$month];
}

function search_filter($str){
	return trim(rtrim(ltrim(urlencode($str))));
}

function output_text($text){
	return stripslashes($text);
}

function ext($str){
	$t = explode('.',$str);
	$c = count($t)-1;
	return $t[$c];
}

function time_span($t1,$t2){
	$t1 = (int) strtotime($t1);
	$t2 = (int) strtotime($t2);
	$t = $t2-$t1;
	if($t<0) $t=0;
	$res = secondsToTime($t);
	$ret = '';
	if( ! empty($res['h']) OR ! empty($res['m']) ){
		$ret = str_pad($res['h'], 2, "0", STR_PAD_LEFT).':'.str_pad($res['m'], 2, "0", STR_PAD_LEFT);
	}

	return $ret;
}

function secondsToTime($seconds)
{
    // extract hours
    $hours = floor($seconds / (60 * 60));

    // extract minutes
    $divisor_for_minutes = $seconds % (60 * 60);
    $minutes = floor($divisor_for_minutes / 60);

    // extract the remaining seconds
    $divisor_for_seconds = $divisor_for_minutes % 60;
    $seconds = ceil($divisor_for_seconds);

    // return the final array
    $obj = array(
        "h" => (int) $hours,
        "m" => (int) $minutes,
        "s" => (int) $seconds,
    );

    return $obj;
}

function get_query_string()
{
	$qs = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
	return $qs;
}

function kekata($x) {
	$x = abs($x);
	$angka = array("", "satu", "dua", "tiga", "empat", "lima",
	"enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
	$temp = "";
	if ($x <12) {
		$temp = " ". $angka[$x];
	} else if ($x <20) {
		$temp = kekata($x - 10). " belas";
	} else if ($x <100) {
		$temp = kekata($x/10)." puluh". kekata($x % 10);
	} else if ($x <200) {
		$temp = " seratus" . kekata($x - 100);
	} else if ($x <1000) {
		$temp = kekata($x/100) . " ratus" . kekata($x % 100);
	} else if ($x <2000) {
		$temp = " seribu" . kekata($x - 1000);
	} else if ($x <1000000) {
		$temp = kekata($x/1000) . " ribu" . kekata($x % 1000);
	} else if ($x <1000000000) {
		$temp = kekata($x/1000000) . " juta" . kekata($x % 1000000);
	} else if ($x <1000000000000) {
		$temp = kekata($x/1000000000) . " milyar" . kekata(fmod($x,1000000000));
	} else if ($x <1000000000000000) {
		$temp = kekata($x/1000000000000) . " trilyun" . kekata(fmod($x,1000000000000));
	}
		return $temp;
}

function terbilang($x, $style=4) {
	if($x<0) {
		$hasil = "minus ". trim(kekata($x));
	} else {
		$hasil = trim(kekata($x));
	}
	switch ($style) {
		case 1:
			$hasil = strtoupper($hasil);
			break;
		case 2:
			$hasil = strtolower($hasil);
			break;
		case 3:
			$hasil = ucwords($hasil);
			break;
		default:
			$hasil = ucfirst($hasil);
			break;
	}
	return $hasil;
}

function bg($i,$c1,$c2){
	if(($i%2)==1) return $c1;
	else return $c2;
}

function _explode($sep = ',',$str){
	$exp = @explode($sep,$str);
	$t = array();
	if(is_array($exp) AND count($exp)>0){
		foreach($exp as $v){
			if($v != '') $t[] = $v;
		}
	}
	return $t;
}

// ------------ lixlpixel recursive PHP functions -------------
// recursive_remove_directory( directory to delete, empty )
// expects path to directory and optional TRUE / FALSE to empty
// of course PHP has to have the rights to delete the directory
// you specify and all files and folders inside the directory
// ------------------------------------------------------------

// to use this function to totally remove a directory, write:
// recursive_remove_directory('path/to/directory/to/delete');

// to use this function to empty a directory, write:
// recursive_remove_directory('path/to/full_directory',TRUE);

function recursive_remove_directory($directory, $empty=FALSE)
{
	// if the path has a slash at the end we remove it here
	if(substr($directory,-1) == '/')
	{
		$directory = substr($directory,0,-1);
	}

	// if the path is not valid or is not a directory ...
	if(!file_exists($directory) || !is_dir($directory))
	{
		// ... we return false and exit the function
		return FALSE;

	// ... if the path is not readable
	}elseif(!is_readable($directory))
	{
		// ... we return false and exit the function
		return FALSE;

	// ... else if the path is readable
	}else{

		// we open the directory
		$handle = opendir($directory);

		// and scan through the items inside
		while (FALSE !== ($item = readdir($handle)))
		{
			// if the filepointer is not the current directory
			// or the parent directory
			if($item != '.' && $item != '..')
			{
				// we build the new path to delete
				$path = $directory.'/'.$item;

				// if the new path is a directory
				if(is_dir($path))
				{
					// we call this function with the new path
					recursive_remove_directory($path);

				// if the new path is a file
				}else{
					// we remove the file
					unlink($path);
				}
			}
		}
		// close the directory
		closedir($handle);

		// if the option to empty is not set to true
		if($empty == FALSE)
		{
			// try to delete the now empty directory
			if(!rmdir($directory))
			{
				// return false if not possible
				return FALSE;
			}
		}
		// return success
		return TRUE;
	}
}

function generate_pin($num = 8)
{
	$character_array = array_merge(range(A, Z), range(0, 9));
	$string = "";
	for($i = 0; $i < $num; $i++) {
		$string .= $character_array[rand(0, (count($character_array) - 1))];
	}
	return $string;
}

function input_number($str)
{
	return str_replace(array(' ',','),'',$str);
}

function romanic_number($integer, $upcase = true)
{
	$integer = intval($integer);
    $table = array('M'=>1000, 'CM'=>900, 'D'=>500, 'CD'=>400, 'C'=>100, 'XC'=>90, 'L'=>50, 'XL'=>40, 'X'=>10, 'IX'=>9, 'V'=>5, 'IV'=>4, 'I'=>1);
    $return = '';
    while($integer > 0)
    {
        foreach($table as $rom=>$arb)
        {
            if($integer >= $arb)
            {
                $integer -= $arb;
                $return .= $rom;
                break;
            }
        }
    }
    return $return;
}

function array_swap($array)
{
	if( ! is_array($array) ) return array();
	$tmp = array();
	if(count($array)>0){ foreach($array as $key => $val){
		$tmp[$val] = $key;
	}}
	return $tmp;
}

function get_search($module,$field)
{
	$ci =& get_instance();
	$userdata = $ci->session->userdata('search_'.$module.'_'.$field);
	return $userdata;
}

function set_search($module,$field)
{
	$ci =& get_instance();
	if(is_array($field)){
		if(count($field) > 0){ foreach($field as $f){
			if( isset($_REQUEST[$f]) ){
				$ci->session->set_userdata('search_'.$module.'_'.$f, $_REQUEST[$f]);
			}
		}}
	}else{
		if( isset($_REQUEST[$field]) ){
			$ci->session->set_userdata('search_'.$module.'_'.$field, $_REQUEST[$field]);
		}
	}
}

function clear_search($module,$field){
	$ci =& get_instance();
	if(is_array($field)){
		if(count($field) > 0){ foreach($field as $f){
			$ci->session->set_userdata('search_'.$module.'_'.$f, '');
		}}
	}else{
		$ci->session->set_userdata('search_'.$module.'_'.$field, '');
	}
}

function url_exists($url){
   $headers = get_headers($url);
   return stripos($headers[0],"200 OK") ? TRUE : FALSE;
}



function get_input($s)
{
	return isset($_REQUEST[$s]) ? $_REQUEST[$s] : '';
}

function dropdown($NAME,$ARRAY,$VALUE = '',$STYLE = ''){
	$res = '<select name="'.$NAME.'" '.$STYLE.'>';
	if(is_array($ARRAY) AND count($ARRAY)>0){ foreach($ARRAY as $k => $v){
		$sel = ($k==$VALUE) ? ' selected' : '';
		$res .= '<option value="'.$k.'"'.$sel.'>'.$v.'</option>';
	}}
	$res .= '</select>';
	return $res;
}

// KHUSUS DATABASE

function db_first($sql){
	$ci =& get_instance();
	$data = $ci->db->query($sql);
	if($data->num_rows() > 0 ) return $data->result();
}
function db_execute($sql){
	$ci =& get_instance();
	$ci->db->query($sql);
}

function sql_show($sql){
	echo  $sql;
}




// function db_fetch($sql){
// 	$ci =& get_instance();
// 	$data = $ci->db->query($sql);
// 	if($data->num_rows() > 0 ) return $data->result();
// }



function db_escape($data,$CLEAN = FALSE) {
	if ( !isset($data) or empty($data) ) return '';
	if ( is_numeric($data) ) return $data;

	$non_displayables = array(
		'/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
		'/%1[0-9a-f]/',             // url encoded 16-31
		'/[\x00-\x08]/',            // 00-08
		'/\x0b/',                   // 11
		'/\x0c/',                   // 12
		'/[\x0e-\x1f]/'             // 14-31
	);
	foreach ( $non_displayables as $regex ){
		$data = preg_replace( $regex, '', $data );
	}

	if($CLEAN){
		$data = preg_replace('/[^A-Za-z0-9\-\_\.\(\)\ ]/i', '', $data);
		$data = strip_tags($data);
	}

	$data = str_replace("'", "\'", $data );
	if($data == '.') $data = '';
	return @rtrim(@ltrim(@trim($data)));
}



function dropdown_option($TABLE,$VALUE,$COLUMN,$EXTRA = '')
{
	$ci =& get_instance();
	$rs = $ci->db->query(" SELECT $VALUE,$COLUMN FROM $TABLE $EXTRA ")->result();
	$t = array();
	if(count($rs)>0){
		foreach($rs as $row){
			$t[$row->$VALUE] = $row->$COLUMN;
		}
	}
	return $t;
}
