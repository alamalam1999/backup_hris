<?php

include 'app-load.php';


$id = get_input('id');
$tipe = get_input('tipe');



if( $tipe == "organisasi" )// tabel => organisasi_karyawan
{

	$q= " UPDATE organisasi_karyawan
				SET APPROVED='APPROVED'
				WHERE ORGANISASI_KARYAWAN_ID='$id'
				";
	db_execute($q);

}

else if( $tipe == "keluarga" )//tabel => keluarga_karyawan
{

	$q= " UPDATE keluarga_karyawan
				SET APPROVED='APPROVED'
				WHERE KELUARGA_KARYAWAN_ID='$id'
				";
	db_execute($q);

}


else if( $tipe == "kerja" )//tabel => pengalaman_karyawan
{

	$q= " UPDATE pengalaman_karyawan
				SET APPROVED='APPROVED'
				WHERE PENGALAMAN_KARYAWAN_ID='$id'
				";
	db_execute($q);

}

//PENDIDIKAN
else if( $tipe == "pendidikan_formal" )
{

	$q= " UPDATE pendidikan_karyawan
				SET APPROVED='APPROVED'
				WHERE PENDIDIKAN_KARYAWAN_ID='$id'
				";
	db_execute($q);

}

else if( $tipe == "pendidikan_kursus" )
{

	$q= " UPDATE kursus_karyawan
				SET APPROVED='APPROVED'
				WHERE KURSUS_KARYAWAN_ID='$id'
				";
	db_execute($q);

}

else if( $tipe == "pendidikan_bahasa" )
{

	$q= " UPDATE bahasa_karyawan
				SET APPROVED='APPROVED'
				WHERE BAHASA_KARYAWAN_ID='$id'
				";
	db_execute($q);

}

//DOKUMEN
else if( $tipe == "dok_ijazah" )
{

	$q= " UPDATE dok_karyawan
				SET APPROVED_IJAZAH='APPROVED'
				WHERE DOK_KARYAWAN_ID='$id'
				";
	db_execute($q);

}

else if( $tipe == "dok_sertifikat" )
{

	$q= " UPDATE dok_karyawan
				SET APPROVED_SERTIFIKAT='APPROVED'
				WHERE DOK_KARYAWAN_ID='$id'
				";
	db_execute($q);

}

else if( $tipe == "dok_sio" )
{

	$q= " UPDATE dok_karyawan
				SET APPROVED_SIO='APPROVED'
				WHERE DOK_KARYAWAN_ID='$id'
				";
	db_execute($q);

}

else if( $tipe == "dok_kta" )
{

	$q= " UPDATE dok_karyawan
				SET APPROVED_KTA='APPROVED'
				WHERE DOK_KARYAWAN_ID='$id'
				";
	db_execute($q);

}


//echo $q; die();

echo "sukses";
//var_dump($hasil);
