<?php

include 'app-load.php';

$q = get_input('q');

$DATA = db_fetch(" SELECT K.*,P.POSISI,J.JABATAN FROM karyawan K LEFT JOIN posisi P ON P.POSISI_ID=K.POSISI_ID LEFT JOIN jabatan J ON J.JABATAN_ID=K.JABATAN_ID WHERE UCASE(NAMA) LIKE UCASE('$q%') AND ST_KERJA='AKTIF'");

$t = array();
if(count($DATA)>0)
{
	foreach($DATA as $key => $row){
		$t[$key]['id'] = $row->KARYAWAN_ID;
		$t[$key]['text'] = $row->NIK . ' - ' . $row->NAMA;
		$t[$key]['nik'] = $row->NIK;
		$t[$key]['posisi'] = $row->POSISI;
		$t[$key]['jabatan'] = $row->JABATAN;
	}
}

$res['results'] = $t;

echo json_encode($res);
