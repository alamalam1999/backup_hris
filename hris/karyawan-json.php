<?php

include 'app-load.php';

is_login('karyawan.view');

$ST = array(
		'PENDING' => '<span style="color:#0000ff;">PENDING</span>',
		'APPROVED' => '<span style="color:#00c000;">APPROVED</span>',
		'VOID' => '<span style="color:#ff0000;">VOID</span>',
	);

set_search('KARYAWAN', array('sort','order','JABATAN_ID','PROJECT_ID','COMPANY_ID','NAMA','NIK'));
if( get_input('clear') ) clear_search('KARYAWAN', array('JABATAN_ID','PROJECT_ID','COMPANY_ID','NAMA','NIK'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'KARYAWAN_ID';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
$wh[] = " (A.ST_KERJA = 'AKTIF' OR A.ST_KERJA = '') ";
if($JABATAN_ID = get_input('JABATAN_ID') AND !empty($JABATAN_ID)) $wh[] = " J.JABATAN_ID = '$JABATAN_ID' ";
if($COMPANY_ID = get_input('COMPANY_ID') AND !empty($COMPANY_ID)) $wh[] = " C.COMPANY_ID = '$COMPANY_ID' ";
if($PROJECT_ID = get_input('PROJECT_ID') AND !empty($PROJECT_ID)) $wh[] = " P.PROJECT_ID = '$PROJECT_ID' ";
if($NAMA = get_input('NAMA') AND !empty($NAMA)) $wh[] = " UPPER(NAMA) LIKE UPPER('%$NAMA%') ";
if($NIK = get_input('NIK') AND !empty($NIK)) $wh[] = " UPPER(NIK) = UPPER('$NIK') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM karyawan A
	LEFT JOIN jabatan J ON (J.JABATAN_ID=A.JABATAN_ID)
	LEFT JOIN posisi PO ON (PO.POSISI_ID=A.POSISI_ID)
	LEFT JOIN project P ON (P.PROJECT_ID=J.PROJECT_ID)
	LEFT JOIN company C ON (C.COMPANY_ID=A.COMPANY_ID)
	{$where}
");
$NUM_ROWS = $rs->cnt;

$rs = db_fetch_limit("
	SELECT A.*, J.JABATAN, C.COMPANY, P.PROJECT, PO.POSISI
	FROM karyawan A
	LEFT JOIN jabatan J ON (J.JABATAN_ID=A.JABATAN_ID)
	LEFT JOIN posisi PO ON (PO.POSISI_ID=A.POSISI_ID)
	LEFT JOIN project P ON (P.PROJECT_ID=J.PROJECT_ID)
	LEFT JOIN company C ON (C.COMPANY_ID=A.COMPANY_ID)
	{$where}
	ORDER BY $SORT $ORDER
	", $PER_PAGE, $OFFSET);

//CEK STATUS by AGUNG

$KARYAWAN_ID_ARR = array();
foreach ($rs as $key => $a) {
	$KARYAWAN_ID_ARR[] = $a->KARYAWAN_ID;
}


function cek_1($id = null){
	$kondisi = "";
	if($id != null) $kondisi = "AND KARYAWAN_ID = '$id'";

	//cek tabel dok_karyawan
	$cek_1 = db_first("
		SELECT COUNT(1) as total
		FROM dok_karyawan
		WHERE (APPROVED = 'PENDING') $kondisi;
	");

	return $cek_1->total;
}

function cek_2($id = null){
	$kondisi = "";
	if($id != null) $kondisi = "AND KARYAWAN_ID = '$id'";
	//cek tabel dok_karyawan
	$cek_2 = db_first("
		SELECT COUNT(1) as total
		FROM organisasi_karyawan
		WHERE APPROVED = 'PENDING' $kondisi;
	");

	return $cek_2->total;
}

function cek_3($id = null){
	$kondisi = "";
	if($id != null) $kondisi = "AND KARYAWAN_ID = '$id'";
	//cek tabel pendidikan_karyawan
	$cek_3 = db_first("
		SELECT COUNT(1) as total
		FROM pendidikan_karyawan
		WHERE APPROVED = 'PENDING' $kondisi;
	");

	return $cek_3->total;
}

function cek_4($id = null){
	$kondisi = "";
	if($id != null) $kondisi = "AND KARYAWAN_ID = '$id'";
	//cek tabel kursus_karyawan
	$cek_4 = db_first("
		SELECT COUNT(1) as total
		FROM kursus_karyawan
		WHERE APPROVED = 'PENDING' $kondisi;
	");

	return $cek_4->total;
}

function cek_5($id = null){
	$kondisi = "";
	if($id != null) $kondisi = "AND KARYAWAN_ID = '$id'";
	//cek tabel bahasa_karyawan
	$cek_5 = db_first("
		SELECT COUNT(1) as total
		FROM bahasa_karyawan
		WHERE APPROVED = 'PENDING' $kondisi;
	");

	return $cek_5->total;
}

function cek_6($id = null){
	$kondisi = "";
	if($id != null) $kondisi = "AND KARYAWAN_ID = '$id'";
	//cek tabel keluarga_karyawan
	$cek_6 = db_first("
		SELECT COUNT(1) as total
		FROM keluarga_karyawan
		WHERE APPROVED = 'PENDING' $kondisi;
	");

	return $cek_6->total;
}


function cek_7($id = null){
	$kondisi = "";
	if($id != null) $kondisi = "AND KARYAWAN_ID = '$id'";
	//cek tabel pengalaman_karyawan
	$cek_7 = db_first("
		SELECT COUNT(1) as total
		FROM pengalaman_karyawan
		WHERE APPROVED = 'PENDING' $kondisi;
	");

	return $cek_7->total;
}

function cek_all($id = null){
	if($id != null) $cek_total = cek_1($id) + cek_2($id) + cek_3($id) + cek_4($id) + cek_5($id) + cek_6($id) + cek_7($id);

	return $cek_total;
}

// echo "<pre>";
// print_r($KARYAWAN_ID_ARR);
//echo arrayToString2($KARYAWAN_ID_ARR,",");die();

// echo "cek 1 : ".cek_1(19)."<br>";
// echo "cek 2 : ".cek_2(19)."<br>";
// echo "cek 3 : ".cek_3(19)."<br>";
// echo "cek 4 : ".cek_4(19)."<br>";
// echo "cek 5 : ".cek_5(19)."<br>";
// echo "cek 6 : ".cek_6(19)."<br>";
// echo "cek 7 : ".cek_7(19)."<br>";
//

// echo cek_all(19);
//  die();


$rows = $t = array();
$key = 0;
$no = 1;
if(count($rs)>0){ foreach($rs as $row){

	$join_date = strtotime($row->TGL_MASUK);
	$now= time();

	$diff= $now - $join_date;
	$diff= round($diff / (60 * 60 * 24));
	$status_join = "old";
	if($diff <= 30) $status_join = "new";
	
	$cek_approve = cek_all($row->KARYAWAN_ID);
	if($cek_approve > 0) $approved = '<span  class="badge badge-info status_approve"><i class="fa fa-plus"></i>'.$cek_approve.'</span>';
	else $approved = "";

	if($row->COMPLETED == 1) $completed = '<span  class="badge badge-success status_completes">Yes</span>';
	else if($row->COMPLETED == 0) $completed = '<span  class="badge badge-danger status_completes">No</span>';

	$t['no'] = $no++;
	$t['APPROVED'] = $approved;
	$t['COMPLETED'] = $completed;
	$t['diff'] = $status_join;


	$t['GAJI_POKOK'] = currency($row->GAJI_POKOK);
	$t['TGL_MASUK'] = tgl($row->TGL_MASUK);
	$rows[$key] = (object) array_merge((array) $row, $t);
	$key++;
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);
