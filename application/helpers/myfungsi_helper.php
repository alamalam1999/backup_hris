<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


#umum

function set_rupiah($angka, $type = "")
{

	$hasil_rupiah = $type . number_format($angka, 0, ',', '.');
	return $hasil_rupiah;
}


function getDateRange($date_from = "0000-00-00", $date_to = "0000-00-00")
{
	$begin = new DateTime($date_from);
	$end = new DateTime($date_to);
	$end = $end->modify('+1 day');

	$interval = new DateInterval('P1D');
	$daterange = new DatePeriod($begin, $interval, $end);
	$date = array();
	foreach ($daterange as $key => $value) {
		$date[] = $value->format('Y-m-d');
	}


	return $date;
}

#format tanggal
function date_tosql($from)
{
	$phpdate = strtotime($from);
	$from_date = date('Y-m-d', $phpdate);

	return $from_date;
}
function date_indo($from, $format = 'd/m/Y')
{
	$phpdate = strtotime($from);
	$from_date = date($format, $phpdate);
	#cek kondisi
	if ($from == '1970-01-01' or $from == '0000-00-00') {
		$from_date = '-';
	}
	return $from_date;
}

function date_indo_full($from, $format = 'd/m/Y H:i:s')
{
	$phpdate = strtotime($from);
	$from_date = date($format, $phpdate);

	#cek kondisi
	if ($from == '1970-01-01 00:00:00' or $from == '0000-00-00 00:00:00') {
		$from_date = '-';
	}
	return $from_date;
}
function to_datepicker($from)
{
	$phpdate = strtotime($from);
	$from_date = date('m/d/Y', $phpdate);
	#cek kondisi
	//if($from == '1970-01-01' OR $from == '0000-00-00'){$from_date = '-';}
	return $from_date;
}
function datepicker_range_to_sql($datesFromDatepickerRange)
{
	//sample range  => 01/01/2020 - 01/25/2020
	$dates = explode(" - ", $datesFromDatepickerRange);
	$date['start'] = date_tosql($dates[0]);
	$date['end'] = date_tosql($dates[1]);
	return $date; //show in array
}

function to_datepicker_range($start, $end)
{
	$mulai = to_datepicker($start);
	$selesai = to_datepicker($end);
	#cek kondisi

	//jika mulai dan selesai sama
	if ($mulai == $selesai) {
		$date = $mulai . ' - ' . $selesai;
	}
	//jika selesai => "-" (bermasalah)
	else if ($selesai == '-') {
		$date = $mulai . ' - ' . $mulai;
	}
	//jika mulai => "-" (bermasalah)
	else if ($mulai == '-') {
		$date = $selesai;
	} else {
		$date = $mulai . ' - ' . $selesai;
	}
	return $date;
}
function date_indo_from_range_picker($start, $end)
{
	$mulai = date_indo($start);
	$selesai = date_indo($end);
	#cek kondisi

	//jika mulai dan selesai sama
	if ($mulai == $selesai) {
		$date = $mulai;
	}
	//jika selesai => "-" (bermasalah)
	else if ($selesai == '-') {
		$date = $mulai;
	}
	//jika mulai => "-" (bermasalah)
	else if ($mulai == '-') {
		$date = '-';
	} else {
		$date = $mulai . ' - ' . $selesai;
	}
	return $date;
}

#format Ukuran file
function formatBytes($bytes, $precision = 2)
{
	$units = array('B', 'KB', 'MB', 'GB', 'TB');

	$bytes = max($bytes, 0);
	$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
	$pow = min($pow, count($units) - 1);

	//Uncomment one of the following alternatives
	//$bytes /= pow(1024, $pow);
	$bytes /= (1 << (10 * $pow));

	return round($bytes, $precision) . ' ' . $units[$pow];
}

#pecah multidata ke array
//data string multiple to array
function data_to_Array($data = "", $karakter = "#")
{
	$k = explode($karakter, $data);
	//$k = array_shift($k);
	unset($k[0]);

	return $k;
}

function data_to_Array2($str, $sep = ',')
{
	$exp = @explode($sep, $str);
	$t = array();
	if (is_array($exp) and count($exp) > 0) {
		foreach ($exp as $v) {
			if ($v != '') $t[] = $v;
		}
	}
	return $t;
}


//array to string multiple value
function arrayToString($data = null, $karakter)
{
	$hasil  = '';
	if ($data != null) {
		foreach ($data as $a) {
			$hasil .= $karakter . $a;
		}
	}

	return $hasil;
}

//cocok untuk tampilan string kalimat
function arrayToString2($data = null, $karakter)
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

function clear_txt($txt = "", $to = "", $other = "\s.?")
{

	//$whiteSpace = '\s';  //if you dnt even want to allow white-space set it to ''
	//$pattern = '/[^a-zA-Z0-9'  .$whiteSpace . $other. ']/u';
	$pattern = '/[^a-zA-Z0-9'  . $other . ']/u';
	$cleared = preg_replace($pattern, $to, (string) $txt);

	return  $cleared;
}

function clear_username($txt, $to)
{
	$other = '.@#?!';
	$whiteSpace = '\s';  //if you dnt even want to allow white-space set it to ''
	$pattern = '/[^a-zA-Z0-9'  . $whiteSpace . $other . ']/u';
	$cleared = preg_replace($pattern, $to, (string) $txt);

	return  $cleared;
}

function maskToSql($txt)
{
	$other = '.?';
	$whiteSpace = '\s';  //if you dnt even want to allow white-space set it to ''
	//$pattern = '/[^a-zA-Z0-9'  .$whiteSpace . $other. ']/u';
	$pattern = '/[^0-9]/u';
	$cleared = preg_replace($pattern, '', (string) $txt);

	return  $cleared;
}

function getBulan($id)
{
	$data = array(
		'1' => 'Januari',
		'2' => 'Februari',
		'3' => 'Maret',
		'4' => 'April',
		'5' => 'Mei',
		'6' => 'Juni',
		'7' => 'Juli',
		'8' => 'Agustus',
		'9' => 'September',
		'10' => 'Oktober',
		'11' => 'November',
		'12' => 'Desember',
	);
	if ($data[$id]) {
		$text = $data[$id];
		return $text;
	} else {
		return  '-';
	}
}

function getBulanArray($id, $tahun = null, $id2 = null, $tahun2 = null)
{
	$id_arr = data_to_Array($id, "#");
	if ($id2 != null) {
		$id_arr2 = data_to_Array($id2, "#");
	} else {
		$id_arr2[] = '';
	}

	$data = array(
		'1' => 'Januari',
		'2' => 'Februari',
		'3' => 'Maret',
		'4' => 'April',
		'5' => 'Mei',
		'6' => 'Juni',
		'7' => 'Juli',
		'8' => 'Agustus',
		'9' => 'September',
		'10' => 'Oktober',
		'11' => 'November',
		'12' => 'Desember',
	);
	$text = "";
	$no = 1;
	//tahun 1
	foreach ($id_arr as $a) {
		if ($tahun != null and $tahun != '' and $tahun != '0000') {
			$text .= $no++ . '. ' . $data[$a] . ' ' . $tahun . '<br/>';
		} else {
			$text .= $no++ . '. ' . $data[$a] . '<br/>';
		}
	}
	//tahun 2
	if ($id2 != null) {
		foreach ($id_arr2 as $a) {
			if ($tahun2 != null and $tahun2 != '' and $tahun2 != '0000') {
				$text .= $no++ . '. ' . $data[$a] . ' ' . $tahun2 . '<br/>';
			} else {
				$text .= $no++ . '. ' . $data[$a] . '<br/>';
			}
		}
	}

	if ($text) {
		return $text;
	} else {
		return '-';
	}
}

function getNoCode($no = 0, $tot = 4, $code = '')
{
	$noID = str_pad($no, $tot, '0', STR_PAD_LEFT);
	$noView = $code . $noID;

	return $noView;
}

#kusus aplikasi
function getUserActive($show = "")
{
	$tampil = "";
	$CI = &get_instance();

	$object = $CI->session->userdata('user_logged');

	if (isset($object->{$show})) $tampil = 	$object->{$show};
	return $tampil;
}

function getPeriodeActive($show = 'PERIODE_ID')
{


	$tampil = '';
	$CI = &get_instance();
	$Q = "SELECT * FROM PERIODE WHERE STATUS_PERIODE = 'OPEN' ORDER BY PERIODE_ID DESC LIMIT 1";
	$data = $CI->db->query($Q);

	if ($data->num_rows() > 0) {
		$text = $data->row();
		if ($show == 'PERIODE_ID') {
			$tampil = $text->PERIODE_ID;
		} else if ($show == 'PERIODE') {
			$tampil = $text->PERIODE;
		} else if ($show == 'TAHUN') {
			$tampil = $text->TAHUN;
		} else if ($show == 'BULAN') {
			$tampil = $text->BULAN;
		} else if ($show == 'TANGGAL_MULAI') {
			$tampil = $text->TANGGAL_MULAI;
		} else if ($show == 'TANGGAL_SELESAI') {
			$tampil = $text->TANGGAL_SELESAI;
		}

		return $tampil;
	} else {
		return  '-';
	}
}

function getPeriode($id = null, $show = '')
{

	$tampil = '';
	$CI = &get_instance();
	$data = $CI->db->get_where('periode', array('PERIODE_ID' => $id));

	if ($data->num_rows() > 0) {
		$text = $data->row();
		if ($show == 'PERIODE') {
			$tampil = $text->PERIODE;
		} else if ($show == 'TAHUN') {
			$tampil = $text->TAHUN;
		} else if ($show == 'BULAN') {
			$tampil = $text->BULAN;
		} else if ($show == 'TANGGAL_MULAI') {
			$tampil = $text->TANGGAL_MULAI;
		} else if ($show == 'TANGGAL_SELESAI') {
			$tampil = $text->TANGGAL_SELESAI;
		}

		return $tampil;
	} else {
		return  '-';
	}
}

function getKaryawan($id = null, $show = '')
{

	$tampil = '';
	$CI = &get_instance();
	$data = $CI->db->get_where('karyawan', array('KARYAWAN_ID' => $id));

	if ($data->num_rows() > 0) {
		$text = $data->row();
		if ($show == 'NIK') {
			$tampil = $text->NIK;
		} else if ($show == 'NO_KK') {
			$tampil = $text->NO_KK;
		} else if ($show == 'NAMA') {
			$tampil = $text->NAMA;
		} else if ($show == 'NAMA_PANGGILAN') {
			$tampil = $text->NAMA_PANGGILAN;
		}

		return $tampil;
	} else {
		return  '-';
	}
}

function url()
{
	return 'http://localhost/hris/';
}

function hris($show = 'url', $add = '')
{
	$tampil = "";
	if ($show == 'url') $tampil =  'http://localhost/hris/' . $add;
	if ($show == 'path') $tampil = '/hris/' . $add;
	if ($show == 'root') $tampil = $_SERVER['DOCUMENT_ROOT'] . '/hris/' . $add;

	return $tampil;
}

function getJenisCuti($show = "")
{
	$JENIS = array(
		'SAKIT' => 'SAKIT',
		'IJIN' => 'IJIN',
		'IJIN_LE' => 'IJIN LATE/EARLY',
		'SKD' => 'SURAT KETERANGAN DOKTER',
		'BACKUP' => 'BACKUP',
		'CT' => 'CUTI TAHUNAN',
		'CI' => 'CUTI ISTIMEWA',
		/*
			'TO_IN' => 'TUKAR OFF (IN)',
			'TO_OUT' => 'TUKAR OFF (OUT)',
			'TS' => 'TUKAR SHIFT (SAME DAY)',
			'TS_IN' => 'TUKAR SHIFT (IN)',
			'TS_OUT' => 'TUKAR SHIFT (OUT)',
		*/
		'R' => 'RESIGN',
		'BM' => 'BELUM MASUK',
		'CM' => 'CUTI MELAHIRKAN',
		'DINAS' => 'DINAS',
		'UL' => 'UNPAID LEAVE',
		'SM' => 'SCAN MANUAL',
	);

	$object = new stdClass();
	foreach ($JENIS as $key => $value) {
		$object->$key = $value;
	}
	$tampil = "";
	if (isset($object->{$show})) $tampil = 	$object->{$show};
	return $tampil;
}

function check_time_range($date = "0000-00-00", $time = "00:00:00", $begin = 0, $end=0)
{
	//$start = strtotime("-15 minutes", strtotime("$date $time"));
	//$finish = strtotime("+15 minutes", strtotime("$date $time"));

	$start = strtotime(-(3600 * $begin) . "seconds", strtotime("$date $time"));
	$finish = strtotime(+(3600 * $end) . "seconds", strtotime("$date $time"));

	if (time() >= $start && time() <= $finish) {
		$show = true;
	} else {
		$show = false;
	}

	return $show;
}
