<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$LOWONGAN_ID = get_input('lowongan');
$LAMARAN_ID = get_input('lamaran');
$CURRENT_ID = get_input('CURRENT_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	is_login('rekomendasi.edit');
	$LOWONGAN = db_first(" SELECT POSISI_ID FROM lowongan WHERE LOWONGAN_ID='$LOWONGAN_ID' ");
	db_execute (" INSERT INTO karyawan(NAMA, NAMA_PANGGILAN, JK, TP_LAHIR, TGL_LAHIR, KEWARGANEGARAAN, SUKU, AGAMA, GOL_DARAH, TINGGI, BERAT, UKURAN_BAJU, UKURAN_SEPATU, TELP, HP, EMAIL, NO_IDENTITAS, NPWP, BPJS_KESEHATAN, BPJS_KETENAGAKERJAAN, FC_KTP, FC_NPWP, FC_BPJS_KESEHATAN, FC_BPJS_KETENAGAKERJAAN, ST_KAWIN, PUNYA_KENDARAAN, JENIS_KENDARAAN, MILIK_KENDARAAN, ALAMAT, KELURAHAN, KECAMATAN, PROVINSI, KOTA, KODE_POS, RT, RW, ALAMAT_KTP, KELURAHAN_KTP, KECAMATAN_KTP, PROVINSI_KTP, KOTA_KTP, KODE_POS_KTP, RT_KTP, RW_KTP, TEMPAT_TINGGAL, TUGAS_JABATAN, JABATAN_ATASAN, JUMLAH_ANAK_BUAH, MASALAH_PENTING, HOBI, MOTIVASI_BEKERJA, MOTIVASI_DIAIRKON, EXPECTED_SALARY, FASILITAS_LAINNYA, KERABAT_AIRKON, NAMA_KERABAT, RIWAYAT_KESEHATAN, NAMA_PENYAKIT, RIWAYAT_RAWAT, SIAP_BEKERJA, LUAR_DAERAH, ALASAN_DILUAR_DAERAH, INFO_LOKER, INFO_LOKER_LAINNYA, FOTO, IJAZAH, CV, PASSWORD) SELECT NAMA, NAMA_PANGGILAN, JK, TP_LAHIR, TGL_LAHIR, KEWARGANEGARAAN, SUKU, AGAMA, GOL_DARAH, TINGGI, BERAT, UKURAN_BAJU, UKURAN_SEPATU, TELP, HP, EMAIL, NO_IDENTITAS, NPWP, BPJS_KESEHATAN, BPJS_KETENAGAKERJAAN, FC_KTP, FC_NPWP, FC_BPJS_KESEHATAN, FC_BPJS_KETENAGAKERJAAN, ST_KAWIN, PUNYA_KENDARAAN, JENIS_KENDARAAN, MILIK_KENDARAAN, ALAMAT, KELURAHAN, KECAMATAN, PROVINSI, KOTA, KODE_POS, RT, RW, ALAMAT_KTP, KELURAHAN_KTP, KECAMATAN_KTP, PROVINSI_KTP, KOTA_KTP, KODE_POS_KTP, RT_KTP, RW_KTP, TEMPAT_TINGGAL, TUGAS_JABATAN, JABATAN_ATASAN, JUMLAH_ANAK_BUAH, MASALAH_PENTING, HOBI, MOTIVASI_BEKERJA, MOTIVASI_DIAIRKON, EXPECTED_SALARY, FASILITAS_LAINNYA, KERABAT_AIRKON, NAMA_KERABAT, RIWAYAT_KESEHATAN, NAMA_PENYAKIT, RIWAYAT_RAWAT, SIAP_BEKERJA, LUAR_DAERAH, ALASAN_DILUAR_DAERAH, INFO_LOKER, INFO_LOKER_LAINNYA, FOTO, IJAZAH, CV, PASSWORD FROM calon_karyawan WHERE calon_karyawan.CALON_KARYAWAN_ID='$ID' ");
	$TGL = date('Y-m-d');
	$KARYAWAN_ID = $DB->Insert_ID();
	db_execute(" UPDATE lamaran SET STATUS_LAMARAN='FINAL' WHERE CALON_KARYAWAN_ID='$ID' ");
	db_execute(" UPDATE bahasa_karyawan SET KARYAWAN_ID='$KARYAWAN_ID' WHERE CALON_KARYAWAN_ID='$ID' ");
	db_execute(" UPDATE keluarga_karyawan SET KARYAWAN_ID='$KARYAWAN_ID' WHERE CALON_KARYAWAN_ID='$ID' ");
	db_execute(" UPDATE kursus_karyawan SET KARYAWAN_ID='$KARYAWAN_ID' WHERE CALON_KARYAWAN_ID='$ID' ");
	db_execute(" UPDATE organisasi_karyawan SET KARYAWAN_ID='$KARYAWAN_ID' WHERE CALON_KARYAWAN_ID='$ID' ");
	db_execute(" UPDATE penanggung_karyawan SET KARYAWAN_ID='$KARYAWAN_ID' WHERE CALON_KARYAWAN_ID='$ID' ");
	db_execute(" UPDATE pendidikan_karyawan SET KARYAWAN_ID='$KARYAWAN_ID' WHERE CALON_KARYAWAN_ID='$ID' ");
	db_execute(" UPDATE pengalaman_karyawan SET KARYAWAN_ID='$KARYAWAN_ID' WHERE CALON_KARYAWAN_ID='$ID' ");
	db_execute(" UPDATE karyawan SET CALON_KARYAWAN_ID='$ID' WHERE KARYAWAN_ID='$KARYAWAN_ID' ");
	//db_execute(" UPDATE karyawan SET POSISI_ID='$LOWONGAN->POSISI_ID' WHERE KARYAWAN_ID='$KARYAWAN_ID' ");
	//db_execute(" INSERT INTO histori_posisi (KARYAWAN_ID,POSISI_ID,KETERANGAN,TGL) VALUES ('$KARYAWAN_ID','$LOWONGAN->POSISI_ID','Bergabung','$TGL') ");
	header("location: karyawan-action.php?op=edit&id=$KARYAWAN_ID");
	exit;
}