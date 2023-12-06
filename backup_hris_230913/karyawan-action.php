<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$TAB = get_input('tab');
$CURR_ID = get_input('CURR_ID');
if ($OP == 'edit' and empty($ID)) die('<p>Data tidak ditemukan.</p>');

if ($OP == 'edit') {
	is_login('karyawan.edit');
	$EDIT = db_first(" SELECT * FROM karyawan WHERE KARYAWAN_ID='$ID' ");
	$JABATAN = db_first("
		SELECT J.JABATAN,P.PROJECT,C.COMPANY
		FROM jabatan J
		LEFT JOIN project P ON P.PROJECT_ID=J.PROJECT_ID
		LEFT JOIN company C ON C.COMPANY_ID=P.COMPANY_ID
		WHERE J.JABATAN_ID='$EDIT->JABATAN_ID'
	");

	// DOKUMEN KARYAWAN
	$LIST_DOK_KARYAWAN = db_fetch("
		SELECT *
		FROM dok_karyawan DK
		WHERE DK.KARYAWAN_ID='$ID'
	");

	// HISTORI STATUS
	$HISTORI_STATUS = db_fetch(" SELECT * FROM histori_status WHERE KARYAWAN_ID='$ID' ORDER BY TGL DESC");

	// HISTORI JABATAN
	$HISTORI_JABATAN = db_fetch("
		SELECT HJ.*,J.JABATAN,P.PROJECT,C.COMPANY
		FROM histori_karir HJ
		LEFT JOIN jabatan J ON J.JABATAN_ID=HJ.JABATAN_ID
		LEFT JOIN project P ON P.PROJECT_ID=J.PROJECT_ID
		LEFT JOIN company C ON C.COMPANY_ID=P.COMPANY_ID
		WHERE HJ.KARYAWAN_ID='$ID' ORDER BY TGL DESC
	");

	// HISTORI POSISI
	$HISTORI_POSISI = db_fetch("
		SELECT HP.*,P.POSISI
		FROM histori_posisi HP
		LEFT JOIN posisi P ON P.POSISI_ID=HP.POSISI_ID
		WHERE HP.KARYAWAN_ID='$ID' ORDER BY TGL DESC
	");

	// HISTORI GAJI
	$HISTORI_GAJI = db_fetch(" SELECT * FROM histori_gaji WHERE KARYAWAN_ID='$ID' ORDER BY TGL DESC");

	// HISTORI EQUIPMENT
	$HISTORI_EQUIPMENT = db_fetch("
		SELECT EU.TANGGAL_TERIMA,E.NAMA,EUD.QTY
		FROM equipment_used EU
		LEFT JOIN equipment_used_detail EUD ON EUD.EQUIPMENT_USED_ID=EU.EQUIPMENT_USED_ID
		LEFT JOIN equipment E ON E.EQUIPMENT_ID=EUD.EQUIPMENT_ID
		WHERE EU.KARYAWAN_ID='$ID' ORDER BY TANGGAL_TERIMA DESC
	");

	// HISTORI SP
	$HISTORI_SP = db_fetch("
		SELECT SP.TANGGAL,SP.SANKSI,SP.KETERANGAN
		FROM sp SP
		WHERE SP.KARYAWAN_ID='$ID' ORDER BY TANGGAL DESC
	");

	// POSISI AWAL SAAT MELAMAR
	$POSISI = db_first(" SELECT P.POSISI FROM lamaran L LEFT JOIN posisi P ON (P.POSISI_ID=L.POSISI_ID) WHERE L.CALON_KARYAWAN_ID='$EDIT->CALON_KARYAWAN_ID' ");
}

if ($OP == 'delete') {
	is_login('karyawan.delete');
	$IDS = get_input('ids');
	if (is_array($IDS)) {
		db_execute(" DELETE FROM karyawan WHERE KARYAWAN_ID IN (" . implode(',', $IDS) . ")");
		db_execute(" UPDATE bahasa_karyawan SET KARYAWAN_ID='0' WHERE KARYAWAN_ID IN (" . implode(',', $IDS) . ") ");
		db_execute(" UPDATE keluarga_karyawan SET KARYAWAN_ID='0' WHERE KARYAWAN_ID IN (" . implode(',', $IDS) . ") ");
		db_execute(" UPDATE kursus_karyawan SET KARYAWAN_ID='0' WHERE KARYAWAN_ID IN (" . implode(',', $IDS) . ") ");
		db_execute(" UPDATE organisasi_karyawan SET KARYAWAN_ID='0' WHERE KARYAWAN_ID IN (" . implode(',', $IDS) . ") ");
		db_execute(" UPDATE penanggung_karyawan SET KARYAWAN_ID='0' WHERE KARYAWAN_ID IN (" . implode(',', $IDS) . ") ");
		db_execute(" UPDATE pendidikan_karyawan SET KARYAWAN_ID='' WHERE KARYAWAN_ID IN (" . implode(',', $IDS) . ") ");
		db_execute(" UPDATE pengalaman_karyawan SET KARYAWAN_ID='' WHERE KARYAWAN_ID IN (" . implode(',', $IDS) . ") ");
		db_execute(" DELETE FROM histori_gaji WHERE KARYAWAN_ID IN (" . implode(',', $IDS) . ") ");
		db_execute(" DELETE FROM histori_karir WHERE KARYAWAN_ID IN (" . implode(',', $IDS) . ") ");
		db_execute(" DELETE FROM histori_posisi WHERE KARYAWAN_ID IN (" . implode(',', $IDS) . ") ");
		db_execute(" DELETE FROM histori_sp WHERE KARYAWAN_ID IN (" . implode(',', $IDS) . ") ");
		db_execute(" DELETE FROM histori_status WHERE KARYAWAN_ID IN (" . implode(',', $IDS) . ") ");
	}
	header('location: karyawan.php');
	exit;
}

if ($OP == 'edit_pasif') {
	is_login('karyawan-pasif.edit');
	$DATE = date('Y-m-d');
	db_execute(" UPDATE karyawan SET ST_KERJA='AKTIF', TGL_MASUK='$DATE' WHERE KARYAWAN_ID='$ID' ");
	db_execute(" INSERT INTO histori_status (KARYAWAN_ID,HISTORI_STATUS,KETERANGAN,TGL) VALUES ('$ID','AKTIF','REHIRE','$DATE') ");
	header('location: karyawan-pasif.php');
	exit;
}

if ($OP == 'delete_pasif') {
	is_login('karyawan-pasif.delete');
	db_execute(" DELETE FROM karyawan WHERE KARYAWAN_ID='$ID' ");
	header('location: karyawan-pasif.php');
	exit;
}

/* generate dummy password */
/*
if ($OP == 'generate_password') {
	is_login('karyawan.edit');

	$GENERATE_PASSWORD = db_fetch("
		SELECT KARYAWAN_ID, TGL_LAHIR
		FROM karyawan 
		WHERE IS_ACTIVE = 0 AND TGL_LAHIR != '0000-00-00'
	");

	if (count($GENERATE_PASSWORD) > 0) {
		foreach ($GENERATE_PASSWORD as $row) {
			$NEW_PASSWORD = password_hash($row->TGL_LAHIR, PASSWORD_DEFAULT);
			db_execute(" 
				UPDATE karyawan SET PASSWORD = '$NEW_PASSWORD', IS_ACTIVE = 1 
				WHERE KARYAWAN_ID = '$row->KARYAWAN_ID' 
			");
		}
	}
	header('location: karyawan.php');
	exit;
}
/* end of generate dummy password */

is_login('karyawan.add');
if (isset($_SERVER['REQUEST_METHOD']) and strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
	$UPDATE_TYPE = get_input('UPDATE_TYPE');

	//update data identitas diri
	if ($UPDATE_TYPE == 'DATA_PELAMAR') {

		$foto_allow_ext = array('png', 'jpg');
		$foto_name = isset($_FILES['FOTO']['name']) ? $_FILES['FOTO']['name'] : '';
		$foto_tmp = isset($_FILES['FOTO']['tmp_name']) ? $_FILES['FOTO']['tmp_name'] : '';
		$foto_ext = strtolower(substr(strrchr($foto_name, "."), 1));
		$foto_new = rand(11111, 99999) . '_' . $foto_name;
		$foto_dest = 'uploads/foto/' . $foto_new;

		$scan_ijazah_allow_ext = array('png', 'jpg', 'pdf');
		$scan_ijazah_name = isset($_FILES['FILE_SCAN_IJAZAH']['name']) ? $_FILES['FILE_SCAN_IJAZAH']['name'] : '';
		$scan_ijazah_tmp = isset($_FILES['FILE_SCAN_IJAZAH']['tmp_name']) ? $_FILES['FILE_SCAN_IJAZAH']['tmp_name'] : '';
		$scan_ijazah_ext = strtolower(substr(strrchr($scan_ijazah_name, "."), 1));
		$scan_ijazah_new = rand(11111, 99999) . '_' . $scan_ijazah_name;
		$scan_ijazah_dest = 'uploads/ijazah/' . $scan_ijazah_new;

		$cv_allow_ext = array('doc', 'docx', 'pdf');
		$cv_name = isset($_FILES['CV']['name']) ? $_FILES['CV']['name'] : '';
		$cv_tmp = isset($_FILES['CV']['tmp_name']) ? $_FILES['CV']['tmp_name'] : '';
		$cv_ext = strtolower(substr(strrchr($cv_name, "."), 1));
		$cv_new = rand(11111, 99999) . '_' . $cv_name;
		$cv_dest = 'uploads/cv/' . $cv_new;

		$fcktp_allow_ext = array('png', 'jpg', 'pdf');
		$fcktp_name = isset($_FILES['FC_KTP']['name']) ? $_FILES['FC_KTP']['name'] : '';
		$fcktp_tmp = isset($_FILES['FC_KTP']['tmp_name']) ? $_FILES['FC_KTP']['tmp_name'] : '';
		$fcktp_ext = strtolower(substr(strrchr($fcktp_name, "."), 1));
		$fcktp_new = rand(11111, 99999) . '_' . $fcktp_name;
		$fcktp_dest = 'uploads/cv/' . $fcktp_new;

		$fcnpwp_allow_ext = array('png', 'jpg', 'pdf');
		$fcnpwp_name = isset($_FILES['FC_NPWP']['name']) ? $_FILES['FC_NPWP']['name'] : '';
		$fcnpwp_tmp = isset($_FILES['FC_NPWP']['tmp_name']) ? $_FILES['FC_NPWP']['tmp_name'] : '';
		$fcnpwp_ext = strtolower(substr(strrchr($fcnpwp_name, "."), 1));
		$fcnpwp_new = rand(11111, 99999) . '_' . $fcnpwp_name;
		$fcnpwp_dest = 'uploads/cv/' . $fcnpwp_new;

		$fcbpjs_kes_allow_ext = array('png', 'jpg', 'pdf');
		$fcbpjs_kes_name = isset($_FILES['FC_BPJS_KESEHATAN']['name']) ? $_FILES['FC_BPJS_KESEHATAN']['name'] : '';
		$fcbpjs_kes_tmp = isset($_FILES['FC_BPJS_KESEHATAN']['tmp_name']) ? $_FILES['FC_BPJS_KESEHATAN']['tmp_name'] : '';
		$fcbpjs_kes_ext = strtolower(substr(strrchr($fcbpjs_kes_name, "."), 1));
		$fcbpjs_kes_new = rand(11111, 99999) . '_' . $fcbpjs_kes_name;
		$fcbpjs_kes_dest = 'uploads/cv/' . $fcbpjs_kes_new;

		$fcbpjs_ket_allow_ext = array('png', 'jpg', 'pdf');
		$fcbpjs_ket_name = isset($_FILES['FC_BPJS_KETENAGAKERJAAN']['name']) ? $_FILES['FC_BPJS_KETENAGAKERJAAN']['name'] : '';
		$fcbpjs_ket_tmp = isset($_FILES['FC_BPJS_KETENAGAKERJAAN']['tmp_name']) ? $_FILES['FC_BPJS_KETENAGAKERJAAN']['tmp_name'] : '';
		$fcbpjs_ket_ext = strtolower(substr(strrchr($fcbpjs_ket_name, "."), 1));
		$fcbpjs_ket_new = rand(11111, 99999) . '_' . $fcbpjs_ket_name;
		$fcbpjs_ket_dest = 'uploads/cv/' . $fcbpjs_ket_new;

		$fckk_allow_ext = array('png', 'jpg', 'pdf');
		$fckk_name = isset($_FILES['FILE_KK']['name']) ? $_FILES['FILE_KK']['name'] : '';
		$fckk_tmp = isset($_FILES['FILE_KK']['tmp_name']) ? $_FILES['FILE_KK']['tmp_name'] : '';
		$fckk_ext = strtolower(substr(strrchr($fckk_name, "."), 1));
		$fckk_new = rand(11111, 99999) . '_' . $fckk_name;
		$fckk_dest = 'uploads/cv/' . $fckk_new;

		$CREATED_ON = date('Y-m-d H:i:s');
		$TAB = 'tab1';

		/* Tambah karyawan baru */
		if ($OP == 'add') {
			$REQUIRE = array('NAMA', 'KARYAWAN_ID', 'TGL_MASUK');
			$ERROR_REQUIRE = 0;
			foreach ($REQUIRE as $REQ) {
				$IREQ = get_input($REQ);
				if ($IREQ == "") $ERROR_REQUIRE = 1;
			}

			if ($ERROR_REQUIRE) {
				$ERROR[] = 'Kolom <strong>' . implode(', ', $REQUIRE) . '</strong> wajib di isi.';
			} else if ((db_exists(" SELECT 1 FROM karyawan WHERE KARYAWAN_ID='" . db_escape(get_input('KARYAWAN_ID')) . "' "))) {
				$ERROR[] = 'PIN sudah terdaftar, silakan gunakan <strong>PIN/ID</strong> lain';
			} else {
				$FIELDS = array(
					'KARYAWAN_ID', 'NAMA', 'NAMA_PANGGILAN', 'JK', 'TP_LAHIR', 'TGL_LAHIR', 'KEWARGANEGARAAN', 'SUKU', 'AGAMA', 'GOL_DARAH', 'TINGGI', 'BERAT', 'UKURAN_BAJU', 'UKURAN_SEPATU', 'TELP', 'HP', 'EMAIL', 'PASSWORD', 'NO_IDENTITAS', 'NPWP', 'BPJS_KESEHATAN',
					'BPJS_KETENAGAKERJAAN', 'ST_KAWIN', 'PUNYA_KENDARAAN', 'JENIS_KENDARAAN', 'MILIK_KENDARAAN', 'ALAMAT', 'KELURAHAN', 'KECAMATAN', 'PROVINSI', 'KOTA', 'KODE_POS', 'RT', 'RW', 'ALAMAT_KTP', 'KELURAHAN_KTP', 'KECAMATAN_KTP', 'PROVINSI_KTP', 'KOTA_KTP', 'KODE_POS_KTP', 'RT_KTP',
					'RW_KTP', 'TEMPAT_TINGGAL', 'SCAN_IJAZAH', 'STRUKTUR_ID',
					'LONGITUDE', 'LATITUDE', 'COMPLETED', 'IS_ACTIVE', 'TGL_MASUK', 'NO_KK',
				);

				$NEW_FOTO = 0;
				if (is_uploaded_file($foto_tmp)) {
					if (move_uploaded_file($foto_tmp, $foto_dest)) {
						$FIELDS[] = 'FOTO';
						$NEW_FOTO = 1;
					}
				}
				$NEW_SCAN_IJAZAH = 0;
				if (is_uploaded_file($scan_ijazah_tmp)) {
					if (move_uploaded_file($scan_ijazah_tmp, $scan_ijazah_dest)) {
						$FIELDS[] = 'IJAZAH';
						$NEW_SCAN_IJAZAH = 1;
					}
				}
				$NEW_CV = 0;
				if (is_uploaded_file($cv_tmp)) {
					if (move_uploaded_file($cv_tmp, $cv_dest)) {
						$FIELDS[] = 'CV';
						$NEW_CV = 1;
					}
				}
				$NEW_FCKTP = 0;
				if (is_uploaded_file($fcktp_tmp)) {
					if (move_uploaded_file($fcktp_tmp, $fcktp_dest)) {
						$FIELDS[] = 'FC_KTP';
						$NEW_FCKTP = 1;
					}
				}
				$NEW_FCNPWP = 0;
				if (is_uploaded_file($fcnpwp_tmp)) {
					if (move_uploaded_file($fcnpwp_tmp, $fcnpwp_dest)) {
						$FIELDS[] = 'FC_NPWP';
						$NEW_FCNPWP = 1;
					}
				}
				$NEW_FCBPJS_KESEHATAN = 0;
				if (is_uploaded_file($fcbpjs_kes_tmp)) {
					if (move_uploaded_file($fcbpjs_kes_tmp, $fcbpjs_kes_dest)) {
						$FIELDS[] = 'FC_BPJS_KESEHATAN';
						$NEW_FCBPJS = 1;
					}
				}
				$NEW_FCBPJS_KETENAGAKERJAAN = 0;
				if (is_uploaded_file($fcbpjs_ket_tmp)) {
					if (move_uploaded_file($fcbpjs_ket_tmp, $fcbpjs_ket_dest)) {
						$FIELDS[] = 'FC_BPJS_KETENAGAKERJAAN';
						$NEW_FCBPJS = 1;
					}
				}

				$NEW_FCKK = 0;
				if (is_uploaded_file($fckk_tmp)) {
					if (move_uploaded_file($fckk_tmp, $fckk_dest)) {
						$FIELDS[] = 'FILE_KK';
						$NEW_FCKK = 1;
					}
				}

				$d = array();
				foreach ($FIELDS as $F) {
					if ($F == 'FOTO') {
						if ($NEW_FOTO == '1') {
							$INSERT_VAL[$F] = "'" . db_escape($foto_new) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape($foto_new) . "'";
						} else {
							$INSERT_VAL[$F] = "'" . db_escape(get_input('CURRENT_FOTO')) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape(get_input('CURRENT_FOTO')) . "'";
						}
					} else if ($F == 'IJAZAH') {
						if ($NEW_IJAZAH == '1') {
							$INSERT_VAL[$F] = "'" . db_escape($scan_ijazah_new) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape($scan_ijazah_new) . "'";
						} else {
							$INSERT_VAL[$F] = "'" . db_escape(get_input('CURRENT_FILE_SCAN_IJAZAH')) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape(get_input('CURRENT_FILE_SCAN_IJAZAH')) . "'";
						}
					} else if ($F == 'CV') {
						if ($NEW_CV == '1') {
							$INSERT_VAL[$F] = "'" . db_escape($cv_new) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape($cv_new) . "'";
						} else {
							$INSERT_VAL[$F] = "'" . db_escape(get_input('CURRENT_CV')) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape(get_input('CURRENT_CV')) . "'";
						}
					} else if ($F == 'FC_KTP') {
						if ($NEW_FCKTP == '1') {
							$INSERT_VAL[$F] = "'" . db_escape($fcktp_new) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape($fcktp_new) . "'";
						} else {
							$INSERT_VAL[$F] = "'" . db_escape(get_input('CURRENT_FCKTP')) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape(get_input('CURRENT_FCKTP')) . "'";
						}
					} else if ($F == 'FC_NPWP') {
						if ($NEW_FCNPWP == '1') {
							$INSERT_VAL[$F] = "'" . db_escape($fcnpwp_new) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape($fcnpwp_new) . "'";
						} else {
							$INSERT_VAL[$F] = "'" . db_escape(get_input('CURRENT_FCNPWP')) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape(get_input('CURRENT_FCNPWP')) . "'";
						}
					} else if ($F == 'FC_BPJS_KESEHATAN') {
						if ($NEW_FCBPJS == '1') {
							$INSERT_VAL[$F] = "'" . db_escape($fcbpjs_kes_new) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape($fcbpjs_kes_new) . "'";
						} else {
							$INSERT_VAL[$F] = "'" . db_escape(get_input('CURRENT_FCBPJS_KESEHATAN')) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape(get_input('CURRENT_FCBPJS_KESEHATAN')) . "'";
						}
					} else if ($F == 'FC_BPJS_KETENAGAKERJAAN') {
						if ($NEW_FCBPJS == '1') {
							$INSERT_VAL[$F] = "'" . db_escape($fcbpjs_ket_new) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape($fcbpjs_ket_new) . "'";
						} else {
							$INSERT_VAL[$F] = "'" . db_escape(get_input('CURRENT_FCBPJS_KETENAGAKERJAAN')) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape(get_input('CURRENT_FCBPJS_KETENAGAKERJAAN')) . "'";
						}
					} else if ($F == 'FILE_KK') {
						if ($NEW_FCKK == '1') {
							$INSERT_VAL[$F] = "'" . db_escape($fckk_new) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape($fckk_new) . "'";
						} else {
							$INSERT_VAL[$F] = "'" . db_escape(get_input('CURRENT_FILE_KK')) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape(get_input('CURRENT_FILE_KK')) . "'";
						}
					} else if ($F == 'PASSWORD') {
						$_PASSWORD = get_input('PASSWORD');
						$_NEW_PASSWORD = password_hash($_PASSWORD, PASSWORD_DEFAULT);
						$INSERT_VAL[$F] = "'" . db_escape($_NEW_PASSWORD) . "'";
						$UPDATE_VAL[$F] = $F . "='" . db_escape($_NEW_PASSWORD) . "'";
					} else if ($F == 'NAMA') {
						$FULLNAME = strtoupper(get_input('NAMA'));
						$INSERT_VAL[$F] = "'" . db_escape($FULLNAME) . "'";
						$UPDATE_VAL[$F] = $F . "='" . db_escape($FULLNAME) . "'";
					} else {
						$INSERT_VAL[$F] = "'" . db_escape(get_input($F)) . "'";
						$UPDATE_VAL[$F] = $F . "='" . db_escape(get_input($F)) . "'";
					}
				}

				$TGL_ST_KERJA = get_input('TGL_MASUK');

				$SQL = db_execute(" INSERT INTO karyawan (" . implode(',', $FIELDS) . ") VALUES (" . implode(',', $INSERT_VAL) . ") ");

				$OP = 'edit';
				$ID = $DB->Insert_ID();

				db_execute(" INSERT INTO histori_status (KARYAWAN_ID,HISTORI_STATUS,KETERANGAN,TGL) VALUES ('$ID','AKTIF','JOIN','$TGL_ST_KERJA') ");

				header('location: ' . $_SERVER['PHP_SELF'] . '?op=edit&m=1&id=' . $ID . '&tab=' . $TAB);
				exit;
			}
		} else {
			$REQUIRE = array('NAMA');
			$ERROR_REQUIRE = 0;
			foreach ($REQUIRE as $REQ) {
				$IREQ = get_input($REQ);
				if ($IREQ == "") $ERROR_REQUIRE = 1;
			}
			$ERROR = array();
			if ($ERROR_REQUIRE) {
				$ERROR[] = 'Kolom ' . implode(',', $REQUIRE) . ' wajib di isi.';
			} else {
				$FIELDS = array(
					'NAMA', 'NAMA_PANGGILAN', 'JK', 'TP_LAHIR', 'TGL_LAHIR', 'KEWARGANEGARAAN', 'SUKU', 'AGAMA', 'GOL_DARAH', 'TINGGI', 'BERAT', 'UKURAN_BAJU', 'UKURAN_SEPATU', 'TELP', 'HP', 'EMAIL', 'PASSWORD', 'NO_IDENTITAS', 'NPWP', 'BPJS_KESEHATAN', 'BPJS_KETENAGAKERJAAN',
					'ST_KAWIN', 'PUNYA_KENDARAAN', 'JENIS_KENDARAAN', 'MILIK_KENDARAAN', 'ALAMAT', 'KELURAHAN', 'KECAMATAN', 'PROVINSI', 'KOTA', 'KODE_POS', 'RT', 'RW', 'ALAMAT_KTP', 'KELURAHAN_KTP', 'KECAMATAN_KTP', 'PROVINSI_KTP', 'KOTA_KTP', 'KODE_POS_KTP', 'RT_KTP', 'RW_KTP',
					'TEMPAT_TINGGAL', 'SCAN_IJAZAH', 'STRUKTUR_ID',
					'LONGITUDE', 'LATITUDE', 'COMPLETED', 'IS_ACTIVE', 'NO_KK'
				);

				$NEW_FOTO = 0;
				if (is_uploaded_file($foto_tmp)) {
					if (move_uploaded_file($foto_tmp, $foto_dest)) {
						$FIELDS[] = 'FOTO';
						$NEW_FOTO = 1;
					}
				}
				$NEW_IJAZAH = 0;
				if (is_uploaded_file($scan_ijazah_tmp)) {
					if (move_uploaded_file($scan_ijazah_tmp, $scan_ijazah_dest)) {
						$FIELDS[] = 'IJAZAH';
						$NEW_IJAZAH = 1;
					}
				}
				$NEW_CV = 0;
				if (is_uploaded_file($cv_tmp)) {
					if (move_uploaded_file($cv_tmp, $cv_dest)) {
						$FIELDS[] = 'CV';
						$NEW_CV = 1;
					}
				}
				$NEW_FCKTP = 0;
				if (is_uploaded_file($fcktp_tmp)) {
					if (move_uploaded_file($fcktp_tmp, $fcktp_dest)) {
						$FIELDS[] = 'FC_KTP';
						$NEW_FCKTP = 1;
					}
				}
				$NEW_FCNPWP = 0;
				if (is_uploaded_file($fcnpwp_tmp)) {
					if (move_uploaded_file($fcnpwp_tmp, $fcnpwp_dest)) {
						$FIELDS[] = 'FC_NPWP';
						$NEW_FCNPWP = 1;
					}
				}
				$NEW_FCBPJS_KESEHATAN = 0;
				if (is_uploaded_file($fcbpjs_kes_tmp)) {
					if (move_uploaded_file($fcbpjs_kes_tmp, $fcbpjs_kes_dest)) {
						$FIELDS[] = 'FC_BPJS_KESEHATAN';
						$NEW_FCBPJS = 1;
					}
				}
				$NEW_FCBPJS_KETENAGAKERJAAN = 0;
				if (is_uploaded_file($fcbpjs_ket_tmp)) {
					if (move_uploaded_file($fcbpjs_ket_tmp, $fcbpjs_ket_dest)) {
						$FIELDS[] = 'FC_BPJS_KETENAGAKERJAAN';
						$NEW_FCBPJS = 1;
					}
				}
				$NEW_FILE_KK = 0;
				if (is_uploaded_file($fckk_tmp)) {
					if (move_uploaded_file($fckk_tmp, $fckk_dest)) {
						$FIELDS[] = 'FILE_KK';
						$NEW_FCKK = 1;
					}
				}

				$d = array();
				foreach ($FIELDS as $F) {
					if ($F == 'FOTO') {
						if ($NEW_FOTO == '1') {
							$INSERT_VAL[$F] = "'" . db_escape($foto_new) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape($foto_new) . "'";
						} else {
							$INSERT_VAL[$F] = "'" . db_escape(get_input('CURRENT_FOTO')) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape(get_input('CURRENT_FOTO')) . "'";
						}
					} else if ($F == 'IJAZAH') {
						if ($NEW_IJAZAH == '1') {
							$INSERT_VAL[$F] = "'" . db_escape($scan_ijazah_new) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape($scan_ijazah_new) . "'";
						} else {
							$INSERT_VAL[$F] = "'" . db_escape(get_input('CURRENT_FILE_SCAN_IJAZAH')) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape(get_input('CURRENT_FILE_SCAN_IJAZAH')) . "'";
						}
					} else if ($F == 'CV') {
						if ($NEW_CV == '1') {
							$INSERT_VAL[$F] = "'" . db_escape($cv_new) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape($cv_new) . "'";
						} else {
							$INSERT_VAL[$F] = "'" . db_escape(get_input('CURRENT_CV')) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape(get_input('CURRENT_CV')) . "'";
						}
					} else if ($F == 'FC_KTP') {
						if ($NEW_FCKTP == '1') {
							$INSERT_VAL[$F] = "'" . db_escape($fcktp_new) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape($fcktp_new) . "'";
						} else {
							$INSERT_VAL[$F] = "'" . db_escape(get_input('CURRENT_FCKTP')) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape(get_input('CURRENT_FCKTP')) . "'";
						}
					} else if ($F == 'FC_NPWP') {
						if ($NEW_FCNPWP == '1') {
							$INSERT_VAL[$F] = "'" . db_escape($fcnpwp_new) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape($fcnpwp_new) . "'";
						} else {
							$INSERT_VAL[$F] = "'" . db_escape(get_input('CURRENT_FCNPWP')) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape(get_input('CURRENT_FCNPWP')) . "'";
						}
					} else if ($F == 'FC_BPJS_KESEHATAN') {
						if ($NEW_FCBPJS == '1') {
							$INSERT_VAL[$F] = "'" . db_escape($fcbpjs_kes_new) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape($fcbpjs_kes_new) . "'";
						} else {
							$INSERT_VAL[$F] = "'" . db_escape(get_input('CURRENT_FCBPJS_KESEHATAN')) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape(get_input('CURRENT_FCBPJS_KESEHATAN')) . "'";
						}
					} else if ($F == 'FC_BPJS_KETENAGAKERJAAN') {
						if ($NEW_FCBPJS == '1') {
							$INSERT_VAL[$F] = "'" . db_escape($fcbpjs_ket_new) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape($fcbpjs_ket_new) . "'";
						} else {
							$INSERT_VAL[$F] = "'" . db_escape(get_input('CURRENT_FCBPJS_KETENAGAKERJAAN')) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape(get_input('CURRENT_FCBPJS_KETENAGAKERJAAN')) . "'";
						}
					} else if ($F == 'FILE_KK') {
						if ($NEW_FCKK == '1') {
							$INSERT_VAL[$F] = "'" . db_escape($fckk_new) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape($fckk_new) . "'";
						} else {
							$INSERT_VAL[$F] = "'" . db_escape(get_input('CURRENT_FILE_KK')) . "'";
							$UPDATE_VAL[$F] = $F . "='" . db_escape(get_input('CURRENT_FILE_KK')) . "'";
						}
					} else if ($F == 'PASSWORD') {
						$_PASSWORD = get_input('PASSWORD');
						$_NEW_PASSWORD = password_hash($_PASSWORD, PASSWORD_DEFAULT);
						$INSERT_VAL[$F] = "'" . db_escape($_NEW_PASSWORD) . "'";
						$UPDATE_VAL[$F] = $F . "='" . db_escape($_NEW_PASSWORD) . "'";
					} else if ($F == 'NAMA') {
						$FULLNAME = strtoupper(get_input('NAMA'));
						$INSERT_VAL[$F] = "'" . db_escape($FULLNAME) . "'";
						$UPDATE_VAL[$F] = $F . "='" . db_escape($FULLNAME) . "'";
					} else {
						$INSERT_VAL[$F] = "'" . db_escape(get_input($F)) . "'";
						$UPDATE_VAL[$F] = $F . "='" . db_escape(get_input($F)) . "'";
					}
				}
			}

			$SQL = db_execute(" UPDATE karyawan SET " . implode(',', $UPDATE_VAL) . " WHERE KARYAWAN_ID='$ID' ");

			header('location: ' . $_SERVER['PHP_SELF'] . '?op=edit&m=1&id=' . $ID . '&tab=' . $TAB);
			exit;
		}
	}

	//update data setup
	if ($UPDATE_TYPE == 'DATA_SETUP') {

		$REQUIRE = array('NIK');
		$ERROR_REQUIRE = 0;
		foreach ($REQUIRE as $REQ) {
			$IREQ = get_input($REQ);
			if ($IREQ == "") $ERROR_REQUIRE = 1;
		}

		if ($ERROR_REQUIRE) {
			$ERROR[] = 'Kolom ' . implode(',', $REQUIRE) . ' wajib di isi.';
		} else if ((get_input('NIK') != $EDIT->NIK) and db_exists(" SELECT 1 FROM karyawan WHERE NIK='" . db_escape(get_input('NIK')) . "' ")) {
			$ERROR[] = 'Nik sudah terdaftar, silakan gunakan Nik lain';
		} else {
			$NIK = db_escape(get_input('NIK'));
			$TGL_MASUK = db_escape(get_input('TGL_MASUK'));
			$TGL_SK = db_escape(get_input('TGL_SK'));
			$TGL_MULAI_KONTRAK = db_escape(get_input('TGL_MULAI_KONTRAK'));
			$TGL_SELESAI_KONTRAK = db_escape(get_input('TGL_SELESAI_KONTRAK'));
			$NO_KONTRAK = db_escape(get_input('NO_KONTRAK'));
			$JENIS = db_escape(get_input('JENIS'));
			$TIPE = db_escape(get_input('TIPE'));
			$STATUS_PTKP = db_escape(get_input('STATUS_PTKP'));
			$BPJS_JHT = db_escape(input_currency(get_input('BPJS_JHT')));
			$BPJS_JP = db_escape(input_currency(get_input('BPJS_JP')));
			$BPJS_KES = db_escape(input_currency(get_input('BPJS_KES')));
			$BPJS_JHT_PERUSAHAAN = db_escape(input_currency(get_input('BPJS_JHT_PERUSAHAAN')));
			$BPJS_JP_PERUSAHAAN = db_escape(input_currency(get_input('BPJS_JP_PERUSAHAAN')));
			$BPJS_KES_PERUSAHAAN = db_escape(input_currency(get_input('BPJS_KES_PERUSAHAAN')));
			$BPJS_JKK = db_escape(input_currency(get_input('BPJS_JKK')));
			$BPJS_JKM = db_escape(input_currency(get_input('BPJS_JKM')));
			$PPH21 = db_escape(input_currency(get_input('PPH21')));
			$R_BPJS_JHT = db_escape(get_input('R_BPJS_JHT'));
			$R_BPJS_JP = db_escape(get_input('R_BPJS_JP'));
			$R_BPJS_JKK = db_escape(get_input('R_BPJS_JKK'));
			$R_BPJS_JKM = db_escape(get_input('R_BPJS_JKM'));
			$R_BPJS_KES = db_escape(get_input('R_BPJS_KES'));
			$LULUSAN = db_escape(get_input('LULUSAN'));
			$JURUSAN_ = db_escape(get_input('JURUSAN_'));
			$TAHUN_LULUS = db_escape(get_input('TAHUN_LULUS'));
			$PENGALAMAN = db_escape(get_input('PENGALAMAN'));
			$IBU_KANDUNG = db_escape(get_input('IBU_KANDUNG'));

			$NAMA_BANK = db_escape(get_input('NAMA_BANK'));
			$AKUN_BANK = db_escape(get_input('AKUN_BANK'));
			$NO_REKENING = db_escape(get_input('NO_REKENING'));
			$STRUKTUR_ID = db_escape(get_input('STRUKTUR_ID'));
			$JOBDESC = db_escape(get_input('JOBDESC'));

			$TAB = 'tab2';
			$SQL = db_execute(" UPDATE karyawan SET PPH21='$PPH21',NIK='$NIK',TGL_MASUK='$TGL_MASUK',TGL_MULAI_KONTRAK='$TGL_MULAI_KONTRAK',TGL_SELESAI_KONTRAK='$TGL_SELESAI_KONTRAK',TGL_SK='$TGL_SK',NO_KONTRAK='$NO_KONTRAK',JENIS='$JENIS',TIPE='$TIPE',STATUS_PTKP='$STATUS_PTKP',BPJS_JHT='$BPJS_JHT',BPJS_JKM='$BPJS_JKM',BPJS_JKK='$BPJS_JKK',BPJS_JP='$BPJS_JP',BPJS_KES='$BPJS_KES',NAMA_BANK='$NAMA_BANK',AKUN_BANK='$AKUN_BANK',NO_REKENING='$NO_REKENING',JOBDESC='$JOBDESC',STRUKTUR_ID='$STRUKTUR_ID', R_BPJS_JHT='$R_BPJS_JHT', R_BPJS_JP='$R_BPJS_JP', R_BPJS_JKK='$R_BPJS_JKK', R_BPJS_JKM='$R_BPJS_JKM', R_BPJS_KES='$R_BPJS_KES',LULUSAN='$LULUSAN',JURUSAN='$JURUSAN_',TAHUN_LULUS='$TAHUN_LULUS',PENGALAMAN='$PENGALAMAN',IBU_KANDUNG='$IBU_KANDUNG' WHERE KARYAWAN_ID='$ID' ");
			header('location: ' . $_SERVER['PHP_SELF'] . '?op=edit&m=1&id=' . $ID . '&tab=' . $TAB);
			exit;
		}
	}

	// update data karir
	if ($UPDATE_TYPE == 'DATA_KARIR') {
		$ST_KERJA = db_escape(get_input('ST_KERJA'));
		$TGL_ST_KERJA = db_escape(get_input('TGL_ST_KERJA'));
		$KET_ST_KERJA = db_escape(get_input('KET_ST_KERJA'));

		$JABATAN_ID = db_escape(get_input('JABATAN_ID'));
		$TGL_JABATAN = db_escape(get_input('TGL_JABATAN'));
		$KET_JABATAN = db_escape(get_input('KET_JABATAN'));

		$POSISI_ID = db_escape(get_input('POSISI_ID'));
		$TGL_POSISI = db_escape(get_input('TGL_POSISI'));
		$KET_POSISI = db_escape(get_input('KET_POSISI'));

		$GAJI_POKOK = db_escape(input_currency(get_input('GAJI_POKOK')));
		$TGL_GAJI_POKOK = db_escape(get_input('TGL_GAJI_POKOK'));
		$KET_GAJI_POKOK = db_escape(get_input('KET_GAJI_POKOK'));

		$ERROR_REQUIRE = 0;
		$ST_KERJA_DATA = '1';
		$JABATAN_DATA = '1';
		$POSISI_DATA = '1';
		$GAJI_DATA = '1';

		if (!empty($ST_KERJA)) {
			if (empty($TGL_ST_KERJA)) {
				$ERROR[] = 'Tanggal Perubahan Status Kerja wajib di isi.';
				$ST_KERJA_DATA = '0';
			} else {
				db_execute(" INSERT INTO histori_status (KARYAWAN_ID,HISTORI_STATUS,KETERANGAN,TGL) VALUES ('$ID','$ST_KERJA','$KET_ST_KERJA','$TGL_ST_KERJA') ");
				db_execute(" UPDATE karyawan SET ST_KERJA='$ST_KERJA' WHERE KARYAWAN_ID='$ID' ");
				if ($ST_KERJA == 'AKTIF') db_execute(" UPDATE karyawan SET TGL_MASUK='$TGL_ST_KERJA' WHERE KARYAWAN_ID='$ID' ");
				$ST_KERJA_DATA = '1';
			}
		}

		if (!empty($JABATAN_ID)) {
			if (empty($TGL_JABATAN)) {
				$ERROR[] = 'Tanggal Perubahan Level Jabatan wajib di isi.';
				$JABATAN_DATA = '0';
			} else {
				$JAB = db_first("
					SELECT P.PROJECT_ID,P.COMPANY_ID
					FROM jabatan J
					LEFT JOIN project P ON P.PROJECT_ID=J.PROJECT_ID
					WHERE J.JABATAN_ID='$JABATAN_ID'
				");
				$PROJECT_ID = isset($JAB->PROJECT_ID) ? $JAB->PROJECT_ID : '';
				$COMPANY_ID = isset($JAB->COMPANY_ID) ? $JAB->COMPANY_ID : '';
				db_execute(" INSERT INTO histori_karir (KARYAWAN_ID,JABATAN_ID,KETERANGAN,TGL) VALUES ('$ID','$JABATAN_ID','$KET_JABATAN','$TGL_JABATAN') ");
				db_execute(" UPDATE karyawan SET JABATAN_ID='$JABATAN_ID',PROJECT_ID='$PROJECT_ID',COMPANY_ID='$COMPANY_ID' WHERE KARYAWAN_ID='$ID' ");
				$JABATAN_DATA = '1';
			}
		}

		if (!empty($POSISI_ID)) {
			if (empty($TGL_POSISI)) {
				$ERROR[] = 'Tanggal Perubahan Jabatan wajib di isi.';
				$POSISI_DATA = '0';
			} else {
				db_execute(" INSERT INTO histori_posisi (KARYAWAN_ID,POSISI_ID,KETERANGAN,TGL) VALUES ('$ID','$POSISI_ID','$KET_POSISI','$TGL_POSISI') ");
				db_execute(" UPDATE karyawan SET POSISI_ID='$POSISI_ID' WHERE KARYAWAN_ID='$ID' ");
				$POSISI_DATA = '1';
			}
		}

		if (!empty($GAJI_POKOK)) {
			if (empty($TGL_GAJI_POKOK)) {
				$ERROR[] = 'Tanggal Perubahan Jabatan wajib di isi.';
				$GAJI_DATA = '0';
			} else {
				db_execute(" INSERT INTO histori_gaji (KARYAWAN_ID,HISTORI_GAJI,KETERANGAN,TGL) VALUES ('$ID','$GAJI_POKOK','$KET_GAJI_POKOK','$TGL_GAJI_POKOK') ");
				db_execute(" UPDATE karyawan SET GAJI_POKOK='$GAJI_POKOK' WHERE KARYAWAN_ID='$ID' ");
				$GAJI_DATA = '1';
			}
		}

		if ($ST_KERJA_DATA == '1' && $JABATAN_DATA == '1' && $POSISI_DATA == '1' && $GAJI_DATA == '1') {
			$TAB = 'tab3';
			header('location: ' . $_SERVER['PHP_SELF'] . '?op=edit&m=1&id=' . $ID . '&tab=' . $TAB);
			exit;
		}
		//$TAB_ACTIVE = '';
	}

	//update data tunjangan
	if ($UPDATE_TYPE == 'DATA_TUNJANGAN') {
		//$TUNJ_JABATAN = db_escape(input_currency(get_input('TUNJ_JABATAN')));
		$TUNJ_KELUARGA = db_escape(input_currency(get_input('TUNJ_KELUARGA')));
		$TUNJ_KINERJA = db_escape(input_currency(get_input('TUNJ_KINERJA')));
		$TUNJ_LAINNYA_1 = db_escape(input_currency(get_input('TUNJ_LAINNYA_1')));
		$TUNJ_LAINNYA_2 = db_escape(input_currency(get_input('TUNJ_LAINNYA_2')));
		$TUNJ_LAINNYA_3 = db_escape(input_currency(get_input('TUNJ_LAINNYA_3')));
		$TUNJ_LAINNYA_4 = db_escape(input_currency(get_input('TUNJ_LAINNYA_4')));
		$TUNJ_LAINNYA_5 = db_escape(input_currency(get_input('TUNJ_LAINNYA_5')));
		$TUNJ_OTTW = db_escape(input_currency(get_input('TUNJ_OTTW')));
		$TUNJ_KEAHLIAN = db_escape(input_currency(get_input('TUNJ_KEAHLIAN')));
		$TUNJ_PROYEK = db_escape(input_currency(get_input('TUNJ_PROYEK')));
		$TUNJ_BACKUP = db_escape(input_currency(get_input('TUNJ_BACKUP')));
		$TUNJ_SHIFT = db_escape(input_currency(get_input('TUNJ_SHIFT')));
		$JENIS_THR = db_escape(get_input('JENIS_THR'));
		$TIPE_GAJI = db_escape(get_input('TIPE_GAJI'));

		$TAB = 'tab4';
		$SQL = db_execute(" UPDATE karyawan SET TUNJ_KEAHLIAN='$TUNJ_KEAHLIAN',TUNJ_KELUARGA='$TUNJ_KELUARGA',TUNJ_OTTW='$TUNJ_OTTW',TUNJ_KINERJA='$TUNJ_KINERJA',TUNJ_LAINNYA_1='$TUNJ_LAINNYA_1',TUNJ_LAINNYA_2='$TUNJ_LAINNYA_2',TUNJ_LAINNYA_3='$TUNJ_LAINNYA_3',TUNJ_LAINNYA_4='$TUNJ_LAINNYA_4',TUNJ_LAINNYA_5='$TUNJ_LAINNYA_5',TUNJ_PROYEK='$TUNJ_PROYEK',TUNJ_BACKUP='$TUNJ_BACKUP',TUNJ_SHIFT='$TUNJ_SHIFT',JENIS_THR='$JENIS_THR',TIPE_GAJI='$TIPE_GAJI' WHERE KARYAWAN_ID='$ID' ");
		header('location: ' . $_SERVER['PHP_SELF'] . '?op=edit&m=1&id=' . $ID . '&tab=' . $TAB);
		exit;
	}

	//update dokumen karyawan
	if ($UPDATE_TYPE == 'DATA_SERTIFIKAT') {
		/*
		$KATEGORI_KEAHLIAN_ID = db_escape(get_input('KATEGORI_KEAHLIAN_ID'));
		$KEAHLIAN_ID = db_escape(get_input('KEAHLIAN_ID'));
		$SQL = db_execute(" UPDATE karyawan SET KATEGORI_KEAHLIAN_ID='$KATEGORI_KEAHLIAN_ID',KEAHLIAN_ID='$KEAHLIAN_ID' WHERE KARYAWAN_ID='$ID' ");
		*/

		$DOK_KARYAWAN = get_input('DOK_KARYAWAN');

		if (is_array($DOK_KARYAWAN) and count($DOK_KARYAWAN)) {
			foreach ($DOK_KARYAWAN as $key => $val) {
				if ($val != '') {
					$IJAZAH 			= get_input('IJAZAH');
					$SERTIFIKAT 	= get_input('SERTIFIKAT');
					$SIO 					= get_input('SIO');
					$KTA 					= get_input('KTA');

					$KETERANGAN_IJAZAH 			= get_input('KETERANGAN_IJAZAH');
					$KETERANGAN_SERTIFIKAT 	= get_input('KETERANGAN_SERTIFIKAT');
					$KETERANGAN_SIO 				= get_input('KETERANGAN_SIO');
					$KETERANGAN_KTA 				= get_input('KETERANGAN_KTA');

					$CURR_IJAZAH 			= get_input('CURR_IJAZAH');
					$CURR_SERTIFIKAT 	= get_input('CURR_SERTIFIKAT');
					$CURR_SIO 				= get_input('CURR_SIO');
					$CURR_KTA 				= get_input('CURR_KTA');

					$MASA_IJAZAH 			= get_input('MASA_IJAZAH');
					$MASA_SERTIFIKAT 	= get_input('MASA_SERTIFIKAT');
					$MASA_SIO 				= get_input('MASA_SIO');
					$MASA_KTA 				= get_input('MASA_KTA');

					$ijazah_name		= $_FILES['FILE_IJAZAH']['name'];
					$ijazah_size		= $_FILES['FILE_IJAZAH']['size'];
					$ijazah_tmp			= $_FILES['FILE_IJAZAH']['tmp_name'];
					$ijazah_type		= $_FILES['FILE_IJAZAH']['type'];

					$sertifikat_name	= $_FILES['FILE_SERTIFIKAT']['name'];
					$sertifikat_size	= $_FILES['FILE_SERTIFIKAT']['size'];
					$sertifikat_tmp		= $_FILES['FILE_SERTIFIKAT']['tmp_name'];
					$sertifikat_type	= $_FILES['FILE_SERTIFIKAT']['type'];

					$sio_name			= $_FILES['FILE_SIO']['name'];
					$sio_size			= $_FILES['FILE_SIO']['size'];
					$sio_tmp			= $_FILES['FILE_SIO']['tmp_name'];
					$sio_type			= $_FILES['FILE_SIO']['type'];

					$kta_name			= $_FILES['FILE_KTA']['name'];
					$kta_size			= $_FILES['FILE_KTA']['size'];
					$kta_tmp			= $_FILES['FILE_KTA']['tmp_name'];
					$kta_type			= $_FILES['FILE_KTA']['type'];

					$IJAZAH					= isset($IJAZAH[$key]) ? $IJAZAH[$key] : '';
					$SERTIFIKAT			= isset($SERTIFIKAT[$key]) ? $SERTIFIKAT[$key] : '';
					$SIO						= isset($SIO[$key]) ? $SIO[$key] : '';
					$KTA						= isset($KTA[$key]) ? $KTA[$key] : '';

					$KETERANGAN_IJAZAH 			= isset($KETERANGAN_IJAZAH[$key]) ? $KETERANGAN_IJAZAH[$key] : '';
					$KETERANGAN_SERTIFIKAT 	= isset($KETERANGAN_SERTIFIKAT[$key]) ? $KETERANGAN_SERTIFIKAT[$key] : '';
					$KETERANGAN_SIO 				= isset($KETERANGAN_SIO[$key]) ? $KETERANGAN_SIO[$key] : '';
					$KETERANGAN_KTA 				= isset($KETERANGAN_KTA[$key]) ? $KETERANGAN_KTA[$key] : '';

					$CURR_IJAZAH 			= isset($CURR_IJAZAH[$key]) ? $CURR_IJAZAH[$key] : '';
					$CURR_SERTIFIKAT 	= isset($CURR_SERTIFIKAT[$key]) ? $CURR_SERTIFIKAT[$key] : '';
					$CURR_SIO 				= isset($CURR_SIO[$key]) ? $CURR_SIO[$key] : '';
					$CURR_KTA 				= isset($CURR_KTA[$key]) ? $CURR_KTA[$key] : '';

					$MASA_IJAZAH 			= isset($MASA_IJAZAH[$key]) ? $MASA_IJAZAH[$key] : '';
					$MASA_SERTIFIKAT 	= isset($MASA_SERTIFIKAT[$key]) ? $MASA_SERTIFIKAT[$key] : '';
					$MASA_SIO 				= isset($MASA_SIO[$key]) ? $MASA_SIO[$key] : '';
					$MASA_KTA 				= isset($MASA_KTA[$key]) ? $MASA_KTA[$key] : '';

					$FILENAME_IJAZAH 			= rand(11111, 99999) . '_' . $ijazah_name[$key];
					$TMP_IJAZAH 					= $ijazah_tmp[$key];
					$FILENAME_SERTIFIKAT	= rand(11111, 99999) . '_' . $sertifikat_name[$key];
					$TMP_SERTIFIKAT				= $sertifikat_tmp[$key];
					$FILENAME_SIO					= rand(11111, 99999) . '_' . $sio_name[$key];
					$TMP_SIO							= $sio_tmp[$key];
					$FILENAME_KTA 				= rand(11111, 99999) . '_' . $kta_name[$key];
					$TMP_KTA 							= $kta_tmp[$key];

					$NEW_FILENAME_IJAZAH = 0;
					if (is_uploaded_file($TMP_IJAZAH)) {
						if (move_uploaded_file($TMP_IJAZAH, "uploads/karyawan/" . $FILENAME_IJAZAH)) {
							$NEW_FILENAME_IJAZAH = 1;
						}
					}
					if ($NEW_FILENAME_IJAZAH == 1) {
						$FILES_IJAZAH = $FILENAME_IJAZAH;
					} else {
						$FILES_IJAZAH = $CURR_IJAZAH;
					}

					$NEW_FILENAME_SERTIFIKAT = 0;
					if (is_uploaded_file($TMP_SERTIFIKAT)) {
						if (move_uploaded_file($TMP_SERTIFIKAT, "uploads/karyawan/" . $FILENAME_SERTIFIKAT)) {
							$NEW_FILENAME_SERTIFIKAT = 1;
						}
					}
					if ($NEW_FILENAME_SERTIFIKAT == 1) {
						$FILES_SERTIFIKAT = $FILENAME_SERTIFIKAT;
					} else {
						$FILES_SERTIFIKAT = $CURR_SERTIFIKAT;
					}

					$NEW_FILENAME_SIO = 0;
					if (is_uploaded_file($TMP_SIO)) {
						if (move_uploaded_file($TMP_SIO, "uploads/karyawan/" . $FILENAME_SIO)) {
							$NEW_FILENAME_SIO = 1;
						}
					}
					if ($NEW_FILENAME_SIO == 1) {
						$FILES_SIO = $FILENAME_SIO;
					} else {
						$FILES_SIO = $CURR_SIO;
					}

					$NEW_FILENAME_KTA = 0;
					if (is_uploaded_file($TMP_KTA)) {
						if (move_uploaded_file($TMP_KTA, "uploads/karyawan/" . $FILENAME_KTA)) {
							$NEW_FILENAME_KTA = 1;
						}
					}
					if ($NEW_FILENAME_KTA == 1) {
						$FILES_KTA = $FILENAME_KTA;
					} else {
						$FILES_KTA = $CURR_KTA;
					}

					$DOK_KARYAWAN = $val;
					$KARYAWAN_ID 	= $ID;

					$DOKUMEN_KARYAWAN .= "('" . $KARYAWAN_ID . "','" . $DOK_KARYAWAN . "','" . $IJAZAH . "','" . $SERTIFIKAT . "','" . $SIO . "','" . $KTA . "','"
						. $KETERANGAN_IJAZAH . "','" . $KETERANGAN_SERTIFIKAT . "','" . $KETERANGAN_SIO . "','" . $KETERANGAN_KTA . "','"
						. $FILES_IJAZAH . "','" . $FILES_SERTIFIKAT . "','" . $FILES_SIO . "','" . $FILES_KTA . "','" . $MASA_IJAZAH . "','" . $MASA_SERTIFIKAT . "','" . $MASA_SIO . "','" . $MASA_KTA . "'),";
				}
			}

			$DOKUMEN_KARYAWAN = rtrim($DOKUMEN_KARYAWAN, ',');

			echo $DOKUMEN_KARYAWAN;
			die();

			if (!empty($DOKUMEN_KARYAWAN)) {
				db_execute(" DELETE FROM dok_karyawan WHERE KARYAWAN_ID='$ID'");
				db_execute(" INSERT INTO dok_karyawan (KARYAWAN_ID,DOK_KARYAWAN,IJAZAH,SERTIFIKAT,SIO,KTA,KETERANGAN_IJAZAH,KETERANGAN_SERTIFIKAT,KETERANGAN_SIO,KETERANGAN_KTA,FILE_IJAZAH,FILE_SERTIFIKAT,FILE_SIO,FILE_KTA,MASA_IJAZAH,MASA_SERTIFIKAT,MASA_SIO,MASA_KTA) VALUES $DOKUMEN_KARYAWAN ");
			}
		} else {
			db_execute(" DELETE FROM dok_karyawan WHERE KARYAWAN_ID='$ID'");
		}

		//print_r($DOK_KARYAWAN); die();
		$TAB = 'tab8';
		header('location: ' . $_SERVER['PHP_SELF'] . '?op=edit&m=1&id=' . $ID . '&tab=' . $TAB);
		exit;
	}

	//update pengalaman oraganisasi
	if ($UPDATE_TYPE == 'DATA_ORGANISASI') {
		/* Riwayat Organisasi calon karyawan */
		$NAMA_ORGANISASI = get_input('NAMA_ORGANISASI');
		if (is_array($NAMA_ORGANISASI) and count($NAMA_ORGANISASI)) {
			foreach ($NAMA_ORGANISASI as $key => $val) {
				if ($val != '') {
					$JABATAN_ORGANISASI = get_input('JABATAN_ORGANISASI');
					$LOKASI_ORGANISASI 	= get_input('LOKASI_ORGANISASI');
					$PERIODE_ORGANISASI = get_input('PERIODE_ORGANISASI');
					$KARYAWAN_ID		= $ID;
					$NAMA_ORGANISASI 	= $val;

					$JABATAN_ORGANISASI	= isset($JABATAN_ORGANISASI[$key]) ? $JABATAN_ORGANISASI[$key] : '';
					$LOKASI_ORGANISASI	= isset($LOKASI_ORGANISASI[$key]) ? $LOKASI_ORGANISASI[$key] : '';
					$PERIODE_ORGANISASI	= isset($PERIODE_ORGANISASI[$key]) ? $PERIODE_ORGANISASI[$key] : '';

					$KRY_ORGANISASI .= "('" . $KARYAWAN_ID . "','" . $NAMA_ORGANISASI . "','" . $JABATAN_ORGANISASI . "','" . $LOKASI_ORGANISASI . "','" . $PERIODE_ORGANISASI . "'),";
				}
			}

			$KRY_ORGANISASI = rtrim($KRY_ORGANISASI, ',');
			if (!empty($KRY_ORGANISASI)) {
				db_execute(" DELETE FROM organisasi_karyawan WHERE KARYAWAN_ID='$ID' ");
				db_execute(" INSERT INTO organisasi_karyawan (KARYAWAN_ID,NAMA_ORGANISASI,JABATAN_ORGANISASI,LOKASI_ORGANISASI,PERIODE_ORGANISASI) VALUES $KRY_ORGANISASI ");
			}
		} else {
		}

		$TAB = 'tab9';
		header('location: ' . $_SERVER['PHP_SELF'] . '?op=edit&m=1&id=' . $ID . '&tab=' . $TAB);
		exit;
	}

	//update data pengalaman
	if ($UPDATE_TYPE == 'DATA_PENGALAMAN') {
		$TUGAS_JABATAN 		=  db_escape(get_input('TUGAS_JABATAN'));
		$MASALAH_PENTING 	=  db_escape(get_input('MASALAH_PENTING'));
		$JABATAN_ATASAN 	=  db_escape(get_input('JABATAN_ATASAN'));
		$JUMLAH_ANAK_BUAH 	=  db_escape(get_input('JUMLAH_ANAK_BUAH'));

		db_execute(" UPDATE karyawan SET TUGAS_JABATAN='$TUGAS_JABATAN',MASALAH_PENTING='$MASALAH_PENTING',JABATAN_ATASAN='$JABATAN_ATASAN',JUMLAH_ANAK_BUAH='$JUMLAH_ANAK_BUAH' WHERE KARYAWAN_ID='$ID'");

		/* Pengalaman calon karyawan */
		$NAMA_PERUSAHAAN = get_input('NAMA_PERUSAHAAN');
		if (is_array($NAMA_PERUSAHAAN) and count($NAMA_PERUSAHAAN)) {
			foreach ($NAMA_PERUSAHAAN as $key => $val) {
				if ($val != '') {
					$BIDANG_USAHA			= get_input('BIDANG_USAHA');
					$ALAMAT_PERUSAHAAN		= get_input('ALAMAT_PERUSAHAAN');
					$ATASAN 				= get_input('ALAMAT_PERUSAHAAN');
					$NO_TELP_PERUSAHAAN		= get_input('NO_TELP_PERUSAHAAN');
					$PERIODE_BEKERJA		= get_input('PERIODE_BEKERJA');
					$JABATAN_AWAL			= get_input('JABATAN_AWAL');
					$JABATAN_AKHIR			= get_input('JABATAN_AKHIR');
					$GAPOK_SEBELUMNYA		= input_currency(get_input('GAPOK_SEBELUMNYA'));
					$TUNJANGAN_LAINNYA		= get_input('TUNJANGAN_LAINNYA');
					$ALASAN_RESIGN			= get_input('ALASAN_RESIGN');
					$DESKRIPSI_PEKERJAAN	= get_input('DESKRIPSI_PEKERJAAN');
					$KARYAWAN_ID		= $ID;
					$NAMA_PERUSAHAAN 		= $val;

					$BIDANG_USAHA			= isset($BIDANG_USAHA[$key]) ? $BIDANG_USAHA[$key] : '';
					$ALAMAT_PERUSAHAAN		= isset($ALAMAT_PERUSAHAAN[$key]) ? $ALAMAT_PERUSAHAAN[$key] : '';
					$ATASAN					= isset($ATASAN[$key]) ? $ATASAN[$key] : '';
					$NO_TELP_PERUSAHAAN		= isset($NO_TELP_PERUSAHAAN[$key]) ? $NO_TELP_PERUSAHAAN[$key] : '';
					$PERIODE_BEKERJA		= isset($PERIODE_BEKERJA[$key]) ? $PERIODE_BEKERJA[$key] : '';
					$JABATAN_AWAL			= isset($JABATAN_AWAL[$key]) ? $JABATAN_AWAL[$key] : '';
					$JABATAN_AKHIR			= isset($JABATAN_AKHIR[$key]) ? $JABATAN_AKHIR[$key] : '';
					$GAPOK_SEBELUMNYA		= isset($GAPOK_SEBELUMNYA[$key]) ? $GAPOK_SEBELUMNYA[$key] : '';
					$TUNJANGAN_LAINNYA		= isset($TUNJANGAN_LAINNYA[$key]) ? $TUNJANGAN_LAINNYA[$key] : '';
					$ALASAN_RESIGN			= isset($ALASAN_RESIGN[$key]) ? $ALASAN_RESIGN[$key] : '';
					$DESKRIPSI_PEKERJAAN	= isset($DESKRIPSI_PEKERJAAN[$key]) ? $DESKRIPSI_PEKERJAAN[$key] : '';

					$PENGALAMAN_DATA .= "('" . $KARYAWAN_ID . "','" . $NAMA_PERUSAHAAN . "','" . $BIDANG_USAHA . "','" . $ALAMAT_PERUSAHAAN . "','" . $ATASAN . "','" . $NO_TELP_PERUSAHAAN . "','" . $PERIODE_BEKERJA . "','" . $JABATAN_AWAL . "','" . $JABATAN_AKHIR . "','" . $GAPOK_SEBELUMNYA . "','" . $TUNJANGAN_LAINNYA . "','" . $ALASAN_RESIGN . "','" . $DESKRIPSI_PEKERJAAN . "'),";
				}
			}

			$PENGALAMAN_DATA = rtrim($PENGALAMAN_DATA, ',');
			if (!empty($PENGALAMAN_DATA)) {
				db_execute(" DELETE FROM pengalaman_karyawan WHERE KARYAWAN_ID='$ID' ");
				db_execute(" INSERT INTO pengalaman_karyawan (KARYAWAN_ID,NAMA_PERUSAHAAN,BIDANG_USAHA,ALAMAT_PERUSAHAAN,ATASAN,NO_TELP_PERUSAHAAN,PERIODE_BEKERJA,JABATAN_AWAL,JABATAN_AKHIR,GAPOK_SEBELUMNYA,TUNJANGAN_LAINNYA,ALASAN_RESIGN,DESKRIPSI_PEKERJAAN) VALUES $PENGALAMAN_DATA ");
			}
		}

		$TAB = 'tab10';
		header('location: ' . $_SERVER['PHP_SELF'] . '?op=edit&m=1&id=' . $ID . '&tab=' . $TAB);
		exit;
	}

	//update data minat dan konsep diri
	if ($UPDATE_TYPE == 'DATA_MINAT') {
		$HOBI 					=  db_escape(get_input('HOBI'));
		$EXPECTED_SALARY 		=  db_escape(input_currency(get_input('EXPECTED_SALARY')));
		$MOTIVASI_BEKERJA 		=  db_escape(get_input('MOTIVASI_BEKERJA'));
		$FASILITAS_LAINNYA 		=  db_escape(get_input('FASILITAS_LAINNYA'));
		$SIAP_BEKERJA 			=  db_escape(get_input('SIAP_BEKERJA'));
		$MOTIVASI_DIAIRKON 		=  db_escape(get_input('MOTIVASI_DIAIRKON'));
		$LUAR_DAERAH 			=  db_escape(get_input('LUAR_DAERAH'));
		$ALASAN_DILUAR_DAERAH 	=  db_escape(get_input('ALASAN_DILUAR_DAERAH'));


		db_execute(" UPDATE karyawan SET HOBI='$HOBI',EXPECTED_SALARY='$EXPECTED_SALARY',MOTIVASI_BEKERJA='$MOTIVASI_BEKERJA',FASILITAS_LAINNYA='$FASILITAS_LAINNYA',SIAP_BEKERJA='$SIAP_BEKERJA',MOTIVASI_DIAIRKON='$MOTIVASI_DIAIRKON',LUAR_DAERAH='$LUAR_DAERAH',ALASAN_DILUAR_DAERAH='$ALASAN_DILUAR_DAERAH' ");

		$TAB = 'tab11';
		header('location: ' . $_SERVER['PHP_SELF'] . '?op=edit&m=1&id=' . $ID . '&tab=' . $TAB);
		exit;
	}

	//update data lain-lain
	if ($UPDATE_TYPE == 'DATA_LAIN') {
		$INFO_LOKER 		=  db_escape(get_input('INFO_LOKER'));
		$KERABAT_AIRKON 	=  db_escape(get_input('KERABAT_AIRKON'));
		$RIWAYAT_KESEHATAN 	=  db_escape(get_input('RIWAYAT_KESEHATAN'));
		$RIWAYAT_RAWAT 		=  db_escape(get_input('RIWAYAT_RAWAT'));
		$INFO_LOKER_LAINNYA =  db_escape(get_input('INFO_LOKER_LAINNYA'));
		$NAMA_KERABAT 		=  db_escape(get_input('NAMA_KERABAT'));
		$NAMA_PENYAKIT 		=  db_escape(get_input('NAMA_PENYAKIT'));

		db_execute(" UPDATE karyawan SET INFO_LOKER='$INFO_LOKER',KERABAT_AIRKON='$KERABAT_AIRKON',RIWAYAT_KESEHATAN='$RIWAYAT_KESEHATAN',RIWAYAT_RAWAT='$RIWAYAT_RAWAT',INFO_LOKER_LAINNYA='$INFO_LOKER_LAINNYA',NAMA_KERABAT='$NAMA_KERABAT',NAMA_PENYAKIT='$NAMA_PENYAKIT' ");

		/* penanggung calon karyawan */
		$NAMA_PENANGGUNG = get_input('NAMA_PENANGGUNG');
		if (is_array($NAMA_PENANGGUNG) and count($NAMA_PENANGGUNG)) {
			foreach ($NAMA_PENANGGUNG as $key => $val) {
				if ($val != '') {
					$ALAMAT_PENANGGUNG		= get_input('ALAMAT_PENANGGUNG');
					$TELP_PENANGGUNG		= get_input('TELP_PENANGGUNG');
					$HUBUNGAN_PENANGGUNG 	= get_input('HUBUNGAN_PENANGGUNG');
					$KARYAWAN_ID			= $ID;
					$NAMA_PENANGGUNG 		= $val;

					$ALAMAT_PENANGGUNG		= isset($ALAMAT_PENANGGUNG[$key]) ? $ALAMAT_PENANGGUNG[$key] : '';
					$TELP_PENANGGUNG		= isset($TELP_PENANGGUNG[$key]) ? $TELP_PENANGGUNG[$key] : '';
					$HUBUNGAN_PENANGGUNG	= isset($HUBUNGAN_PENANGGUNG[$key]) ? $HUBUNGAN_PENANGGUNG[$key] : '';

					$PENANGGUNG_DATA .= "('" . $KARYAWAN_ID . "','" . $NAMA_PENANGGUNG . "','" . $ALAMAT_PENANGGUNG . "','" . $TELP_PENANGGUNG . "','" . $HUBUNGAN_PENANGGUNG . "'),";
				}
			}

			$PENANGGUNG_DATA = rtrim($PENANGGUNG_DATA, ',');
			if (!empty($PENANGGUNG_DATA)) {
				db_execute(" DELETE FROM penanggung_karyawan WHERE KARYAWAN_ID='$ID' ");
				db_execute(" INSERT INTO penanggung_karyawan (KARYAWAN_ID,NAMA_PENANGGUNG,ALAMAT_PENANGGUNG,TELP_PENANGGUNG,HUBUNGAN_PENANGGUNG) VALUES $PENANGGUNG_DATA ");
			}
		} else {
			db_execute(" DELETE FROM penanggung_karyawan WHERE KARYAWAN_ID='$ID' ");
		}

		$TAB = 'tab12';
		header('location: ' . $_SERVER['PHP_SELF'] . '?op=edit&m=1&id=' . $ID . '&tab=' . $TAB);
		exit;
	}
}

if ($EDIT->TAHUN_LULUS == '0000') $EDIT->TAHUN_LULUS = '';
if (get_input('m') == '1') {
	$SUCCESS = 'Data berhasil di simpan.';
}

if ($OP == 'add') {
	$distab = 'disabled';
	$distoggle = '';
}

if ($OP == 'edit') {
	$distab = '';
	$distoggle = 'tab';
}

/* -------------- Start of Struktur Organisasi ------------- */
$STRUKTUR_ORGANISASI = db_fetch("
	SELECT *
	FROM struktur
	ORDER BY ORD ASC
");
$list = array();
if (count($STRUKTUR_ORGANISASI) > 0) {
	foreach ($STRUKTUR_ORGANISASI as $so) {
		$thisref = &$refs[$so->STRUKTUR_ID];
		$thisref = array_merge((array) $thisref, (array) $so);
		if ($so->PARENT_ID == 0) {
			$list[] = &$thisref;
		} else {
			$refs[$so->PARENT_ID]['child'][] = &$thisref;
		}
	}
}
$TREE_CHAR = '_____';
$RS = hirearchy($list);
$STRUKTUR_ORGANISASI_OPTION = array('0' => ' -- STRUKTUR --');
if (count($RS) > 0) {
	foreach ($RS as $so) {
		$TREE = '';
		for ($i = 1; $i < $so->DEPTH; $i++) {
			$TREE .= $TREE_CHAR;
		}
		$STRUKTUR_ORGANISASI_OPTION[$so->STRUKTUR_ID] = '<span style="color:#cccccc;">' . $TREE . '</span>' . ' ' . strtoupper($so->STRUKTUR);
	}
}
/* -------------- End of Struktur Organisasi ------------- */

$JS[] = 'static/select2/js/select2.min.js';
$CSS[] = 'static/select2/css/select2.min.css';
$CSS[] = 'static/select2/css/select2-bootstrap.min.css';

$JS[] = 'static/date/jquery.plugin.min.js';
$JS[] = 'static/date/jquery.datepick.min.js';
$JS[] = 'static/date/jquery.datepick-id.js';
$CSS[] = 'static/date/jquery.datepick.css';

include 'header.php';
?>

<section class="container-fluid" style="margin-top: 25px;">
	<h1 style="margin-top:0px;" class="border-title">
		<?php echo ucfirst($OP) ?> Karyawan
		<a href="karyawan.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<?php if ($OP == 'edit') {
			echo '<a href="karyawan-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>';
		} ?>
		&nbsp;&nbsp;<span class="text-primary"><?php echo isset($EDIT->NAMA) ? strtoupper($EDIT->NAMA) : ''; ?></span>
		&nbsp;&nbsp;&nbsp;<?php echo isset($EDIT->NIK) ? '[NIK : ' . $EDIT->NIK . ']' : ''; ?>
		<?php echo isset($EDIT->KARYAWAN_ID) ? ' &nbsp;&nbsp;&nbsp; [PIN : ' . $EDIT->KARYAWAN_ID . ']' : ''; ?>
	</h1>

	<?php include 'msg.php' ?>

	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active">
			<a href="#tab1" aria-controls="tab1" role="tab" data-toggle="tab">Identitas Diri</a>
		</li>
		<li role="presentation" class="<?php echo $distab ?>">
			<a href="#tab2" aria-controls="tab2" role="tab" data-toggle="<?php echo $distoggle ?>">Setup</a>
		</li>
		<?php if (has_access('karyawan.view_gaji')) { ?>
			<li role="presentation" class="<?php echo $distab ?>">
				<a href="#tab3" aria-controls="tab3" role="tab" data-toggle="<?php echo $distoggle ?>">Karir</a>
			</li>
			<li role="presentation" class="<?php echo $distab ?>">
				<a href="#tab4" aria-controls="tab4" role="tab" data-toggle="<?php echo $distoggle ?>">Tunjangan</a>
			</li>
			<li role="presentation" class="<?php echo $distab ?>">
				<a href="#tab5" aria-controls="tab5" role="tab" data-toggle="<?php echo $distoggle ?>">Riwayat Karir</a>
			</li>
		<?php } ?>
		<li role="presentation" class="<?php echo $distab ?>">
			<a href="dokumen-pendidikan.php?id=<?= $ID; ?>">Dokumen Pendidikan</a>
		</li>
		<li role="presentation" class="<?php echo $distab ?>">
			<a href="dokumen-keluarga.php?id=<?= $ID; ?>">Dokumen Keluarga</a>
		</li>
		<li role="presentation" class="<?php echo $distab ?>">
			<a href="dokumen-karyawan.php?id=<?= $ID; ?>">Dokumen Karyawan</a>
		</li>
		<li role="presentation" class="<?php echo $distab ?>">
			<a href="#tab9" aria-controls="tab9" role="tab" data-toggle="<?php echo $distoggle ?>">Pengalaman Organisasi</a>
		</li>
		<li role="presentation" class="<?php echo $distab ?>">
			<a href="#tab10" aria-controls="tab10" role="tab" data-toggle="<?php echo $distoggle ?>">Pengalaman Kerja</a>
		</li>
		<li role="presentation" class="<?php echo $distab ?>">
			<a href="#tab11" aria-controls="tab11" role="tab" data-toggle="<?php echo $distoggle ?>">Minat & Konsep Diri</a>
		</li>
		<li role="presentation" class="<?php echo $distab ?>">
			<a href="#tab12" aria-controls="tab12" role="tab" data-toggle="<?php echo $distoggle ?>">Lainnya</a>
		</li>
	</ul>

	<form id="form" class="form-horizontal" action="karyawan-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>&tab=<?php echo $TAB ?>" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="CURRENT_FOTO" value="<?php echo $EDIT->FOTO ?>">
		<input type="hidden" name="CURRENT_FILE_SCAN_IJAZAH" value="<?php echo $EDIT->IJAZAH ?>">
		<input type="hidden" name="CURRENT_CV" value="<?php echo $EDIT->CV ?>">
		<input type="hidden" name="CURRENT_FCKTP" value="<?php echo $EDIT->FC_KTP ?>">
		<input type="hidden" name="CURRENT_FCNPWP" value="<?php echo $EDIT->FC_NPWP ?>">
		<input type="hidden" name="CURRENT_FCBPJS_KESEHATAN" value="<?php echo $EDIT->FC_BPJS_KESEHATAN ?>">
		<input type="hidden" name="CURRENT_FCBPJS_KETENAGAKERJAAN" value="<?php echo $EDIT->FC_BPJS_KETENAGAKERJAAN ?>">
		<input type="hidden" name="CURRENT_FILE_KK" value="<?php echo $EDIT->FILE_KK ?>">
		<input type="hidden" name="CURRENT_STRUKTUR_IMG" value="<?php echo $EDIT->STRUKTUR_IMG ?>">
		<input type="hidden" name="TMP_JABATAN_ID" value="<?php echo $EDIT->JABATAN_ID ?>">
		<input type="hidden" name="CURRENT_ID" value="<?php echo $ID ?>">

		<div class="tab-content" style="margin-top:20px;">

			<?php /* TAB 1 : IDENTITAS DIRI */ ?>
			<div role="tabpanel" class="tab-pane active" id="tab1">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group bg-info text-center" style="margin-bottom: 20px;">
							<label for="" class="control-label" style="border-bottom: 2px solid #eee; padding-bottom: 10px;">
								IDENTITAS DIRI
							</label>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<?php if ($OP == 'add') { ?>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">PIN / ID</label>
								<div class="col-sm-9">
									<input type="text" name="KARYAWAN_ID" value="<?php echo set_value('KARYAWAN_ID', $EDIT->KARYAWAN_ID) ?>" class="form-control" maxlength="20">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">JOIN DATE</label>
								<div class="col-sm-9">
									<input type="date" name="TGL_MASUK" class="form-control" maxlength="20">
								</div>
							</div>
						<?php } ?>

						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Nama Lengkap
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-9">
								<input type="text" name="NAMA" value="<?php echo set_value('NAMA', $EDIT->NAMA) ?>" class="form-control" style="text-transform:uppercase">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Nama Panggilan
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-9">
								<input type="text" name="NAMA_PANGGILAN" value="<?php echo set_value('NAMA_PANGGILAN', $EDIT->NAMA_PANGGILAN) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Jenis Kelamin
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-9">
								<?php echo dropdown('JK', array('L' => 'Laki-Laki', 'P' => 'Perempuan'), set_value('JK', $EDIT->JK), ' class="form-control" ') ?>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Tempat Lahir
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-9">
								<input type="text" name="TP_LAHIR" value="<?php echo set_value('TP_LAHIR', $EDIT->TP_LAHIR) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Tanggal Lahir
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-9">
								<input type="text" name="TGL_LAHIR" value="<?php echo set_value('TGL_LAHIR', $EDIT->TGL_LAHIR) ?>" class="form-control datepicker" autocomplete="off" placeholder="YYYY-MM-DD">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Kewarganegaraan
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-9">
								<?php echo dropdown('KEWARGANEGARAAN', array('WNI' => 'WNI', 'WNA' => 'WNA'), set_value('KEWARGANEGARAAN', $EDIT->KEWARGANEGARAAN), ' class="form-control" ') ?>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Suku
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-9">
								<input type="text" name="SUKU" value="<?php echo set_value('SUKU', $EDIT->SUKU) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Agama
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-9">
								<?php echo dropdown('AGAMA', array('ISLAM' => 'ISLAM', 'KRISTEN' => 'KRISTEN', 'KATOLIK' => 'KATOLIK', 'HINDU' => 'HINDU', 'BUDHA' => 'BUDHA', 'KONG HU CHU' => 'KONG HU CHU'), set_value('AGAMA', $EDIT->AGAMA), ' class="form-control" ') ?>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Golongan Darah
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-9">
								<?php echo dropdown('GOL_DARAH', array('A' => 'A', 'B' => 'B', 'AB' => 'AB', 'O' => 'O'), set_value('GOL_DARAH', $EDIT->GOL_DARAH), ' class="form-control" ') ?>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Tinggi Badan
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-2">
								<input type="number" name="TINGGI" value="<?php echo set_value('TINGGI', $EDIT->TINGGI) ?>" class="form-control">
							</div>
							<div class="col-sm-1 control-label" style="text-align: left;">Cm</div>
							<label for="" class="col-sm-3 control-label">Berat Badan
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-2">
								<input type="number" name="BERAT" value="<?php echo set_value('BERAT', $EDIT->BERAT) ?>" class="form-control">
							</div>
							<div class="col-sm-1 control-label" style="text-align: left;">Kg</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Ukuran Baju
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-3">
								<?php echo dropdown('UKURAN_BAJU', array('S' => 'S', 'M' => 'M', 'L' => 'L', 'XL' => 'XL'), set_value('UKURAN_BAJU', $EDIT->UKURAN_BAJU), ' class="form-control" ') ?>
							</div>
							<label for="" class="col-sm-3 control-label">Ukuran Sepatu
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-3">
								<input type="number" name="UKURAN_SEPATU" value="<?php echo set_value('UKURAN_SEPATU', $EDIT->UKURAN_SEPATU) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">No Telp Rumah
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-4">
								<input type="text" name="TELP" value="<?php echo set_value('TELP', $EDIT->TELP) ?>" class="form-control">
							</div>
							<label for="" class="col-sm-1 control-label">HP
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-4">
								<input type="text" name="HP" value="<?php echo set_value('HP', $EDIT->HP) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Alamat Email
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-5">
								<input type="text" name="EMAIL" value="<?php echo set_value('EMAIL', $EDIT->EMAIL) ?>" class="form-control">
							</div>
							<div class="col-sm-4">
								<input type="password" name="PASSWORD" class="form-control" placeholder="Password">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">No. e-KTP
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-5">
								<input type="text" name="NO_IDENTITAS" value="<?php echo set_value('NO_IDENTITAS', $EDIT->NO_IDENTITAS) ?>" class="form-control">
							</div>
							<div class="col-sm-3">
								<input type="file" name="FC_KTP" class="form-control" title="Upload Scan e-KTP">
							</div>
							<?php if (!empty($EDIT->FC_KTP) and url_exists(base_url() . 'uploads/cv/' . rawurlencode($EDIT->FC_KTP))) { ?>
								<div class="col-sm-1" style="margin-left: -35px;">
									<span class="input-group-btn">
										<a class="btn btn-primary btn-flat" href="<?php echo base_url() . "uploads/cv/" . $EDIT->FC_KTP ?>" download title="Download">
											<span class="glyphicon glyphicon-download"></span>
										</a>
									</span>
								</div>
							<?php } ?>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">No. NPWP
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-5">
								<input type="text" name="NPWP" value="<?php echo set_value('NPWP', $EDIT->NPWP) ?>" class="form-control">
							</div>
							<div class="col-sm-3">
								<input type="file" name="FC_NPWP" class="form-control" title="Upload Scan NPWP">
							</div>
							<?php if (!empty($EDIT->FC_NPWP) and url_exists(base_url() . 'uploads/cv/' . rawurlencode($EDIT->FC_NPWP))) { ?>
								<div class="col-sm-1" style="margin-left: -35px;">
									<span class="input-group-btn">
										<a class="btn btn-primary btn-flat" href="<?php echo base_url() . "uploads/cv/" . $EDIT->FC_NPWP ?>" download title="Download">
											<span class="glyphicon glyphicon-download"></span>
										</a>
									</span>
								</div>
							<?php } ?>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">BPJS Kesehatan
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-5">
								<input type="text" name="BPJS_KESEHATAN" value="<?php echo set_value('BPJS_KESEHATAN', $EDIT->BPJS_KESEHATAN) ?>" class="form-control">
							</div>
							<div class="col-sm-3">
								<input type="file" name="FC_BPJS_KESEHATAN" class="form-control" title="Upload Scan BPJS Kesehatan">
							</div>
							<?php if (!empty($EDIT->FC_BPJS_KESEHATAN) and url_exists(base_url() . 'uploads/cv/' . rawurlencode($EDIT->FC_BPJS_KESEHATAN))) { ?>
								<div class="col-sm-1" style="margin-left: -35px;">
									<span class="input-group-btn">
										<a class="btn btn-primary btn-flat" href="<?php echo base_url() . "uploads/cv/" . $EDIT->FC_BPJS_KESEHATAN ?>" download title="Download">
											<span class="glyphicon glyphicon-download"></span>
										</a>
									</span>
								</div>
							<?php } ?>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">BPJS Ketenagakerjaan
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-5">
								<input type="text" name="BPJS_KETENAGAKERJAAN" value="<?php echo set_value('BPJS_KETENAGAKERJAAN', $EDIT->BPJS_KETENAGAKERJAAN) ?>" class="form-control">
							</div>
							<div class="col-sm-3">
								<input type="file" name="FC_BPJS_KETENAGAKERJAAN" class="form-control" title="Upload Scan BPJS Ketenagakerjaan">
							</div>
							<?php if (!empty($EDIT->FC_BPJS_KETENAGAKERJAAN) and url_exists(base_url() . 'uploads/cv/' . rawurlencode($EDIT->FC_BPJS_KETENAGAKERJAAN))) { ?>
								<div class="col-sm-1" style="margin-left: -35px;">
									<span class="input-group-btn">
										<a class="btn btn-primary btn-flat" href="<?php echo base_url() . "uploads/cv/" . $EDIT->FC_BPJS_KETENAGAKERJAAN ?>" download title="Download">
											<span class="glyphicon glyphicon-download"></span>
										</a>
									</span>
								</div>
							<?php } ?>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Satus Kawin
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-9">
								<?php echo dropdown('ST_KAWIN', array('LAJANG' => 'LAJANG', 'MENIKAH' => 'MENIKAH', 'JANDA' => 'JANDA', 'DUDA' => 'DUDA'), set_value('ST_KAWIN', $EDIT->ST_KAWIN), ' class="form-control" ') ?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Kendaraan
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-9">
								<?php echo dropdown('PUNYA_KENDARAAN', array('PUNYA' => 'PUNYA', 'TIDAK PUNYA' => 'TIDAK'), set_value('PUNYA_KENDARAAN', $EDIT->PUNYA_KENDARAAN), ' class="form-control" id="PUNYA_KENDARAAN"') ?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Jenis Kendaraan
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-9">
								<?php echo dropdown('JENIS_KENDARAAN', array('MOTOR' => 'MOTOR', 'MOBIL' => 'MOBIL'), set_value('JENIS_KENDARAAN', $EDIT->JENIS_KENDARAAN), ' class="form-control" id="JENIS_KENDARAAN"') ?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Milik
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-9">
								<?php echo dropdown('MILIK_KENDARAAN', array('SENDIRI' => 'SENDIRI', 'ORANG TUA' => 'ORANG TUA', 'KANTOR' => 'KANTOR'), set_value('MILIK_KENDARAAN', $EDIT->MILIK_KENDARAAN), ' class="form-control" id="MILIK_KENDARAAN"') ?>
							</div>
						</div>
					</div>
					<!-- sisi kanan -->
					<div class="col-md-6">
						<div class="form-group">
							<label for="" class="col-sm-3 control-label"></label>
							<label for="" class="col-sm-9 control-label" style="text-align: left; border-bottom: 2px solid #eee; padding-bottom: 10px;">
								Isi sesuai tempat tinggal KTP
							</label>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Alamat
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-9">
								<input type="text" name="ALAMAT_KTP" value="<?php echo set_value('ALAMAT_KTP', $EDIT->ALAMAT_KTP) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Kelurahan
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-9">
								<input type="text" name="KELURAHAN_KTP" value="<?php echo set_value('KELURAHAN_KTP', $EDIT->KELURAHAN_KTP) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Kecamatan
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-9">
								<input type="text" name="KECAMATAN_KTP" value="<?php echo set_value('KECAMATAN_KTP', $EDIT->KECAMATAN_KTP) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Provinsi
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-9">
								<select name="PROVINSI_KTP" id="PROVINSI_KTP" class="form-control" style="width: 100%;">
									<?php
									$K = db_first(" SELECT * FROM provinsi WHERE PROVINSI='" . db_escape(set_value('PROVINSI_KTP', $EDIT->PROVINSI_KTP)) . "' ");
									if (isset($K->PROVINSI)) {
										echo '<option value="' . $K->PROVINSI . '" data-kode="' . $K->PROVINSI_ID . '" selected="selected">' . $K->PROVINSI . '</option>';
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Kota
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-9">
								<select name="KOTA_KTP" id="KOTA_KTP" class="form-control" style="width: 100%;">
									<?php
									$K = db_first(" SELECT * FROM kota WHERE KOTA='" . db_escape(set_value('KOTA_KTP', $EDIT->KOTA_KTP)) . "' ");
									if (isset($K->KOTA)) {
										echo '<option value="' . $K->KOTA . '" selected="selected">' . $K->KOTA . '</option>';
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Kode Pos
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-3">
								<input type="text" name="KODE_POS_KTP" value="<?php echo set_value('KODE_POS_KTP', $EDIT->KODE_POS_KTP) ?>" class="form-control" style="text-align:center;">
							</div>
							<label for="" class="col-sm-1 control-label">RT
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-2">
								<input type="text" name="RT_KTP" value="<?php echo set_value('RT_KTP', $EDIT->RT_KTP) ?>" class="form-control" style="text-align:center;">
							</div>
							<label for="" class="col-sm-1 control-label">RW
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-2">
								<input type="text" name="RW_KTP" value="<?php echo set_value('RW_KTP', $EDIT->RW_KTP) ?>" class="form-control" style="text-align:center;">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">
								Nomor KK
							</label>
							<div class="col-sm-5">
								<input type="text" name="NO_KK" value="<?php echo set_value('NO_KK', $EDIT->NO_KK) ?>" class="form-control" style="text-align:center;">
							</div>
							<div class="col-sm-3">
								<input type="file" name="FILE_KK" class="form-control" title="Upload Scan e-KTP">
							</div>
							<?php if (!empty($EDIT->FILE_KK) and url_exists(base_url() . 'uploads/cv/' . rawurlencode($EDIT->FILE_KK))) { ?>
								<div class="col-sm-1" style="margin-left: -35px;">
									<span class="input-group-btn">
										<a class="btn btn-primary btn-flat" href="<?php echo base_url() . "uploads/cv/" . $EDIT->FILE_KK ?>" download title="Download">
											<span class="glyphicon glyphicon-download"></span>
										</a>
									</span>
								</div>
							<?php } ?>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label"></label>
							<label for="" class="col-sm-9 control-label" style="text-align: left; border-bottom: 2px solid #eee; padding-bottom: 35px;">
							</label>
						</div>

						<div class="form-group">
							<label for="" class="col-sm-3 control-label"></label>
							<label for="" class="col-sm-9 control-label" style="text-align: left; border-bottom: 2px solid #eee; padding-bottom: 10px;">
								Isi sesuai dengan tempat tinggal sekarang
							</label>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Alamat
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-9">
								<input type="text" name="ALAMAT" value="<?php echo set_value('ALAMAT', $EDIT->ALAMAT) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Kelurahan
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-9">
								<input type="text" name="KELURAHAN" value="<?php echo set_value('KELURAHAN', $EDIT->KELURAHAN) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Kecamatan
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-9">
								<input type="text" name="KECAMATAN" value="<?php echo set_value('KECAMATAN', $EDIT->KECAMATAN) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Provinsi
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-9">
								<select name="PROVINSI" id="PROVINSI" class="form-control" style="width: 100%;">
									<?php
									$K = db_first(" SELECT * FROM provinsi WHERE PROVINSI='" . db_escape(set_value('PROVINSI', $EDIT->PROVINSI)) . "' ");
									if (isset($K->PROVINSI)) {
										echo '<option value="' . $K->PROVINSI . '" data-kode="' . $K->PROVINSI_ID . '" selected="selected">' . $K->PROVINSI . '</option>';
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Kota
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-9">
								<select name="KOTA" id="KOTA" class="form-control" style="width: 100%;">
									<?php
									$K = db_first(" SELECT * FROM kota WHERE KOTA='" . db_escape(set_value('KOTA', $EDIT->KOTA)) . "' ");
									if (isset($K->KOTA)) {
										echo '<option value="' . $K->KOTA . '" selected="selected">' . $K->KOTA . '</option>';
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Kode Pos
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-3">
								<input type="text" name="KODE_POS" value="<?php echo set_value('KODE_POS', $EDIT->KODE_POS) ?>" class="form-control" style="text-align:center;">
							</div>
							<label for="" class="col-sm-1 control-label">RT
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-2">
								<input type="text" name="RT" value="<?php echo set_value('RT', $EDIT->RT) ?>" class="form-control" style="text-align:center;">
							</div>
							<label for="" class="col-sm-1 control-label">RW
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-2">
								<input type="text" name="RW" value="<?php echo set_value('RW', $EDIT->RW) ?>" class="form-control" style="text-align:center;">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label"></label>
							<label for="" class="col-sm-9 control-label" style="text-align: left; border-bottom: 2px solid #eee; padding-bottom: 35px;">
							</label>
						</div>

						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Tempat Tinggal
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-9">
								<?php echo dropdown('TEMPAT_TINGGAL', array('MILIK SENDIRI' => 'MILIK SENDIRI', 'MILIK ORANG TUA' => 'MILIK ORANG TUA', 'SEWA / KOS / KONTRAK' => 'SEWA / KOS / KONTRAK', 'LAIN-LAIN' => 'LAIN-LAIN'), set_value('TEMPAT_TINGGAL', $EDIT->TEMPAT_TINGGAL), ' class="form-control" id="TEMPAT_TINGGAL"') ?>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">File Foto
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-8">
								<input type="file" name="FOTO" class="form-control">
							</div>
							<?php if (!empty($EDIT->FOTO) and url_exists(base_url() . 'uploads/foto/' . rawurlencode($EDIT->FOTO))) { ?>
								<div class="col-sm-1" style="margin-left: -35px;">
									<span class="input-group-btn">
										<a class="btn btn-primary btn-flat" href="<?php echo base_url() . "uploads/foto/" . $EDIT->FOTO ?>" download title="Download">
											<span class="glyphicon glyphicon-download"></span>
										</a>
									</span>
								</div>
							<?php } ?>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Scan Ijazah</label>
							<div class="col-sm-9">
								<?php echo dropdown('SCAN_IJAZAH', array('Scan Asli' => 'SCAN ASLI', 'Scan Copy' => 'SCAN COPY'), set_value('SCAN_IJAZAH', $EDIT->SCAN_IJAZAH), ' class="form-control" ') ?>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">File Scan Ijazah
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-8">
								<input type="file" name="FILE_SCAN_IJAZAH" class="form-control">
							</div>
							<?php if (!empty($EDIT->IJAZAH) and url_exists(base_url() . 'uploads/ijazah/' . rawurlencode($EDIT->IJAZAH))) { ?>
								<div class="col-sm-1" style="margin-left: -35px;">
									<span class="input-group-btn">
										<a class="btn btn-primary btn-flat" href="<?php echo base_url() . "uploads/ijazah/" . $EDIT->IJAZAH ?>" download title="Download">
											<span class="glyphicon glyphicon-download"></span>
										</a>
									</span>
								</div>
							<?php } ?>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">File Cv
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-8">
								<input type="file" name="CV" class="form-control">
							</div>
							<?php if (!empty($EDIT->CV) and url_exists(base_url() . 'uploads/cv/' . rawurlencode($EDIT->CV))) { ?>
								<div class="col-sm-1" style="margin-left: -35px;">
									<span class="input-group-btn">
										<a class="btn btn-primary btn-flat" href="<?php echo base_url() . "uploads/cv/" . $EDIT->CV ?>" download title="Download">
											<span class="glyphicon glyphicon-download"></span>
										</a>
									</span>
								</div>
							<?php } ?>
						</div>




					</div>
					<!-- end kanan -->
				</div>

				<div class="row" style="margin-top:25px;">
					<div class="col-md-12">
						<!-- #tambah AGUNG LONG LAT -->
						<div class="form-group bg-info">

							<label for="" class="col-md-12 control-label" style="text-align: center; border-bottom: 2px solid #eee; padding-bottom: 10px;">
								SETTING DATA LOCATION
							</label>
						</div>
						<div class="form-group">
							<label for="" class="col-md-1 control-label">LONGITUDE
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-md-10">
								<input type="text" name="LONGITUDE" value="<?php echo set_value('LONGITUDE', $EDIT->LONGITUDE) ?>" class="form-control" style="text-align:center;">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-md-1 control-label">LATITUDE
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-md-10">
								<input type="text" name="LATITUDE" value="<?php echo set_value('LATITUDE', $EDIT->LATITUDE) ?>" class="form-control" style="text-align:center;">
							</div>

						</div>

						<div class="form-group">
							<label for="" class="col-md-1 control-label">STATUS</label>
							<div class="col-md-10">
								<?php echo dropdown('COMPLETED', array('0' => 'NOT COMPLETED', '1' => 'COMPLETED'), set_value('COMPLETED', $EDIT->COMPLETED), ' class="form-control" ') ?>

							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-md-1 control-label">AKTIVASI ESS</label>
							<div class="col-md-10">
								<?php echo dropdown('IS_ACTIVE', array('0' => 'NOT ACTIVE', '1' => 'ACTIVE'), set_value('IS_ACTIVE', $EDIT->IS_ACTIVE), ' class="form-control" ') ?>

							</div>
						</div>
					</div>
				</div>

				<?php /* submit data indentitas */ ?>
				<hr style="margin-bottom: 10px;">
				<div class="form-group text-center" style="padding-left: 20px;">
					<button name="UPDATE_TYPE" type="submit" value="DATA_PELAMAR" class="btn btn-primary" style="width: 400px;" onclick="$('#form').submit()">
						<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save
					</button>
				</div>
			</div>
			<?php /* END OF TAB 1 */ ?>

			<?php /* TAB 2 : SETUP */ ?>
			<?php if (has_access('karyawan.view_gaji')) { ?>
				<div role="tabpanel" class="tab-pane" id="tab2">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group bg-info text-center" style="margin-bottom: 20px;">
								<label for="" class="control-label" style="border-bottom: 2px solid #eee; padding-bottom: 10px;">
									SETUP UMUM
								</label>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">NIK</label>
								<div class="col-sm-9">
									<input type="text" name="NIK" value="<?php echo set_value('NIK', $EDIT->NIK) ?>" class="form-control" maxlength="20">
								</div>
							</div>
							<?php if ($OP == 'edit') { ?>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Tanggal Masuk</label>
									<div class="col-sm-9">
										<input type="date" name="TGL_MASUK" value="<?php echo set_value('TGL_MASUK', $EDIT->TGL_MASUK) ?>" class="form-control" maxlength="20">
									</div>
								</div>
							<?php } ?>
							<?php if ($OP == 'edit') { ?>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">PIN / ID</label>
									<div class="col-sm-9">
										<input type="text" name="PIN" value="<?php echo set_value('KARYAWAN_ID', $EDIT->KARYAWAN_ID) ?>" class="form-control" maxlength="20" readonly>
									</div>
								</div>
							<?php } ?>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">No Kontrak</label>
								<div class="col-sm-9">
									<input type="text" name="NO_KONTRAK" value="<?php echo set_value('NO_KONTRAK', $EDIT->NO_KONTRAK) ?>" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">Jenis</label>
								<div class="col-sm-9">
									<?php /* tetap, kontrak, vendor, magang, freelance */ ?>
									<?php echo dropdown('JENIS', array('TETAP' => 'TETAP', 'KONTRAK' => 'KONTRAK', 'VENDOR' => 'VENDOR', 'MAGANG' => 'MAGANG', 'FREELANCE' => 'FREELANCE'), set_value('JENIS', $EDIT->JENIS), ' class="form-control" id="jenis"'); ?>
								</div>
							</div>
							<?php /*
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Penempatan</label>
								<div class="col-sm-9">
									<input type="text" name="PENEMPATAN" value="<?php echo set_value('PENEMPATAN',$EDIT->PENEMPATAN) ?>" class="form-control">
								</div>
							</div>
							*/ ?>
							<div class="form-group" id="tgl_keluar">
								<label for="" class="col-sm-3 control-label">Tgl Keluar</label>
								<div class="col-sm-9">
									<input type="text" name="TGL_KELUAR" value="<?php echo set_value('TGL_KELUAR', cdate($EDIT->TGL_KELUAR)) ?>" class="form-control datepicker" autocomplete="off">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">Tipe</label>
								<div class="col-sm-9">
									<?php echo dropdown('TIPE', array('MANAJEMEN' => 'MANAJEMEN', 'NON MANAJEMEN' => 'NON MANAJEMEN'), set_value('TIPE', $EDIT->TIPE), ' class="form-control" ') ?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">Status PTKP</label>
								<div class="col-sm-9">
									<?php echo dropdown('STATUS_PTKP', array('TK/0' => 'TK/0', 'TK/1' => 'TK/1', 'TK/2' => 'TK/2', 'TK/3' => 'TK/3', 'K/0' => 'K/0', 'K/1' => 'K/1', 'K/2' => 'K/2', 'K/3' => 'K/3', 'K/I/0' => 'K/I/0', 'K/I/1' => 'K/I/1', 'K/I/2' => 'K/I/2', 'K/I/3' => 'K/I/3'), set_value('STATUS_PTKP', $EDIT->STATUS_PTKP), ' class="form-control" ') ?>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Pendidikan Terakhir</label>
								<div class="col-sm-9">
									<input type="text" name="LULUSAN" value="<?php echo set_value('LULUSAN', $EDIT->LULUSAN) ?>" class="form-control " autocomplete="off">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Jurusan</label>
								<div class="col-sm-9">
									<input type="text" name="JURUSAN_" value="<?php echo set_value('JURUSAN', $EDIT->JURUSAN) ?>" class="form-control " autocomplete="off">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Tahun Lulus</label>
								<div class="col-sm-9">
									<input type="text" name="TAHUN_LULUS" value="<?php echo set_value('TAHUN_LULUS', $EDIT->TAHUN_LULUS) ?>" class="form-control " autocomplete="off">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">LAMA BEKERJA SEBELUMNYA</label>
								<div class="col-sm-9">
									<input type="text" name="PENGALAMAN" value="<?php echo set_value('PENGALAMAN', $EDIT->PENGALAMAN) ?>" class="form-control " autocomplete="off">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">NAMA IBU KANDUNG</label>
								<div class="col-sm-9">
									<input type="text" name="IBU_KANDUNG" value="<?php echo set_value('IBU_KANDUNG', $EDIT->IBU_KANDUNG) ?>" class="form-control " autocomplete="off">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">Setup BPJS JHT</label>
								<div class="col-sm-9">
									<?php echo dropdown('R_BPJS_JHT', array('0' => 'Tidak Aktif', '1' => 'Aktif'), set_value('R_BPJS_JHT', $EDIT->R_BPJS_JHT), ' class="form-control" ') ?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">Setup BPJS JP</label>
								<div class="col-sm-9">
									<?php echo dropdown('R_BPJS_JP', array('0' => 'Tidak Aktif', '1' => 'Aktif'), set_value('R_BPJS_JP', $EDIT->R_BPJS_JP), ' class="form-control" ') ?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">Setup BPJS JKK</label>
								<div class="col-sm-9">
									<?php echo dropdown('R_BPJS_JKK', array('0' => 'Tidak Aktif', '1' => 'Aktif'), set_value('R_BPJS_JKK', $EDIT->R_BPJS_JKK), ' class="form-control" ') ?>
								</div>
							</div>

							<!-- 
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">BPJS JHT</label>
								<div class="col-sm-9">
									<input type="text" name="BPJS_JHT" value="<?php /* echo set_value('BPJS_JHT', $EDIT->BPJS_JHT) */ ?>" class="form-control currency" maxlength="20" read>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">BPJS JP</label>
								<div class="col-sm-9">
									<input type="text" name="BPJS_JP" value="<?php /* echo set_value('BPJS_JP', $EDIT->BPJS_JP) */ ?>" class="form-control currency" maxlength="20">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">BPJS KES</label>
								<div class="col-sm-9">
									<input type="text" name="BPJS_KES" value="<?php /* echo set_value('BPJS_KES', $EDIT->BPJS_KES) */ ?>" class="form-control currency" maxlength="20">
								</div>
							</div> 
							-->
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">PPH 21</label>
								<div class="col-sm-9">
									<input type="text" value="<?php echo set_value('PPH21', $EDIT->PPH21) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Tanggal SK</label>
								<div class="col-sm-9">
									<input type="date" name="TGL_SK" value="<?php echo set_value('TGL_SK', $EDIT->TGL_SK) ?>" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Tgl Kontrak Mulai</label>
								<div class="col-sm-3">
									<input type="date" name="TGL_MULAI_KONTRAK" value="<?php echo set_value('TGL_MULAI_KONTRAK', $EDIT->TGL_MULAI_KONTRAK) ?>" class="form-control">
								</div>
								<label for="" class="col-sm-3 control-label">Tgl Kontrak Selesai</label>
								<div class="col-sm-3">
									<input type="date" name="TGL_SELESAI_KONTRAK" value="<?php echo set_value('TGL_SELESAI_KONTRAK', $EDIT->TGL_SELESAI_KONTRAK) ?>" class="form-control">
								</div>

							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Nama Bank</label>
								<div class="col-sm-9">
									<input type="text" name="NAMA_BANK" value="<?php echo set_value('NAMA_BANK', $EDIT->NAMA_BANK) ?>" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Atas Nama</label>
								<div class="col-sm-9">
									<input type="text" name="AKUN_BANK" value="<?php echo set_value('AKUN_BANK', $EDIT->AKUN_BANK) ?>" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">No Rekening</label>
								<div class="col-sm-9">
									<input type="text" name="NO_REKENING" value="<?php echo set_value('NO_REKENING', $EDIT->NO_REKENING) ?>" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Deskripsi Pekerjaan</label>
								<div class="col-sm-9">
									<textarea class="form-control" rows="4" name="JOBDESC"><?php echo isset($EDIT->JOBDESC) ? $EDIT->JOBDESC : '' ?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Str. Organisasi</label>
								<div class="col-sm-9">
									<?php echo dropdown('STRUKTUR_ID', $STRUKTUR_ORGANISASI_OPTION, set_value('STRUKTUR_ID', $EDIT->STRUKTUR_ID), ' class="form-control"') ?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">Setup BPJS JKM</label>
								<div class="col-sm-9">
									<?php echo dropdown('R_BPJS_JKM', array('0' => 'Tidak Aktif', '1' => 'Aktif'), set_value('R_BPJS_JKM', $EDIT->R_BPJS_JKM), ' class="form-control" ') ?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">Setup BPJS KES</label>
								<div class="col-sm-9">
									<?php echo dropdown('R_BPJS_KES', array('0' => 'Tidak Aktif', '1' => 'Aktif'), set_value('R_BPJS_KES', $EDIT->R_BPJS_KES), ' class="form-control" ') ?>
								</div>
							</div>

							<!-- 
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">BPJS JKK</label>
								<div class="col-sm-9">
									<input type="text" name="BPJS_JKK" value="<?php /* echo set_value('BPJS_JKK', $EDIT->BPJS_JKK) */ ?>" class="form-control currency" maxlength="20">
								</div>
							</div> 
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">BPJS JKM</label>
								<div class="col-sm-9">
									<input type="text" name="BPJS_JKM" value="<?php /* echo set_value('BPJS_JKM', $EDIT->BPJS_JKM) */ ?>" class="form-control currency" maxlength="20">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">BPJS JHT Perusahaan</label>
								<div class="col-sm-9">
									<input type="text" name="BPJS_JHT_PERUSAHAAN" value="<?php /* echo set_value('BPJS_JHT_PERUSAHAAN', $EDIT->BPJS_JHT_PERUSAHAAN) */ ?>" class="form-control currency" maxlength="20">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">BPJS JP Perusahaan</label>
								<div class="col-sm-9">
									<input type="text" name="BPJS_JP_PERUSAHAAN" value="<?php /* echo set_value('BPJS_JP_PERUSAHAAN', $EDIT->BPJS_JP_PERUSAHAAN) */ ?>" class="form-control currency" maxlength="20">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">BPJS KES Perusahaan</label>
								<div class="col-sm-9">
									<input type="text" name="BPJS_KES_PERUSAHAAN" value="<?php /* echo set_value('BPJS_KES_PERUSAHAAN', $EDIT->BPJS_KES_PERUSAHAAN) */ ?>" class="form-control currency" maxlength="20">
								</div>
							</div> 
							-->
						</div>
					</div>
					<!-- end row -->


					<div class="row">
						<div class="col-md-12">
							<div class="form-group bg-info text-center" style="margin-bottom: 20px;">
								<label for="" class="control-label" style="border-bottom: 2px solid #eee; padding-bottom: 10px;">
									INFORMASI PPH
								</label>
							</div>
						</div>
					</div>

					<div class="upah-tetap">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group" style="text-align: left; border-bottom: 2px solid #eee; padding-bottom: 10px; padding-left: 40px;">
									<label for="" class="control-label">
										Upah Tetap
									</label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">Gaji Pokok</label>
								<div class="col-sm-9">
									<input type="text" value="<?php echo set_value('GAJI_POKOK_INFO', $EDIT->GAJI_POKOK) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
							<?php
							$TOTAL_TUNJANGAN = $EDIT->TUNJ_JABATAN + $EDIT->TUNJ_KEAHLIAN + $EDIT->TUNJ_PROYEK + $TUNJ_KEAHLIAN + $TUNJ_PROYEK + $TUNJ_BACKUP + $TUNJ_BACKUP;
							?>
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">Tunjangan Keluarga</label>
								<div class="col-sm-9">
									<input type="text" value="<?php echo $EDIT->TUNJ_KELUARGA ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
						</div>

						<div class="row" style="margin-top:10px;">
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">Total Upah Tetap</label>
								<div class="col-sm-9">
									<?php
									$TOTAL_GJ_POKOK = $EDIT->GAJI_POKOK + $EDIT->TUNJ_KELUARGA;
									?>
									<input type="text" value="<?php echo set_value('TOTAL_GJ_POKOK', $TOTAL_GJ_POKOK) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
						</div>
					</div>

					<div class="tunjangan-jabatan">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group" style="text-align: left; border-bottom: 2px solid #eee; padding: 30px 0 10px; padding-left: 40px;">
									<label for="" class="control-label">
										Tunjangan Jabatan
									</label>
								</div>
							</div>
						</div>

						<div class="row" style="margin-top:10px;">
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">Tunj KepSek/ Wakasek</label>
								<div class="col-sm-9">
									<?php
									$TUNJ_LAINNYA_1 = $EDIT->TUNJ_LAINNYA_1;
									?>
									<input type="text" value="<?php echo set_value('TUNJ_LAINNYA_1', $TUNJ_LAINNYA_1) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">Tunj Staff Kurikulum</label>
								<div class="col-sm-9">
									<?php
									$TUNJ_LAINNYA_2 = $EDIT->TUNJ_LAINNYA_2;
									?>
									<input type="text" value="<?php echo set_value('TUNJ_LAINNYA_2', $TUNJ_LAINNYA_2) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
						</div>

						<div class="row" style="margin-top:10px;">
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">Tunj Ketua MGMP</label>
								<div class="col-sm-9">
									<?php
									$TUNJ_LAINNYA_3 = $EDIT->TUNJ_LAINNYA_3;
									?>
									<input type="text" value="<?php echo set_value('TUNJ_LAINNYA_3', $TUNJ_LAINNYA_3) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">Tunj Walikelas</label>
								<div class="col-sm-9">
									<?php
									$TUNJ_LAINNYA_4 = $EDIT->TUNJ_LAINNYA_4;
									?>
									<input type="text" value="<?php echo set_value('TUNJ_LAINNYA_4', $TUNJ_LAINNYA_4) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
						</div>

						<div class="row" style="margin-top:10px;">
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">Tunj Kelebihan Jam Mengajar</label>
								<div class="col-sm-9">
									<?php
									$TUNJ_LAINNYA_5 = $EDIT->TUNJ_LAINNYA_5;
									?>
									<input type="text" value="<?php echo set_value('TUNJ_LAINNYA_5', $TUNJ_LAINNYA_5) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">Tunj Kinerja</label>
								<div class="col-sm-9">
									<?php
									$TUNJ_KINERJA = $EDIT->TUNJ_KINERJA;
									?>
									<input type="text" value="<?php echo set_value('TUNJ_KINERJA', $TUNJ_KINERJA) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
						</div>

						<div class="row" style="margin-top:10px;">
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">Total Tunjangan Jabatan</label>
								<div class="col-sm-9">
									<?php
									$TOTAL_TUNJ_JABATAN = $TUNJ_LAINNYA_1 + $TUNJ_LAINNYA_2 + $TUNJ_LAINNYA_3 + $TUNJ_LAINNYA_4 + $TUNJ_LAINNYA_5 + $TUNJ_KINERJA;
									?>
									<input type="text" value="<?php echo set_value('TOTAL_TUNJ_JABATAN', $TOTAL_TUNJ_JABATAN) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>

						</div>
					</div>

					<div class="potongan-bpjs-ketenagakerjaan">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group" style="text-align: left; border-bottom: 2px solid #eee; padding: 30px 0 10px; padding-left: 40px;">
									<label for="" class="control-label">
										Potongan BPJS Ketenagakerjaan
									</label>
								</div>
							</div>
						</div>

						<div class="row" style="margin-top:10px;">
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">JKK karyawan (0%)</label>
								<div class="col-sm-9">
									<?php
									$JKK_KARYAWAN = 0;
									?>
									<input type="text" value="<?php echo set_value('JKK_KARYAWAN', $JKK_KARYAWAN) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">JKK perusahaan(0,24%)</label>
								<div class="col-sm-9">
									<?php
									$JKK_PERUSAHAAN = ($TOTAL_GJ_POKOK * 0.24) / 100;
									?>
									<input type="text" value="<?php echo set_value('JKK_PERUSAHAAN', $JKK_PERUSAHAAN) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
						</div>

						<div class="row" style="margin-top:10px;">
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">JHT karyawan (2%)</label>
								<div class="col-sm-9">
									<?php
									$JHT_KARYAWAN = ($TOTAL_GJ_POKOK * 2) / 100;
									?>
									<input type="text" value="<?php echo set_value('JHT_KARYAWAN', $JHT_KARYAWAN) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">JHT perusahaan(3,7%)</label>
								<div class="col-sm-9">
									<?php
									$JHT_PERUSAHAAN = ($TOTAL_GJ_POKOK * 3.7) / 100;
									?>
									<input type="text" value="<?php echo set_value('JHT_PERUSAHAAN', $JHT_PERUSAHAAN) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
						</div>

						<div class="row" style="margin-top:10px;">
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">JKM karyawan(0%)</label>
								<div class="col-sm-9">
									<?php
									$JKM_KARYAWAN = 0;
									?>
									<input type="text" value="<?php echo set_value('JKM_KARYAWAN', $JKM_KARYAWAN) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">JKM perusahaan(0,3%)</label>
								<div class="col-sm-9">
									<?php
									$JKM_PERUSAHAAN = ($TOTAL_GJ_POKOK * 0.3) / 100;
									?>
									<input type="text" value="<?php echo set_value('JKM_PERUSAHAAN', $JKM_PERUSAHAAN) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
						</div>

						<div class="row" style="margin-top:10px;">
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">JP karyawan(0 %)</label>
								<div class="col-sm-9">
									<?php
									$JP_KARYAWAN = 0;
									?>
									<input type="text" value="<?php echo set_value('JP_KARYAWAN', $JP_KARYAWAN) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">JP perusahaan(0 %)</label>
								<div class="col-sm-9">
									<?php
									$JP_PERUSAHAAN = 0;
									?>
									<input type="text" value="<?php echo set_value('JP_PERUSAHAAN', $JP_PERUSAHAAN) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
						</div>

						<div class="row" style="margin-top:10px;">
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">Total Potongan karyawan</label>
								<div class="col-sm-9">
									<?php
									$TOTAL_BPJS_KETENAGAKERJAAN_KARYAWAN = $JKK_KARYAWAN + $JHT_KARYAWAN + $JKM_KARYAWAN + $JP_KARYAWAN;
									?>
									<input type="text" value="<?php echo set_value('TOTAL_BPJS_KETENAGAKERJAAN_KARYAWAN', $TOTAL_BPJS_KETENAGAKERJAAN_KARYAWAN) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">Total Potongan perusahaan</label>
								<div class="col-sm-9">
									<?php
									$TOTAL_BPJS_KETENAGAKERJAAN_PERUSAHAAN = $JKK_PERUSAHAAN + $JHT_PERUSAHAAN + $JKM_PERUSAHAAN + $JP_PERUSAHAAN;
									?>
									<input type="text" value="<?php echo set_value('TOTAL_BPJS_KETENAGAKERJAAN_PERUSAHAAN', $TOTAL_BPJS_KETENAGAKERJAAN_PERUSAHAAN) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
						</div>
					</div>

					<div class="potongan-bpjs-kesehatan">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group" style="text-align: left; border-bottom: 2px solid #eee; padding: 30px 0 10px; padding-left: 40px;">
									<label for="" class="control-label">
										Potongan BPJS Kesehatan
									</label>
								</div>
							</div>
						</div>

						<div class="row" style="margin-top:10px;">
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">karyawan (1%)</label>
								<div class="col-sm-9">
									<?php
									$BPJS_K_KARYAWAN = ($TOTAL_GJ_POKOK * 1) / 100;
									?>
									<input type="text" value="<?php echo set_value('BPJS_K_KARYAWAN', $BPJS_K_KARYAWAN) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">perusahaan (4%)</label>
								<div class="col-sm-9">
									<?php
									$BPJS_K_PERUSAHAAN = ($TOTAL_GJ_POKOK * 4) / 100;
									?>
									<input type="text" value="<?php echo set_value('BPJS_K_PERUSAHAAN', $BPJS_K_PERUSAHAAN) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
						</div>
					</div>

					<div class="summary-pph">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group" style="text-align: left; border-bottom: 2px solid #eee; padding: 30px 0 10px; padding-left: 40px;">
									<label for="" class="control-label">
										SUMMARY PPH
									</label>
								</div>
							</div>
						</div>

						<div class="row" style="margin-top:10px;">
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">Total Upah Tetap</label>
								<div class="col-sm-9">
									<input type="text" value="<?php echo set_value('TOTAL_GJ_POKOK', $TOTAL_GJ_POKOK) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">Total Potongan Karyawan</label>
								<div class="col-sm-9">
									<?php
									$TOTAL_POTONGAN_KARYAWAN = $JHT_KARYAWAN + $JKM_KARYAWAN + $BPJS_K_KARYAWAN;


									?>
									<input type="text" value="<?php echo set_value('TOTAL_POTONGAN_KARYAWAN', $TOTAL_POTONGAN_KARYAWAN) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
						</div>

						<div class="row" style="margin-top:10px;">
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">Total Tunjangan Jabatan</label>
								<div class="col-sm-9">
									<?php
									$GAJI_DITERIMA = ($TOTAL_GJ_POKOK - $TOTAL_POTONGAN_KARYAWAN) + $TOTAL_TUNJ_JABATAN;
									?>
									<input type="text" value="<?php echo set_value('TOTAL_TUNJ_JABATAN', $TOTAL_TUNJ_JABATAN) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">Gaji Sebulan-Setahun</label>
								<div class="col-sm-9">
									<?php
									$GAJI_SETAHUN = $GAJI_DITERIMA * 12;
									$GAJI_SEBULAN_SETAHUN = 'SEBULAN = ' . currency($GAJI_DITERIMA) . ' - SETAHUN = ' . currency($GAJI_SETAHUN);
									?>
									<input type="text" value="<?php echo set_value('GAJI_SEBULAN_SETAHUN', $GAJI_SEBULAN_SETAHUN) ?>" class="form-control" disabled>
								</div>
							</div>
						</div>

						<div class="row" style="margin-top:10px;">
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">PTKP</label>
								<div class="col-sm-9">
									<?php
									$NILAI_PTKP = db_first(" SELECT NILAI as NILAI FROM ptkp WHERE NAMA = '$EDIT->STATUS_PTKP' ")->NILAI;
									?>
									<input type="text" value="<?php echo set_value('NILAI_PTKP', $NILAI_PTKP) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
							</div>
							<div class="col-md-6">
								<label for="" class="col-sm-3 control-label">PKP</label>
								<div class="col-sm-9">
									<?php
									$PKP = $GAJI_SETAHUN - $NILAI_PTKP;
									if ($PKP > 0) {
									?>

										<input type="text" value="<?php echo set_value('PKP', $PKP) ?>" class="form-control currency" maxlength="20" disabled>
									<?php } ?>
								</div>
							</div>
						</div>

						<?php
						$PROGRESIF = 60000000;
						if ($PKP < 0) $TIPE_PPH = 0;
						if ($PKP <= 60000000 && $PKP > 0) $TIPE_PPH = 1;
						if ($PKP > 60000000 && $PKP <= 250000000) {
							$TIPE_PPH = 2;
							$SELISIH_PKP = $PKP - 60000000;
							$LEVEL_2 = ($SELISIH_PKP * 15) / 100;
						}
						if ($PKP > 250000000 && $PKP <= 500000000) {
							$TIPE_PPH = 3;
							$SELISIH_PKP = $PKP - 60000000;
							$LEVEL_2 = ($SELISIH_PKP * 25) / 100;
						}
						if ($PKP > 500000000) {
							$TIPE_PPH = 4;
							$SELISIH_PKP = $PKP - 60000000;
							$LEVEL_2 = ($SELISIH_PKP * 30) / 100;
						}
						?>

						<div class="row" style="margin-top:20px; padding-left: 40px;">
							<div class="col-md-12" style="text-align:left">
								<?php if ($TIPE_PPH == 0) { ?>
									<p style="font-size: 16px;color:red;"><strong>PKP dibawah angka minimum</strong></p>
								<?php } ?>
								<?php if ($TIPE_PPH == 1) { ?>
									<p style="font-size: 16px;color:red;"><strong>PPh 21 Terutang Setahun (5% x <?= currency($PKP) ?>) </strong></p>
									<?php
									$PPH_TAHUNAN = (($PKP * 5) / 100);
									$PPH_BULANAN = $PPH_TAHUNAN / 12;
									?>
									<div class="row">
										<div class="col-md-6">
											<label for="" class="col-sm-3 control-label">PPH 21 Setahun</label>
											<div class="col-sm-9">
												<input type="text" value="<?php echo set_value('PPH_TAHUNAN', $PPH_TAHUNAN) ?>" class="form-control currency" maxlength="20" disabled>
											</div>
										</div>

										<div class="col-md-6">
											<label for="" class="col-sm-3 control-label">PPH 21 Sebulan</label>
											<div class="col-sm-9">
												<input type="text" name="PPH21" value="<?php echo set_value('PPH_BULANAN', $PPH_BULANAN) ?>" class="form-control currency" maxlength="20" readonly>

											</div>
										</div>
									</div>
								<?php } ?>

								<?php if ($TIPE_PPH == 2) { ?>
									<p style="font-size: 16px;color:red;"><strong>PPh 21 Terutang Setahun Pajak Progresif (5% x <?= currency($PROGRESIF) ?>) + (15% x <?= currency($SELISIH_PKP) ?>)</strong></p>
									<?php
									$PPH_TAHUNAN_LV_1 = (($PKP * 5) / 100);
									$PPH_TAHUNAN_LV_2 = $LEVEL_2;
									$PPH_TAHUNAN = $PPH_TAHUNAN_LV_1 + $PPH_TAHUNAN_LV_2;
									$PPH_BULANAN = $PPH_TAHUNAN / 12;
									?>
									<div class="row">
										<div class="col-md-6">
											<label for="" class="col-sm-3 control-label">PPH 21 Setahun</label>
											<div class="col-sm-9">
												<input type="text" value="<?php echo set_value('PPH_TAHUNAN', $PPH_TAHUNAN) ?>" class="form-control currency" maxlength="20" disabled>
											</div>
										</div>

										<div class="col-md-6">
											<label for="" class="col-sm-3 control-label">PPH 21 Sebulan</label>
											<div class="col-sm-9">
												<input type="text" name="PPH21" value="<?php echo set_value('PPH_BULANAN', $PPH_BULANAN) ?>" class="form-control currency" maxlength="20" readonly>
											</div>
										</div>
									</div>
								<?php } ?>

								<?php if ($TIPE_PPH == 3) { ?>
									<p style="font-size: 16px;color:red;"><strong>PPh 21 Terutang Setahun Pajak Progresif (5% x <?= currency($PROGRESIF) ?>) + (25% x <?= currency($SELISIH_PKP) ?>)</strong></p>
									<?php
									$PPH_TAHUNAN_LV_1 = (($PKP * 5) / 100);
									$PPH_TAHUNAN_LV_2 = $LEVEL_2;
									$PPH_TAHUNAN = $PPH_TAHUNAN_LV_1 + $PPH_TAHUNAN_LV_2;
									$PPH_BULANAN = $PPH_TAHUNAN / 12;
									?>
									<div class="row">
										<div class="col-md-6">
											<label for="" class="col-sm-3 control-label">PPH 21 Setahun</label>
											<div class="col-sm-9">
												<input type="text" value="<?php echo set_value('PPH_TAHUNAN', $PPH_TAHUNAN) ?>" class="form-control currency" maxlength="20" disabled>
											</div>
										</div>

										<div class="col-md-6">
											<label for="" class="col-sm-3 control-label">PPH 21 Sebulan</label>
											<div class="col-sm-9">
												<input type="text" name="PPH21" value="<?php echo set_value('PPH_BULANAN', $PPH_BULANAN) ?>" class="form-control currency" maxlength="20" readonly>
											</div>
										</div>
									</div>
								<?php } ?>

								<?php if ($TIPE_PPH == 4) { ?>
									<p style="font-size: 16px;color:red;"><strong>PPh 21 Terutang Setahun Pajak Progresif (5% x <?= currency($PROGRESIF) ?>) + (30% x <?= currency($SELISIH_PKP) ?>)</strong></p>
									<?php
									$PPH_TAHUNAN_LV_1 = (($PKP * 5) / 100);
									$PPH_TAHUNAN_LV_2 = $LEVEL_2;
									$PPH_TAHUNAN = $PPH_TAHUNAN_LV_1 + $PPH_TAHUNAN_LV_2;
									$PPH_BULANAN = $PPH_TAHUNAN / 12;
									?>
									<div class="row">
										<div class="col-md-6">
											<label for="" class="col-sm-3 control-label">PPH 21 Setahun</label>
											<div class="col-sm-9">
												<input type="text" value="<?php echo set_value('PPH_TAHUNAN', $PPH_TAHUNAN) ?>" class="form-control currency" maxlength="20" disabled>
											</div>
										</div>

										<div class="col-md-6">
											<label for="" class="col-sm-3 control-label">PPH 21 Sebulan</label>
											<div class="col-sm-9">
												<input type="text" name="PPH21" value="<?php echo set_value('PPH_BULANAN', $PPH_BULANAN) ?>" class="form-control currency" maxlength="20" readonly>
											</div>
										</div>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>

					<?php /* submit data setup */ ?>
					<div class="form-group text-center" style="margin-top : 20px; padding-left: 20px;">
						<button name="UPDATE_TYPE" type="submit" value="DATA_SETUP" class="btn btn-primary" style="width: 400px;" onclick="$('#form').submit()">
							<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save
						</button>
					</div>
				</div>
			<?php } ?>
			<?php /* END OF TAB 2 */ ?>

			<?php /* TAB 3 : KARIR */ ?>
			<?php if (has_access('karyawan.view_gaji')) { ?>
				<div role="tabpanel" class="tab-pane" id="tab3">
					<div class="row">
						<div class="col-md-12">
							<?php /* STATUS KERJA */ ?>
							<div class="form-group">
								<label for="" class="col-sm-1 control-label">Status Kerja</label>
								<div class="col-sm-2">
									<?php echo dropdown('ST_KERJA_INFO', array('' => '-- STATUS UNSET --', 'AKTIF' => 'AKTIF', 'RESIGN' => 'RESIGN', 'PENSIUN' => 'PENSIUN'), set_value('ST_KERJA_INFO', $EDIT->ST_KERJA), ' class="form-control" disabled') ?>
								</div>
								<?php if ($EDIT->ST_KERJA == '') { ?>
									<div class="col-sm-2">
										<?php echo dropdown('ST_KERJA', array('' => '--STATUS BARU--', 'AKTIF' => 'AKTIF'), set_value('ST_KERJA', ''), ' class="form-control"') ?>
									</div>
								<?php } elseif ($EDIT->ST_KERJA == 'AKTIF') { ?>
									<div class="col-sm-2">
										<?php echo dropdown('ST_KERJA', array('' => '--STATUS BARU--', 'PASIF' => 'PASIF', 'RESIGN' => 'RESIGN', 'PENSIUN' => 'PENSIUN'), set_value('ST_KERJA', ''), ' class="form-control"') ?>
									</div>
								<?php } elseif ($EDIT->ST_KERJA == 'RESIGN') { ?>
									<div class="col-sm-2">
										<?php echo dropdown('ST_KERJA', array('' => '--STATUS BARU--', 'AKTIF' => 'AKTIF'), set_value('ST_KERJA', ''), ' class="form-control"') ?>
									</div>
								<?php } ?>
								<div class="col-sm-3">
									<input type="text" name="TGL_ST_KERJA" value="" class="form-control datepicker" autocomplete="off" placeholder="<?php if ($EDIT->ST_KERJA == '') {
																																																																		echo 'Tanggal Gabung (Join Date)';
																																																																	} else {
																																																																		echo 'Tanggal';
																																																																	} ?>">
								</div>
								<div class="col-sm-4">
									<input type="text" name="KET_ST_KERJA" value="" class="form-control" placeholder="Keterangan">
								</div>
							</div>

							<?php /* JENJANG KARIR (LEVEL JABATAN) */ ?>
							<div class="form-group">
								<label for="" class="col-sm-1 control-label">Level Jabatan</label>
								<div class="col-sm-2">
									<?php echo dropdown('JABATAN_ID_INFO', dropdown_option_default('jabatan', 'JABATAN_ID', 'JABATAN', 'ORDER BY JABATAN ASC', '- LVL JABATAN UNSET -'), set_value('JABATAN_ID_INFO', $EDIT->JABATAN_ID), ' class="form-control" disabled') ?>
								</div>
								<div class="col-sm-2">
									<?php echo dropdown('JABATAN_ID', dropdown_option_default('jabatan', 'JABATAN_ID', 'JABATAN', 'ORDER BY JABATAN ASC', '- LVL JABATAN BARU -'), '', ' class="form-control"') ?>
								</div>
								<div class="col-sm-3">
									<input type="text" name="TGL_JABATAN" value="" class="form-control datepicker" autocomplete="off" placeholder="Tanggal">
								</div>
								<div class="col-sm-4">
									<input type="text" name="KET_JABATAN" value="" class="form-control" placeholder="Keterangan">
								</div>
							</div>

							<?php /* GAJI  */ ?>
							<div class="form-group">
								<label for="" class="col-sm-1 control-label">Gaji Pokok</label>
								<div class="col-sm-2">
									<input type="text" name="GAJI_POKOK_INFO" value="<?php echo set_value('GAJI_POKOK_INFO', $EDIT->GAJI_POKOK) ?>" class="form-control currency" maxlength="20" disabled>
								</div>
								<div class="col-sm-2">
									<input type="text" name="GAJI_POKOK" value="" class="form-control currency" maxlength="20" placeholder="Gaji Baru">
								</div>
								<div class="col-sm-3">
									<input type="text" name="TGL_GAJI_POKOK" value="" class="form-control datepicker" autocomplete="off" placeholder="Tanggal">
								</div>
								<div class="col-sm-4">
									<input type="text" name="KET_GAJI_POKOK" value="" class="form-control" placeholder="Keterangan">
								</div>
							</div>

							<?php // ---------- change code ----------- //
							?>
							<?php /* POSISI (JABATAN) */ ?>
							<div class="form-group">
								<label for="" class="col-sm-1 control-label">Jabatan</label>
								<div class="col-sm-2">
									<?php echo dropdown('POSISI_ID_INFO', dropdown_option_default('posisi', 'POSISI_ID', 'POSISI', 'ORDER BY POSISI ASC', '-- JABATAN UNSET --'), set_value('POSISI_ID_INFO', $EDIT->POSISI_ID), ' class="form-control" disabled') ?>
								</div>
								<div class="col-sm-2">
									<?php echo dropdown('POSISI_ID', dropdown_option_default('posisi', 'POSISI_ID', 'POSISI', 'ORDER BY POSISI ASC', '-- JABATAN BARU--'), set_value('POSISI_ID', ''), ' class="form-control"') ?>
								</div>
								<div class="col-sm-3">
									<input type="text" name="TGL_POSISI" value="" class="form-control datepicker" autocomplete="off" placeholder="Tanggal">
								</div>
								<div class="col-sm-4">
									<input type="text" name="KET_POSISI" value="" class="form-control" placeholder="Keterangan">
								</div>
							</div>
							<?php if (empty($EDIT->NIK) && $POSISI->POSISI) { ?>
								<div class="alert alert-info alert-dismissible" role="alert">
									<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<p>Berdasarkan data dari hasil interview, Jabatan yang ditentukan adalah : <b><?php echo $POSISI->POSISI; ?></b></p>
								</div>
							<?php } ?>
							<?php // ---------- end change code ----------- //
							?>


						</div>
					</div>
					<?php /* submit data karir */ ?>
					<div class="form-group text-center" style="padding-left: 20px;">
						<button name="UPDATE_TYPE" type="submit" value="DATA_KARIR" class="btn btn-primary" style="width: 400px;" onclick="$('#form').submit()">
							<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save
						</button>
					</div>
					<?php if (!empty($EDIT->FOTO) and url_exists(base_url() . 'uploads/foto/' . rawurlencode($EDIT->FOTO))) { ?>
						<div class="row">
							<div class="col-md-4" style="padding-left: 130px;">
								<div class="frame">
									<img src="<?php echo base_url() . 'uploads/foto/' . $EDIT->FOTO ?>" style="width: 150px; height: 180px;" title="untuk update foto pada tab Identitas Diri">
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
			<?php /* END OF TAB 3 */ ?>

			<?php /* TAB 4 : TUNJANGAN */ ?>
			<?php if (has_access('karyawan.view_gaji')) { ?>
				<div role="tabpanel" class="tab-pane" id="tab4">
					<div class="row">
						<div class="col-md-6">
							<!-- <div class="form-group">
								<label for="" class="col-sm-4 control-label">Tunj Jabatan (dari master lvl Jabatan)</label>
								<div class="col-sm-8">
									<?php
									$NILAI_TUNJ_JABATAN = db_first("SELECT TUNJ_JABATAN FROM jabatan WHERE JABATAN_ID='$EDIT->JABATAN_ID'")->TUNJ_JABATAN;

									?>
									<input type="text" name="TUNJ_JABATAN" value="<?php echo set_value('TUNJ_JABATAN', $NILAI_TUNJ_JABATAN) ?>" class="form-control currency" maxlength="20">
								</div>
							</div> -->
							<!-- <div class="form-group">
								<label for="" class="col-sm-4 control-label">Tunj Keahlian (Keluarga)</label>
								<div class="col-sm-8">
									<input type="text" name="TUNJ_KEAHLIAN" value="<?php echo set_value('TUNJ_KEAHLIAN', $EDIT->TUNJ_KEAHLIAN) ?>" class="form-control currency" maxlength="20">
								</div>
							</div> -->
							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Tunj KepSek/ Wakasek</label>
								<div class="col-sm-8">
									<input type="text" name="TUNJ_LAINNYA_1" value="<?php echo set_value('TUNJ_LAINNYA_1', $EDIT->TUNJ_LAINNYA_1) ?>" class="form-control currency" maxlength="20">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Tunj Staff Kurikulum </label>
								<div class="col-sm-8">
									<input type="text" name="TUNJ_LAINNYA_2" value="<?php echo set_value('TUNJ_LAINNYA_2', $EDIT->TUNJ_LAINNYA_2) ?>" class="form-control currency" maxlength="20">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Tunj Ketua MGMP</label>
								<div class="col-sm-8">
									<input type="text" name="TUNJ_LAINNYA_3" value="<?php echo set_value('TUNJ_LAINNYA_3', $EDIT->TUNJ_LAINNYA_3) ?>" class="form-control currency" maxlength="20">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Tunj Walikelas</label>
								<div class="col-sm-8">
									<input type="text" name="TUNJ_LAINNYA_4" value="<?php echo set_value('TUNJ_LAINNYA_4', $EDIT->TUNJ_LAINNYA_4) ?>" class="form-control currency" maxlength="20">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Tunj Kelebihan Jam Mengajar</label>
								<div class="col-sm-8">
									<input type="text" name="TUNJ_LAINNYA_5" value="<?php echo set_value('TUNJ_LAINNYA_5', $EDIT->TUNJ_LAINNYA_5) ?>" class="form-control currency" maxlength="20">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Tunj Kinerja</label>
								<div class="col-sm-8">
									<input type="text" name="TUNJ_KINERJA" value="<?php echo set_value('TUNJ_KINERJA', $EDIT->TUNJ_KINERJA) ?>" class="form-control currency" maxlength="20">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Tunj Keluarga</label>
								<div class="col-sm-8">
									<input type="text" name="TUNJ_KELUARGA" value="<?php echo set_value('TUNJ_KELUARGA', $EDIT->TUNJ_KELUARGA) ?>" class="form-control currency" maxlength="20">
								</div>
							</div>


							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Tunj Orang Tua Tunggal Wanita</label>
								<div class="col-sm-8">
									<input type="text" name="TUNJ_OTTW" value="<?php echo set_value('TUNJ_OTTW', $EDIT->TUNJ_OTTW) ?>" class="form-control currency" maxlength="20">
								</div>
							</div>

							<!-- <div class="form-group">
								<label for="" class="col-sm-4 control-label">Tunj Proyek</label>
								<div class="col-sm-8">
									<input type="text" name="TUNJ_PROYEK" value="<?php echo set_value('TUNJ_PROYEK', $EDIT->TUNJ_PROYEK) ?>" class="form-control currency" maxlength="20">
								</div>
							</div> -->
							<!-- <div class="form-group">
								<label for="" class="col-sm-4 control-label">Backup</label>
								<div class="col-sm-8">
									<input type="text" name="TUNJ_BACKUP" value="<?php echo set_value('TUNJ_BACKUP', $EDIT->TUNJ_BACKUP) ?>" class="form-control currency" maxlength="20">
								</div>
							</div> -->

						</div>
						<div class="col-md-6">
							<!-- <div class="form-group">
								<label for="" class="col-sm-4 control-label">Tunj Shift</label>
								<div class="col-sm-8">
									<input type="text" name="TUNJ_SHIFT" value="<?php echo set_value('TUNJ_SHIFT', $EDIT->TUNJ_SHIFT) ?>" class="form-control currency" maxlength="20">
								</div>
							</div> -->
							<div class="form-group">
								<label class="col-sm-4 control-label">Jenis THR</label>
								<div class="col-sm-8">
									<?php echo dropdown('JENIS_THR', array('IDUL FITRI' => 'IDUL FITRI', 'KUNINGAN' => 'KUNINGAN'), set_value('JENIS_THR', $EDIT->JENIS_THR), ' class="form-control" ') ?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Tipe Gaji</label>
								<div class="col-sm-8">
									<?php echo dropdown('TIPE_GAJI', array('MIDDLE' => 'MIDDLE', 'END' => 'END'), set_value('TIPE_GAJI', $EDIT->TIPE_GAJI), ' class="form-control" ') ?>
								</div>
							</div>
						</div>


					</div>
					<?php /* submit data tunjangan */ ?>
					<div class="form-group text-center" style="padding-left: 20px;">
						<button name="UPDATE_TYPE" type="submit" value="DATA_TUNJANGAN" class="btn btn-primary" style="width: 400px;" onclick="$('#form').submit()">
							<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save
						</button>
					</div>
					<!-- end row -->
				</div>
			<?php } ?>
			<?php /* END OF TAB 4 */ ?>

			<?php /* TAB 5 : RIWAYAT KARIR */ ?>
			<?php if (has_access('karyawan.view_gaji')) { ?>
				<div role="tabpanel" class="tab-pane" id="tab5">
					<div class="row">
						<div class="col-md-3">
							<ul class="list-group">
								<?php /* ---------- change code ----------- */ ?>
								<a href="#" class="list-group-item all-history">
									All History
								</a>
								<a href="#" class="list-group-item status-kerja">
									Status Kerja
								</a>
								<a href="#" class="list-group-item jenjang-karir">
									Level Jabatan
								</a>
								<a href="#" class="list-group-item posisi">
									Jabatan
								</a>
								<a href="#" class="list-group-item gaji">
									Gaji
								</a>
								<a href="#" class="list-group-item equipment">
									Equipment
								</a>
								<a href="#" class="list-group-item sp">
									Surat Peringatan
								</a>
								<?php /* ---------- end change code ----------- */ ?>
							</ul>
						</div>
						<div class="col-md-9">
							<div class="panel panel-default" style="box-shadow: none;">

								<div class="panel-heading status-kerja-view">
									Status Kerja
								</div>
								<div style="border-color: transparent" ; class="panel-body entry status-kerja-view">
									<div class="table-responsive">
										<table class="table table-hover table-striped" style="border: 1px solid #f1f1f1;">
											<tr>
												<th style="width:5%;text-align:center;">
													<?php /* if (has_access('riwayat.add')) { ?>
														<a href="javascript:void(0);" class="btn btn-default btn-sm" id="add-status" title="Tambah riwayat Status Kerja">
															<span class="glyphicon glyphicon-plus"></span>
														</a>
													<?php } else { ?>
														#
													<?php } */ ?>
												</th>
												<th style="width:15%;text-align:center;">TANGGAL</th>
												<th style="width:12%;text-align:center;">STATUS</th>
												<th>KETERANGAN</th>
												<!-- <th></th> -->
											</tr>

											<?php $NO = 0;
											if (count($HISTORI_STATUS) > 0) {
												foreach ($HISTORI_STATUS as $row) {
													$NO = $NO + 1; ?>
													<tr>
														<td class="text-center"><?php echo $NO ?></td>
														<td class="text-center"><?php echo tgl($row->TGL) ?></td>
														<td class="text-center"><?php echo $row->HISTORI_STATUS ?></td>
														<td><?php echo $row->KETERANGAN ?></td>
														<?php
														/*
														<td class="text-right">
															<?php if (has_access('riwayat.add')) { ?>
																<a href="<?php echo $_SERVER['PHP_SELF'] . '?op=delete-status&histori_id=' . $row->HISTORI_STATUS_ID . '&id=' . $ID ?>" class="btn btn-danger btn-sm" id="delete-status" title="Hapus riwayat Status Kerja">
																	<span class="glyphicon glyphicon-trash"></span>
																</a>
															<?php } ?>
														</td>
														*/
														?>
													</tr>
												<?php }
											} else { ?>
												<tr>
													<td colspan="5" style="text-align:center;color:#0000ff;">Data Kosong</td>
												</tr>
											<?php } ?>
											<?php if (has_access('riwayat.add')) { ?>
												<tr class="riwayat-status">
													<td></td>
													<td>
														<input type="text" name="TGL_HISTORI_STATUS" value="" class="form-control datepicker" autocomplete="off">
													</td>
													<td>
														<select class="form-control" name="HISTORI_STATUS">
															<option value="AKTIF">AKTIF</option>
															<option value="PASIF">PASIF</option>
															<option value="RESIGN">RESIGN</option>
															<option value="PENSIUN">PENSIUN</option>
														</select>
													</td>
													<td>
														<input type="text" name="KET_HISTORI_STATUS" value="" class="form-control" autocomplete="off">
													</td>
													<td class="text-right">
														<button name="UPDATE_TYPE" type="submit" value="S_HISTORI_STATUS" class="btn btn-primary btn-sm" onclick="$('#form').submit()">
															<span class="glyphicon glyphicon-save"></span>
														</button>
													</td>
												</tr>
											<?php } ?>
										</table>
									</div>
								</div>

								<div class="panel-heading jenjang-karir-view">
									Level Jabatan
								</div>
								<div style="border-color: transparent" ; class="panel-body entry jenjang-karir-view">
									<div class="table-responsive">
										<table class="table table-hover table-striped" style="border: 1px solid #f1f1f1;">
											<tr>
												<th style="width:5%;text-align:center;">
													<?php /* if (has_access('riwayat.add')) { ?>
														<a href="javascript:void(0);" class="btn btn-default btn-sm" id="add-level-jabatan" title="Tambah riwayat level jabatan">
															<span class="glyphicon glyphicon-plus"></span>
														</a>
													<?php } else { ?>
														#
													<?php } */ ?>
												</th>
												<th style="width:15%;text-align:center;">TANGGAL</th>
												<th>LEVEL JABATAN</th>
												<th>PROJECT</th>
												<th>PERUSAHAAN</th>
												<th>KETERANGAN</th>
												<!-- <th></th> -->
											</tr>

											<?php $NO = 0;
											if (count($HISTORI_JABATAN) > 0) {
												foreach ($HISTORI_JABATAN as $row) {
													$NO = $NO + 1; ?>
													<tr>
														<td class="text-center"><?php echo $NO ?></td>
														<td class="text-center"><?php echo tgl($row->TGL) ?></td>
														<td><?php echo $row->JABATAN ?></td>
														<td><?php echo $row->PROJECT ?></td>
														<td><?php echo $row->COMPANY ?></td>
														<td><?php echo $row->KETERANGAN ?></td>
														<?php
														/*
														<td class="text-right">
															<?php if (has_access('riwayat.add')) { ?>
																<a href="<?php echo $_SERVER['PHP_SELF'] . '?op=delete-karir&histori_id=' . $row->HISTORI_KARIR_ID . '&id=' . $ID ?>" class="btn btn-danger btn-sm" id="delete-karir" title="Hapus riwayat level jabatan">
																	<span class="glyphicon glyphicon-trash"></span>
																</a>
															<?php } ?>
														</td>
														*/
														?>
													</tr>
												<?php }
											} else { ?>
												<tr>
													<td colspan="7" style="text-align:center;color:#0000ff;">Data Kosong</td>
												</tr>
											<?php } ?>
											<?php if (has_access('riwayat.add')) { ?>
												<tr class="riwayat-level-jabatan">
													<td></td>
													<td>
														<input type="text" name="TGL_HISTORI_KARIR" value="" class="form-control datepicker" autocomplete="off">
													</td>
													<td>
														<?php echo dropdown('HISTORI_KARIR', dropdown_option_default('jabatan', 'JABATAN_ID', 'JABATAN', 'ORDER BY JABATAN ASC', '- LVL JABATAN -'), '', ' class="form-control"') ?>
													</td>
													<td></td>
													<td></td>
													<td>
														<input type="text" name="KET_HISTORI_KARIR" value="" class="form-control" autocomplete="off">
													</td>
													<td class="text-right">
														<button name="UPDATE_TYPE" type="submit" value="S_HISTORI_KARIR" class="btn btn-primary btn-sm" onclick="$('#form').submit()">
															<span class="glyphicon glyphicon-save"></span>
														</button>
													</td>
												</tr>
											<?php } ?>
										</table>
									</div>
								</div>

								<div class="panel-heading posisi-view">Jabatan</div>
								<div style="border-color: transparent" ; class="panel-body entry posisi-view">
									<div class="table-responsive">
										<table class="table table-hover table-striped" style="border: 1px solid #f1f1f1;">
											<tr>
												<th style="width:5%;text-align:center;">
													<?php /* if (has_access('riwayat.add')) { ?>
														<a href="javascript:void(0);" class="btn btn-default btn-sm" id="add-posisi" title="Tambah riwayat jabatan">
															<span class="glyphicon glyphicon-plus"></span>
														</a>
													<?php } else { ?>
														#
													<?php } */ ?>
												</th>
												<th style="width:15%;text-align:center;">TANGGAL</th>
												<th>JABATAN</th>
												<th>KETERANGAN</th>
												<!-- <th></th> -->
											</tr>
											<?php $NO = 0;
											if (count($HISTORI_POSISI) > 0) {
												foreach ($HISTORI_POSISI as $row) {
													$NO = $NO + 1; ?>
													<tr>
														<td class="text-center"><?php echo $NO ?></td>
														<td class="text-center"><?php echo tgl($row->TGL) ?></td>
														<td><?php echo $row->POSISI ?></td>
														<td><?php echo $row->KETERANGAN ?></td>
														<?php
														/*
														<td class="text-right">
															<?php if (has_access('riwayat.add')) { ?>
																<a href="<?php echo $_SERVER['PHP_SELF'] . '?op=delete-posisi&histori_id=' . $row->HISTORI_POSISI_ID . '&id=' . $ID ?>" class="btn btn-danger btn-sm" id="delete-posisi" title="Hapus riwayat posisi">
																	<span class="glyphicon glyphicon-trash"></span>
																</a>
															<?php } ?>
														</td>
														*/
														?>
													</tr>
												<?php }
											} else { ?>
												<tr>
													<td colspan="5" style="text-align:center;color:#0000ff;">Data Kosong</td>
												</tr>
											<?php } ?>
											<?php if (has_access('riwayat.add')) { ?>
												<tr class="riwayat-posisi">
													<td></td>
													<td>
														<input type="text" name="TGL_HISTORI_POSISI" value="" class="form-control datepicker" autocomplete="off">
													</td>
													<td>
														<?php echo dropdown('HISTORI_POSISI', dropdown_option_default('posisi', 'POSISI_ID', 'POSISI', 'ORDER BY POSISI ASC', '- JABATAN -'), '', ' class="form-control"') ?>
													</td>
													<td>
														<input type="text" name="KET_HISTORI_POSISI" value="" class="form-control" autocomplete="off">
													</td>
													<td class="text-right">
														<button name="UPDATE_TYPE" type="submit" value="S_HISTORI_POSISI" class="btn btn-primary btn-sm" onclick="$('#form').submit()">
															<span class="glyphicon glyphicon-save"></span>
														</button>
													</td>
												</tr>
											<?php } ?>
										</table>
									</div>
								</div>

								<div class="panel-heading gaji-view">Gaji</div>
								<div style="border-color: transparent" ; class="panel-body entry gaji-view">
									<div class="table-responsive">
										<table class="table table-hover table-striped" style="border: 1px solid #f1f1f1;">
											<tr>
												<th style="width:5%;text-align:center;">
													<?php /* if (has_access('riwayat.add')) { ?>
														<a href="javascript:void(0);" class="btn btn-default btn-sm" id="add-gaji" title="Tambah riwayat gaji">
															<span class="glyphicon glyphicon-plus"></span>
														</a>
													<?php } else { ?>
														#
													<?php } */ ?>
													NO.
												</th>
												<th style="width:15%;text-align:center;">TANGGAL</th>
												<th>GAJI</th>
												<!-- <th>KETERANGAN</th> -->
												<th></th>
											</tr>

											<?php $NO = 0;
											if (count($HISTORI_GAJI) > 0) {
												foreach ($HISTORI_GAJI as $row) {

													$NO = $NO + 1; ?>
													<tr>
														<td class="text-center"><?php echo $NO ?></td>
														<td class="text-center"><?php echo tgl($row->TGL) ?></td>
														<td><?php echo currency($row->HISTORI_GAJI) ?></td>
														<td><?php echo $row->KETERANGAN ?></td>
														<?php
														/*
														<td class="text-right">
															<?php if (has_access('riwayat.add')) { ?>
																<a href="<?php echo $_SERVER['PHP_SELF'] . '?op=delete-gaji&histori_id=' . $row->HISTORI_GAJI_ID . '&id=' . $ID ?>" class="btn btn-danger btn-sm" id="delete-gaji" title="Hapus riwayat gaji">
																	<span class="glyphicon glyphicon-trash"></span>
																</a>
															<?php } ?>
														</td>
														*/
														?>
													</tr>
												<?php }
											} else { ?>
												<tr>
													<td colspan="5" style="text-align:center;color:#0000ff;">Data Kosong</td>
												</tr>
											<?php } ?>
											<?php if (has_access('riwayat.add')) { ?>
												<tr class="riwayat-gaji">
													<td></td>
													<td>
														<input type="text" name="TGL_HISTORI_GAJI" value="" class="form-control datepicker" autocomplete="off">
													</td>
													<td>
														<input type="text" name="HISTORI_GAJI" value="" class="form-control currency" autocomplete="off">
													</td>
													<td>
														<input type="text" name="KET_HISTORI_GAJI" value="" class="form-control" autocomplete="off">
													</td>
													<td class="text-right">
														<button name="UPDATE_TYPE" type="submit" value="S_HISTORI_GAJI" class="btn btn-primary btn-sm" onclick="$('#form').submit()">
															<span class="glyphicon glyphicon-save"></span>
														</button>
													</td>
												</tr>
											<?php } ?>
										</table>
									</div>
								</div>

								<div class="panel-heading equipment-view">Equipment</div>
								<div style="border-color: transparent" ; class="panel-body entry equipment-view">
									<div class="table-responsive">
										<table class="table table-hover table-striped" style="border: 1px solid #f1f1f1;">
											<thead>
												<tr>
													<th style="width:5%;text-align:center;">#</th>
													<th style="width:15%;text-align:center;">TANGGAL</th>
													<th>EQUIPMENT</th>
													<th style="text-align: center;">QTY</th>
												</tr>
											</thead>
											<tbody>
												<?php $NO = 0;
												if (count($HISTORI_EQUIPMENT) > 0) {
													foreach ($HISTORI_EQUIPMENT as $row) {
														$NO = $NO + 1; ?>
														<tr>
															<td class="text-center"><?php echo $NO ?></td>
															<td class="text-center"><?php echo tgl($row->TANGGAL_TERIMA) ?></td>
															<td><?php echo $row->NAMA ?></td>
															<td style="text-align: center;"><?php echo $row->QTY ?></td>
														</tr>
													<?php }
												} else { ?>
													<tr>
														<td colspan="4" style="text-align:center;color:#0000ff;">Data Kosong</td>
													</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>

								<?php // ---------- end change code ----------- //
								?>
								<div class="panel-heading sp-view">Surat Peringatan</div>
								<div style="border-color: transparent" ; class="panel-body entry sp-view">
									<div class="table-responsive">
										<table class="table table-hover table-striped" style="border: 1px solid #f1f1f1;">
											<thead>
												<tr>
													<th style="width:5%;text-align:center;">#</th>
													<th style="width:15%;text-align:center;">TANGGAL</th>
													<th>JENIS SP</th>
													<th>KETERANGAN</th>
												</tr>
											</thead>
											<tbody>
												<?php $NO = 0;
												if (count($HISTORI_SP) > 0) {
													foreach ($HISTORI_SP as $row) {
														$NO = $NO + 1; ?>
														<tr>
															<td class="text-center"><?php echo $NO ?></td>
															<td class="text-center"><?php echo tgl($row->TANGGAL) ?></td>
															<td><?php echo $row->SANKSI ?></td>
															<td><?php echo $row->KETERANGAN ?></td>
														</tr>
													<?php }
												} else { ?>
													<tr>
														<td colspan="4" style="text-align:center;color:#0000ff;">Data Kosong</td>
													</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
			<?php /* END OF TAB 5 */ ?>

			<?php /* TAB 6 : DOKUMEN PENDIDIKAN */ ?>
			<?php /* END OF TAB 6 */ ?>

			<?php /* TAB 7 : DOKUMEN KELUARGA */ ?>
			<?php /* END OF TAB 7 */ ?>

			<?php /* TAB 8 : DOKUMEN KARYAWAN */ ?>
			<div role="tabpanel" class="tab-pane" id="tab8">
				<!-- <div class="row" style="margin-bottom: 20px;">
					<div class="col-md-6">
						<div class="form-group">
							<label class="col-sm-3 control-label">Kategori Keahlian</label>
							<div class="col-sm-9">
								<select name="KATEGORI_KEAHLIAN_ID" id="KATEGORI_KEAHLIAN_ID" class="form-control" style="width: 100%;">
									<?php
									$K = db_first(" SELECT * FROM kategori_keahlian WHERE KATEGORI_KEAHLIAN_ID='" . db_escape(set_value('KATEGORI_KEAHLIAN_ID', $EDIT->KATEGORI_KEAHLIAN_ID)) . "' ");
									if (isset($K->KATEGORI_KEAHLIAN_ID)) {
										echo '<option value="' . $K->KATEGORI_KEAHLIAN_ID . '" selected="selected">' . $K->KATEGORI_KEAHLIAN . '</option>';
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Keahlian</label>
							<div class="col-sm-9">
								<select name="KEAHLIAN_ID" id="KEAHLIAN_ID" class="form-control" style="width: 100%;">
									<?php
									$K = db_first(" SELECT * FROM keahlian WHERE KEAHLIAN_ID='" . db_escape(set_value('KEAHLIAN_ID', $EDIT->KEAHLIAN_ID)) . "' ");
									if (isset($K->KEAHLIAN_ID)) {
										echo '<option value="' . $K->KEAHLIAN_ID . '" selected="selected">' . $K->KEAHLIAN . '</option>';
									}
									?>
								</select>
							</div>
						</div>
					</div>
				</div> -->

				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
							<table class="table table table-bordered dokumen">
								<tr style="background-color: #E9ECEF; color: #495057;">
									<th colspan="4">Dokumen Karyawan</th>
									<th class="text-right">
										<span class="input-group-btn" style="display: inline;">
											<button type="button" class="btn btn-primary btn-flat" title="Tambah Data Dokumen" style="width: 150px;" id="add-dokumen">
												<span class="glyphicon glyphicon-plus btn-primary" style="border-bottom: none;"></span> Tambah Data
											</button>
										</span>
									</th>
								</tr>
								<tr>
									<th class="text-center">Dokumen</th>
									<th class="text-center">(Ada/Tidak) / Jenis</th>
									<th class="text-center">File</th>
									<th class="text-center" style="width: 120px;">Tahun Berlaku (YYYY)</th>
									<th class="text-center" style="width: 50px;"></th>
								</tr>
								<?php if (count($LIST_DOK_KARYAWAN) > 0) {
									foreach ($LIST_DOK_KARYAWAN as $row) { ?>
										<tbody>
											<tr>
												<td rowspan="4">
													<input type="text" name="DOK_KARYAWAN[]" value="<?php echo $row->DOK_KARYAWAN ?>" class="form-control">
												</td>
												<td>
													<input type="checkbox" name="IJAZAH[]" value="ADA_IJAZAH" <?php if ($row->IJAZAH == 'ADA_IJAZAH') echo 'checked'; ?>> IJAZAH
													<textarea class="form-control" name="KETERANGAN_IJAZAH[]"><?= $row->KETERANGAN_IJAZAH ?></textarea>
												</td>
												<td>
													<input type="file" name="FILE_IJAZAH[]" class="form-control"> <br>
													<span>
														<?php if (!empty($row->FILE_IJAZAH) and url_exists(base_url() . 'uploads/karyawan/' . rawurlencode($row->FILE_IJAZAH))) { ?>
															<input type="hidden" name="CURR_IJAZAH[]" value="<?php echo $row->FILE_IJAZAH; ?>">
															<a class="btn btn-primary btn-flat btn-sm" href="<?php echo base_url() . "uploads/karyawan/" . $row->FILE_IJAZAH ?>" title="Download" download>
																<i class="glyphicon glyphicon-download"></i> Download Dokumen
															</a>
															<a class="btn btn-warning btn-flat btn-sm btn-del-doc" href="Javascript:void(0)" title="Delete : agar file terhapus click tombol save paling bawah.">
																<i class="glyphicon glyphicon-remove-circle"></i> Delete Dokumen
															</a>

															<?php if ($row->APPROVED_IJAZAH != 'APPROVED') {  ?>
																<button type="button" class="btn btn-flat btn-sm  btn-success " onclick="approved_1('Yakin ingin di Setujui ijazah ?','<?= $row->DOK_KARYAWAN_ID ?>' , 'dok_ijazah')" id="btn_dok_ijazah_<?= $row->DOK_KARYAWAN_ID ?>" title="Approved">
																	<span class="glyphicon glyphicon-plus "></span>
																</button>
															<?php }  ?>

														<?php } ?>

													</span>
												</td>
												<td>
													<input type="text" name="MASA_IJAZAH[]" value="<?php echo $row->MASA_IJAZAH ?>" class="form-control">
												</td>
												<td rowspan="4" class="text-center"> <span class="input-group-btn"> <button type="button" class="btn btn-danger btn-flat del-dokumen" title="Hapus Data : agar file terhapus click tombol save paling bawah."> <span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span> Hapus Data</button>
													</span>
												</td>
											</tr>
											<tr>
												<td>
													<input type="checkbox" name="SERTIFIKAT[]" value="ADA_SERTIFIKAT" <?php if ($row->SERTIFIKAT == 'ADA_SERTIFIKAT') echo 'checked'; ?>> SERTIFIKAT
													<textarea class="form-control" name="KETERANGAN_SERTIFIKAT[]"><?= $row->KETERANGAN_SERTIFIKAT ?></textarea>

												</td>
												<td>
													<input type="file" name="FILE_SERTIFIKAT[]" class="form-control"> <br>
													<span>
														<?php if (!empty($row->FILE_SERTIFIKAT) and url_exists(base_url() . 'uploads/karyawan/' . rawurlencode($row->FILE_SERTIFIKAT))) { ?>
															<input type="hidden" name="CURR_SERTIFIKAT[]" value="<?php echo $row->FILE_SERTIFIKAT; ?>">
															<a class="btn btn-primary btn-flat btn-sm" href="<?php echo base_url() . "uploads/karyawan/" . $row->FILE_SERTIFIKAT ?>" download title="Download">
																<span class="glyphicon glyphicon-download"></span> Download Dokumen
															</a>

															<a class="btn btn-warning btn-flat btn-sm btn-del-doc" href="Javascript:void(0)" title="Delete : agar file terhapus click tombol save paling bawah.">
																<i class="glyphicon glyphicon-remove-circle"></i> Delete Dokumen
															</a>

															<?php if ($row->APPROVED_SERTIFIKAT != 'APPROVED') {  ?>
																<button type="button" class="btn btn-flat btn-sm  btn-success " onclick="approved_1('Yakin ingin di Setujui sertifikat  ?','<?= $row->DOK_KARYAWAN_ID ?>' , 'dok_sertifikat')" id="btn_dok_sertifikat_<?= $row->DOK_KARYAWAN_ID ?>" title="Approved">
																	<span class="glyphicon glyphicon-plus "></span>
																</button>
															<?php }  ?>


														<?php } ?>
													</span>
												</td>
												<td>
													<input type="text" name="MASA_SERTIFIKAT[]" value="<?php echo $row->MASA_SERTIFIKAT ?>" class="form-control">
												</td>
											</tr>
											<tr>
												<td>
													<input type="checkbox" name="SIO[]" value="ADA_SIO" <?php if ($row->SIO == 'ADA_SIO') echo 'checked'; ?>> SK Pegawai Kontrak
													<textarea class="form-control" name="KETERANGAN_SIO[]"><?= $row->KETERANGAN_SIO ?></textarea>

												</td>
												<td>
													<input type="file" name="FILE_SIO[]" class="form-control"> <br>
													<span>
														<?php if (!empty($row->FILE_SIO) and url_exists(base_url() . 'uploads/karyawan/' . rawurlencode($row->FILE_SIO))) { ?>
															<input type="hidden" name="CURR_SIO[]" value="<?php echo $row->FILE_SIO; ?>">
															<a class="btn btn-primary btn-flat btn-sm" href="<?php echo base_url() . "uploads/karyawan/" . $row->FILE_SIO ?>" download title="Download">
																<span class="glyphicon glyphicon-download"></span> Download Dokumen
															</a>
															<a class="btn btn-warning btn-flat btn-sm btn-del-doc" href="Javascript:void(0)" title="Delete : agar file terhapus click tombol save paling bawah.">
																<i class="glyphicon glyphicon-remove-circle"></i> Delete Dokumen
															</a>

															<?php if ($row->APPROVED_SIO != 'APPROVED') {  ?>
																<button type="button" class="btn btn-flat btn-sm  btn-success " onclick="approved_1('Yakin ingin di Setujui SIO  ?','<?= $row->DOK_KARYAWAN_ID ?>' , 'dok_sio')" id="btn_dok_sio_<?= $row->DOK_KARYAWAN_ID ?>" title="Approved">
																	<span class="glyphicon glyphicon-plus "></span>
																</button>
															<?php }  ?>


														<?php } ?>

													</span>
												</td>
												<td>
													<input type="text" name="MASA_SIO[]" value="<?php echo $row->MASA_SIO ?>" class="form-control">
												</td>
											</tr>
											<tr>
												<td>
													<input type="checkbox" name="KTA[]" value="ADA_KTA" <?php if ($row->KTA == 'ADA_KTA') echo 'checked'; ?>> SK Pegawai Tetap
													<textarea class="form-control" name="KETERANGAN_KTA[]"><?= $row->KETERANGAN_KTA ?></textarea>
												</td>
												<td>
													<input type="file" name="FILE_KTA[]" class="form-control"> <br>
													<span>
														<?php if (!empty($row->FILE_KTA) and url_exists(base_url() . 'uploads/karyawan/' . rawurlencode($row->FILE_KTA))) { ?>
															<input type="hidden" name="CURR_KTA[]" value="<?php echo $row->FILE_KTA; ?>">
															<a class="btn btn-primary btn-flat btn-sm" href="<?php echo base_url() . "uploads/karyawan/" . $row->FILE_KTA ?>" download title="Download">
																<span class="glyphicon glyphicon-download"></span> Download Dokumen
															</a>
															<a class="btn btn-warning btn-flat btn-sm btn-del-doc" href="Javascript:void(0)" title="Delete : agar file terhapus click tombol save paling bawah.">
																<i class="glyphicon glyphicon-remove-circle"></i> Delete Dokumen
															</a>

															<?php if ($row->APPROVED_KTA != 'APPROVED') {  ?>
																<button type="button" class="btn btn-flat btn-sm  btn-success " onclick="approved_1('Yakin ingin di Setujui KTA  ?','<?= $row->DOK_KARYAWAN_ID ?>' , 'dok_kta')" id="btn_dok_kta_<?= $row->DOK_KARYAWAN_ID ?>" title="Approved">
																	<span class="glyphicon glyphicon-plus "></span>
																</button>
															<?php }  ?>


														<?php } ?>

													</span>
												</td>
												<td>
													<input type="text" name="MASA_KTA[]" value="<?php echo $row->MASA_KTA ?>" class="form-control">
												</td>
											</tr>
										</tbody>
								<?php }
								} ?>
							</table>
						</div>
					</div>
				</div>

				<?php /* submit data sertifikat */ ?>
				<div class="form-group" style="padding-left: 20px;">
					<button name="UPDATE_TYPE" type="submit" value="DATA_SERTIFIKAT" class="btn btn-primary" onclick="$('#form').submit()">
						<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save
					</button>
				</div>
			</div>
			<?php /* END OF TAB 8 */ ?>

			<?php /* TAB 9 : PENGALAMAN ORGANISASI */ ?>
			<div role="tabpanel" class="tab-pane" id="tab9">
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
							<table class="table table-bordered organisasi">
								<tr style="background-color: #E9ECEF; color: #495057;">
									<th colspan="4">Pengalaman Organisasi</th>
									<th colspan="2" class="text-right">
										<span class="input-group-btn" style="display: inline;">
											<button type="button" class="btn btn-primary btn-flat" title="Tambah Data Organisasi" style="width: 150px;" id="add-organisasi">
												<span class="glyphicon glyphicon-plus btn-primary" style="border-bottom: none;"></span> Tambah Data
											</button>
										</span>
									</th>
								</tr>
								<tr>
									<th class="text-center" style="width: 20px;">No</th>
									<th class="text-center">Nama Organisasi</th>
									<th class="text-center">Jabatan</th>
									<th class="text-center">Lokasi</th>
									<th class="text-center">Periode (Tahun)</th>
									<th class="text-center" style="width: 100px;"></th>
								</tr>
								<?php $ORG_KARYAWAN = db_fetch("SELECT * FROM organisasi_karyawan WHERE KARYAWAN_ID='$ID'");
								if (count($ORG_KARYAWAN) > 0) {
									foreach ($ORG_KARYAWAN as $key => $row) { ?>
										<tr>
											<td><?php echo $key + 1 ?></td>
											<td>
												<input type="text" name="NAMA_ORGANISASI[]" value="<?php echo $row->NAMA_ORGANISASI ?>" class="form-control">
											</td>
											<td>
												<input type="text" name="JABATAN_ORGANISASI[]" value="<?php echo $row->JABATAN_ORGANISASI ?>" class="form-control">
											</td>
											<td>
												<input type="text" name="LOKASI_ORGANISASI[]" value="<?php echo $row->LOKASI_ORGANISASI ?>" class="form-control">
											</td>
											<td>
												<input type="text" name="PERIODE_ORGANISASI[]" value="<?php echo $row->PERIODE_ORGANISASI ?>" class="form-control">
											</td>
											<td class="text-center">
												<span class="input-group-btn mr-2">
													<button type="button" class="btn btn-danger btn-flat del-organisasi" title="Hapus Data">
														<span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span>
													</button>
												</span>



												<?php if ($row->APPROVED != 'APPROVED') {  ?>

													<span class="input-group-btn">
														<button type="button" class="btn btn-success " onclick="approved_1('Yakin ingin di Setujui Pengalaman Organisasi ?','<?= $row->ORGANISASI_KARYAWAN_ID ?>' , 'organisasi')" id="btn_organisasi_<?= $row->ORGANISASI_KARYAWAN_ID ?>" title="Approved">
															<span class="glyphicon glyphicon-plus "></span>
														</button>
													</span>

												<?php }  ?>

											</td>
										</tr>
								<?php }
								} ?>
							</table>
						</div>
						<div class="form-group" style="padding-left: 20px;">
							<button name="UPDATE_TYPE" type="submit" value="DATA_ORGANISASI" class="btn btn-primary" onclick="$('#form').submit()">
								<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save
							</button>
						</div>

					</div>
				</div>
			</div>
			<?php /* END OF TAB 9 */ ?>

			<?php /* TAB 10 : RIWAYAT PEKERJAAN */ ?>
			<div role="tabpanel" class="tab-pane" id="tab10">
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="col-sm-12 control-label" style="text-align: left;">
									Sebutkan tugas, tanggung jawab dan wewenang Pada Jabatan terakhir
									<!--<span style="color:red; padding-left: 5px;">*</span>-->
								</label>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<textarea class="form-control" rows="5" name="TUGAS_JABATAN"><?php echo isset($EDIT->TUGAS_JABATAN) ? $EDIT->TUGAS_JABATAN : '' ?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-12 control-label" style="text-align: left;">
									Jabatan Atasan pada posisi terakhir
									<!--<span style="color:red; padding-left: 5px;">*</span>-->
								</label>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<input type="text" name="JABATAN_ATASAN" value="<?php echo isset($EDIT->JABATAN_ATASAN) ? $EDIT->JABATAN_ATASAN : '' ?>" class="form-control">
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="col-sm-12 control-label" style="text-align: left;">
									Masalah terpenting yang pernah dihadapi dan bagaimana mengatasinya
									<!--<span style="color:red; padding-left: 5px;">*</span>-->
								</label>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<textarea class="form-control" rows="5" name="MASALAH_PENTING"><?php echo isset($EDIT->MASALAH_PENTING) ? $EDIT->MASALAH_PENTING : '' ?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-12 control-label" style="text-align: left;">
									Jumlah anak buah pada posisi terakhir
									<!--<span style="color:red; padding-left: 5px;">*</span>-->
								</label>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<input type="text" name="JUMLAH_ANAK_BUAH" value="<?php echo isset($EDIT->JUMLAH_ANAK_BUAH) ? $EDIT->JUMLAH_ANAK_BUAH : '' ?>" class="form-control">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-12 pengalaman">
					<table class="table table-bordered">
						<tr style="background-color: #E9ECEF; color: #495057;">
							<th>Pengalaman kerja, dimulai dari pekerjaan calon karyawan yang terakhir (saat ini)</th>
							<th class="text-center" style="width: 200px;">
								<span class="input-group-btn" style="display: inline;">
									<button type="button" class="btn btn-primary btn-flat" title="Tmabah Data Pengalaman" style="width: 150px;" id="add-pengalaman">
										<span class="glyphicon glyphicon-plus btn-primary" style="border-bottom: none;"></span> Tambah Data
									</button>
								</span>
							</th>
						</tr>
					</table>
					<?php $PENGALAMAN_KARYAWAN = db_fetch("SELECT * FROM pengalaman_karyawan WHERE KARYAWAN_ID='$ID'");
					if (count($PENGALAMAN_KARYAWAN) > 0) {
						foreach ($PENGALAMAN_KARYAWAN as $key => $row) { ?>
							<table class="table">
								<tr>
									<td class="text-right" style="vertical-align: middle; width: 180px;">Nama Perusahaan</td>
									<td><input type="text" name="NAMA_PERUSAHAAN[]" value="<?php echo $row->NAMA_PERUSAHAAN ?>" class="form-control"></td>
									<td></td>
									<td class="text-right" style="vertical-align: middle; width: 180px;">Jabatan Awal</td>
									<td><input type="text" name="JABATAN_AWAL[]" value="<?php echo $row->JABATAN_AWAL ?>" class="form-control"></td>
								</tr>
								<tr>
									<td class="text-right" style="vertical-align: middle;">Bergerak di Bidang </td>
									<td><input type="text" name="BIDANG_USAHA[]" value="<?php echo $row->BIDANG_USAHA ?>" class="form-control"></td>
									<td></td>
									<td class="text-right" style="vertical-align: middle;">Jabatan Akhir</td>
									<td><input type="text" name="JABATAN_AKHIR[]" value="<?php echo $row->JABATAN_AKHIR ?>" class="form-control"></td>
								</tr>
								<tr>
									<td class="text-right" style="vertical-align: middle;">Alamat</td>
									<td><input type="text" name="ALAMAT_PERUSAHAAN[]" value="<?php echo $row->ALAMAT_PERUSAHAAN ?>" class="form-control"></td>
									<td></td>
									<td class="text-right" style="vertical-align: middle;">Gaji Pokok</td>
									<td><input type="text" name="GAPOK_SEBELUMNYA[]" value="<?php echo $row->GAPOK_SEBELUMNYA ?>" class="form-control currency"></td>
								</tr>
								<tr>
									<td class="text-right" style="vertical-align: middle;">Nama Atasan Langsung </td>
									<td><input type="text" name="ATASAN[]" value="<?php echo $row->ATASAN ?>" class="form-control"></td>
									<td></td>
									<td class="text-right" style="vertical-align: middle;">Tunjangan Lainnya </td>
									<td><input type="text" name="TUNJANGAN_LAINNYA[]" value="<?php echo $row->TUNJANGAN_LAINNYA ?>" class="form-control"></td>
								</tr>
								<tr>
									<td class="text-right" style="vertical-align: middle;">No. Telepon</td>
									<td><input type="text" name="NO_TELP_PERUSAHAAN[]" value="<?php echo $row->NO_TELP_PERUSAHAAN ?>" class="form-control"></td>
									<td></td>
									<td class="text-right" style="vertical-align: middle;">Alasan Pengunduran Diri </td>
									<td><input type="text" name="ALASAN_RESIGN[]" value="<?php echo $row->ALASAN_RESIGN ?>" class="form-control"></td>
								</tr>
								<tr>
									<td class="text-right" style="vertical-align: middle;">Periode Kerja</td>
									<td><input type="text" name="PERIODE_BEKERJA[]" value="<?php echo $row->PERIODE_BEKERJA ?>" class="form-control"></td>
									<td></td>
									<td class="text-right" style="vertical-align: middle;">Deskripsi Pekerjaan </td>
									<td><input type="text" name="DESKRIPSI_PEKERJAAN[]" value="<?php echo $row->DESKRIPSI_PEKERJAAN ?>" class="form-control"></td>
								</tr>
								<tr>
									<td colspan="5" class="text-right">
										<span class="input-group-btn" style="display: inline;">
											<button type="button" class="btn btn-danger btn-flat del-pengalaman" title="Hapus Data" style="width: 150px;">
												<span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span> Hapus Data
											</button>
										</span>

										<?php if ($row->APPROVED != 'APPROVED') {  ?>

											<span class="input-group-btn" style="display: inline;">
												<button type="button" class="btn btn-success " onclick="approved_1('Yakin ingin di Setujui Pengalaman kerja ?','<?= $row->PENGALAMAN_KARYAWAN_ID ?>' , 'kerja')" id="btn_kerja_<?= $row->PENGALAMAN_KARYAWAN_ID ?>" title="Approved">
													<span class="glyphicon glyphicon-plus "></span>
												</button>
											</span>

										<?php }  ?>


									</td>
								</tr>
							</table>
					<?php }
					} ?>
				</div>
				<div class="form-group" style="padding-left: 40px;">
					<button name="UPDATE_TYPE" type="submit" value="DATA_PENGALAMAN" class="btn btn-primary" onclick="$('#form').submit()">
						<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save
					</button>
				</div>
			</div>
			<?php /* END OF TAB 10 */ ?>

			<?php /* TAB 11 : RIWAYAT PEKERJAAN */ ?>
			<div role="tabpanel" class="tab-pane" id="tab11">
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="col-sm-3 control-label" style="text-align: left;">
									Hobi Anda
									<!--<span style="color:red; padding-left: 5px;">*</span>-->
								</label>
								<div class="col-sm-9">
									<input type="text" name="HOBI" value="<?php echo set_value('HOBI', $EDIT->HOBI) ?>" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-12 control-label" style="text-align: left;">
									Apa yang mendorong untuk bekerja
									<!--<span style="color:red; padding-left: 5px;">*</span>-->
								</label>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<textarea class="form-control" rows="5" name="MOTIVASI_BEKERJA"><?php echo isset($EDIT->MOTIVASI_BEKERJA) ? $EDIT->MOTIVASI_BEKERJA : '' ?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-12 control-label" style="text-align: left;">
									Mengapa ingin bekerja di perusahaan kami
									<!--<span style="color:red; padding-left: 5px;">*</span>-->
								</label>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<textarea class="form-control" rows="5" name="MOTIVASI_DIAIRKON"><?php echo isset($EDIT->MOTIVASI_DIAIRKON) ? $EDIT->MOTIVASI_DIAIRKON : '' ?></textarea>
								</div>
							</div>
							<div class="form-group" style="padding-left: 20px;">
								<button name="UPDATE_TYPE" type="submit" value="DATA_MINAT" class="btn btn-primary" onclick="$('#form').submit()">
									<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save
								</button>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="col-sm-3 control-label" style="text-align: left;">
									Gaji yang inginkan
									<!--<span style="color:red; padding-left: 5px;">*</span>-->
								</label>
								<div class="col-sm-9">
									<input type="text" name="EXPECTED_SALARY" value="<?php echo set_value('EXPECTED_SALARY', $EDIT->EXPECTED_SALARY) ?>" class="form-control currency" maxlength="20">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-12 control-label" style="text-align: left;">
									Sebutkan fasilitas lainnya yang diharapkan
									<!--<span style="color:red; padding-left: 5px;">*</span>-->
								</label>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<textarea class="form-control" rows="5" name="FASILITAS_LAINNYA"><?php echo isset($EDIT->FASILITAS_LAINNYA) ? $EDIT->FASILITAS_LAINNYA : '' ?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label" style="text-align: left;">
									Kapan siap bekerja
									<!--<span style="color:red; padding-left: 5px;">*</span>-->
								</label>
								<div class="col-sm-9">
									<input type="text" name="SIAP_BEKERJA" value="<?php echo set_value('SIAP_BEKERJA', $EDIT->SIAP_BEKERJA) ?>" class="form-control datepicker">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label" style="text-align: left;">
									Bersedia di luar daerah
									<!--<span style="color:red; padding-left: 5px;">*</span>-->
								</label>
								<div class="col-sm-9" style="padding-top: 5px;">
									<label class="form-check-label">
										<input class="form-check-input" type="radio" name="LUAR_DAERAH" value="Ya" <?php if ($EDIT->LUAR_DAERAH == 'Ya') echo 'checked' ?>> Ya
									</label>
									<label class="form-check-label" style="padding-left:30px; ">
										<input class="form-check-input" type="radio" name="LUAR_DAERAH" value="Tidak" <?php if ($EDIT->LUAR_DAERAH == 'Tidak') echo 'checked' ?>> Tidak
									</label>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label" style="text-align: left;">
									Jika Tidak, mengapa
									<!--<span style="color:red; padding-left: 5px;">*</span>-->
								</label>
								<div class="col-sm-9">
									<textarea class="form-control" rows="2" name="ALASAN_DILUAR_DAERAH"><?php echo isset($EDIT->ALASAN_DILUAR_DAERAH) ? $EDIT->ALASAN_DILUAR_DAERAH : '' ?></textarea>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php /* END OF TAB 11 */ ?>

			<?php /* TAB 12 : RIWAYAT PEKERJAAN */ ?>
			<div role="tabpanel" class="tab-pane" id="tab12">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="" class="col-sm-2 control-label" style="text-align: left;">
								Sumber informasi lowongan kerja
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-4" style="padding-top: 5px;">
								<label class="form-check-label">
									<input class="form-check-input" type="radio" name="INFO_LOKER" value="Internet" <?php if ($EDIT->INFO_LOKER == 'Internet') echo 'checked' ?>> Internet
								</label>
								<label class="form-check-label" style="padding-left:30px; ">
									<input class="form-check-input" type="radio" name="INFO_LOKER" value="Sosmed" <?php if ($EDIT->INFO_LOKER == 'Sosmed') echo 'checked' ?>> Sosmed
								</label>
								<label class="form-check-label" style="padding-left:30px; ">
									<input class="form-check-input" type="radio" name="INFO_LOKER" value="Iklan" <?php if ($EDIT->INFO_LOKER == 'Jobfair') echo 'checked' ?>> Jobfair
								</label>
								<label class="form-check-label" style="padding-left:30px; ">
									<input class="form-check-input" type="radio" name="INFO_LOKER" value="Lain-Lain" <?php if ($EDIT->INFO_LOKER == 'Lain-Lain') echo 'checked' ?>> Lain-Lain
								</label>
							</div>
							<div class="col-sm-6">
								<input type="text" name="INFO_LOKER_LAINNYA" value="<?php echo set_value('INFO_LOKER_LAINNYA', $EDIT->INFO_LOKER_LAINNYA) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label" style="text-align: left;">
								Saudara yang bekerja di Perusahaan Ini
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-4" style="padding-top: 5px;">
								<label class="form-check-label">
									<input class="form-check-input" type="radio" name="KERABAT_AIRKON" value="Ya" <?php if ($EDIT->KERABAT_AIRKON == 'Ya') echo 'checked' ?>> Ya
								</label>
								<label class="form-check-label" style="padding-left:30px; ">
									<input class="form-check-input" type="radio" name="KERABAT_AIRKON" value="Tidak" <?php if ($EDIT->KERABAT_AIRKON == 'Tidak') echo 'checked' ?>> Tidak
								</label>
							</div>
							<div class="col-sm-6">
								<input type="text" name="NAMA_KERABAT" value="<?php echo set_value('NAMA_KERABAT', $EDIT->NAMA_KERABAT) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label" style="text-align: left;">
								Pernah sakit yang lama sembuh
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-4" style="padding-top: 5px;">
								<label class="form-check-label">
									<input class="form-check-input" type="radio" name="RIWAYAT_KESEHATAN" value="Ya" <?php if ($EDIT->RIWAYAT_KESEHATAN == 'Ya') echo 'checked' ?>> Ya
								</label>
								<label class="form-check-label" style="padding-left:30px; ">
									<input class="form-check-input" type="radio" name="RIWAYAT_KESEHATAN" value="Tidak" <?php if ($EDIT->RIWAYAT_KESEHATAN == 'Tidak') echo 'checked' ?>> Tidak
								</label>
							</div>
							<div class="col-sm-6">
								<input type="text" name="NAMA_PENYAKIT" value="<?php echo set_value('NAMA_PENYAKIT', $EDIT->NAMA_PENYAKIT) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label" style="text-align: left;">
								Dirawat dalam 1 tahun terakhir
								<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-4" style="padding-top: 5px;">
								<label class="form-check-label">
									<input class="form-check-input" type="radio" name="RIWAYAT_RAWAT" value="Ya" <?php if ($EDIT->RIWAYAT_RAWAT == 'Ya') echo 'checked' ?>> Ya
								</label>
								<label class="form-check-label" style="padding-left:30px; ">
									<input class="form-check-input" type="radio" name="RIWAYAT_RAWAT" value="Tidak" <?php if ($EDIT->RIWAYAT_RAWAT == 'Tidak') echo 'checked' ?>> Tidak
								</label>
							</div>

						</div>

						<table class="table table-bordered penanggung">
							<tr style="background-color: #E9ECEF; color: #495057;">
								<th colspan="4">Orang yang dapat dihubungi apabila dalam keadaan darurat, minimal 2(dua)</th>
								<th colspan="2" class="text-right">
									<span class="input-group-btn" style="display: inline;">
										<button type="button" class="btn btn-primary btn-flat" title="Tambah Data" style="width: 150px;" id="add-penanggung">
											<span class="glyphicon glyphicon-plus btn-primary" style="border-bottom: none;"></span> Tambah Data
										</button>
									</span>
								</th>
							</tr>
							<tr>
								<th class="text-center" style="width: 20px;">No</th>
								<th class="text-center">Nama</th>
								<th class="text-center">Alamat</th>
								<th class="text-center">No Telp / HP</th>
								<th class="text-center">Hubungan</th>
								<th style="width: 50px;"></th>
							</tr>
							<?php $PENANGGUNG_KARYAWAN = db_fetch("SELECT * FROM penanggung_karyawan WHERE KARYAWAN_ID='$ID'");
							if (count($PENANGGUNG_KARYAWAN) > 0) {
								foreach ($PENANGGUNG_KARYAWAN as $key => $row) { ?>
									<tr>
										<td><?php echo $no + 1 ?></td>
										<td><input type="text" name="NAMA_PENANGGUNG[]" value="<?php echo $row->NAMA_PENANGGUNG ?>" class="form-control"></td>
										<td><input type="text" name="ALAMAT_PENANGGUNG[]" value="<?php echo $row->ALAMAT_PENANGGUNG ?>" class="form-control"></td>
										<td><input type="text" name="TELP_PENANGGUNG[]" value="<?php echo $row->TELP_PENANGGUNG ?>" class="form-control"></td>
										<td><input type="text" name="HUBUNGAN_PENANGGUNG[]" value="<?php echo $row->HUBUNGAN_PENANGGUNG ?>" class="form-control"></td>
										<td>
											<span class="input-group-btn">
												<button type="button" class="btn btn-danger btn-flat del-penanggung" title="Hapus Data">
													<span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span>
												</button>
											</span>
										</td>
									</tr>
							<?php }
							} ?>
						</table>
						<div class="form-group" style="padding-left: 20px;">
							<button name="UPDATE_TYPE" type="submit" value="DATA_LAIN" class="btn btn-primary" onclick="$('#form').submit()">
								<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save
							</button>
						</div>
					</div>
				</div>
			</div>
			<?php /* END OF TAB 12 */ ?>
		</div>
	</form>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
	$(document).ready(function() {
		// Javascript to enable link to tab
		var hash = document.location.hash;
		var prefix = "tab_";
		if (hash) {
			$('.nav-tabs a[href="' + hash.replace(prefix, "") + '"]').tab('show');
		}

		// Change hash for page-reload
		$('.nav-tabs a').on('shown', function(e) {
			window.location.hash = e.target.hash.replace("#", "#" + prefix);
		});

		var val = '<?php echo $_GET['tab'] ?>';
		if (val != '') {
			//alert(val);
			jQuery(function() {
				jQuery('a[href="#' + val + '"]').tab('show');
			});
		}

		$('#PROVINSI').select2({
			theme: "bootstrap",
			ajax: {
				url: 'provinsi-json.php',
				dataType: 'json',
			}
		});

		$('#PROVINSI').change(function() {
			data = $(this).select2('data')[0];
			$(this).find(':selected').attr('data-kode', data.kode);
		});

		$('#PROVINSI').on('select2:select', function(e) {
			$('#KOTA').val(null).trigger('change');
		});

		$('#KOTA').select2({
			theme: "bootstrap",
			ajax: {
				url: 'kota-json.php',
				dataType: 'json',
				data: function(params) {
					provinsi_id = $('#PROVINSI').find(':selected').attr('data-kode');
					return {
						q: params.term,
						provinsi_id: provinsi_id,
						page_limit: 20
					}
				}
			}
		});

		$('#PROVINSI_KTP').select2({
			theme: "bootstrap",
			ajax: {
				url: 'provinsi-json.php',
				dataType: 'json',
			}
		});

		$('#PROVINSI_KTP').change(function() {
			data = $(this).select2('data')[0];
			$(this).find(':selected').attr('data-kode', data.kode);
		});

		$('#PROVINSI_KTP').on('select2:select', function(e) {
			$('#KOTA_KTP').val(null).trigger('change');
		});

		$('#KOTA_KTP').select2({
			theme: "bootstrap",
			ajax: {
				url: 'kota-json.php',
				dataType: 'json',
				data: function(params) {
					provinsi_id = $('#PROVINSI_KTP').find(':selected').attr('data-kode');
					return {
						q: params.term,
						provinsi_id: provinsi_id,
						page_limit: 20
					}
				}
			}
		});


		$('#KATEGORI_KEAHLIAN_ID').select2({
			theme: "bootstrap",
			ajax: {
				url: 'kategori-keahlian-ac.php',
				dataType: 'json',
			}
		});

		$('#KATEGORI_KEAHLIAN_ID').change(function() {
			data = $(this).select2('data')[0];
			$(this).find(':selected').attr('val', data.id);
		});

		$('#KATEGORI_KEAHLIAN_ID').on('select2:select', function(e) {
			$('#KEAHLIAN_ID').val(null).trigger('change');
		});

		$('#KEAHLIAN_ID').select2({
			theme: "bootstrap",
			ajax: {
				url: 'keahlian-ac.php',
				dataType: 'json',
				data: function(params) {
					kategori_keahlian_id = $('#KATEGORI_KEAHLIAN_ID').find(':selected').attr('val');
					return {
						q: params.term,
						kategori_keahlian_id: kategori_keahlian_id,
						page_limit: 20
					}
				}
			}
		});

		$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
			$('.currency').mask('000,000,000,000,000', {
				reverse: true
			});
			mask();
		})

		$('#kat_kh').on('change', function() {
			<?php $KAT_KH = '$("#kat_kh").val()'; ?>
		});

		delete_dokumen();
		delete_doc_detail();
		delete_organisasi();
		delete_pengalaman();
		delete_penanggung();
	});

	$(function() {
		if ($('#jenis').val() == 'KONTRAK' || $('#jenis').val() == 'MAGANG') {
			$('#tgl_keluar').show();
		} else {
			$('#tgl_keluar').hide();
		}
		//$('#tgl_keluar').hide();
		$('#jenis').change(function() {
			if ($('#jenis').val() == 'KONTRAK' || $('#jenis').val() == 'MAGANG') {
				$('#tgl_keluar').show();
			} else {
				$('#tgl_keluar').hide();
			}
		});

		/* ---------- change code ----------- */
		$('.all-history').click(function() {
			$('.status-kerja-view').show();
			$('.jenjang-karir-view').show();
			$('.posisi-view').show();
			$('.gaji-view').show();
			$('.equipment-view').show();
			$('.sp-view').show();
		});

		$('.status-kerja').click(function() {
			$('.status-kerja-view').show();
			$('.jenjang-karir-view').hide();
			$('.posisi-view').hide();
			$('.gaji-view').hide();
			$('.equipment-view').hide();
			$('.sp-view').hide();
		});

		$('.jenjang-karir').click(function() {
			$('.status-kerja-view').hide();
			$('.jenjang-karir-view').show();
			$('.posisi-view').hide();
			$('.gaji-view').hide();
			$('.equipment-view').hide();
			$('.sp-view').hide();
		});

		$('.posisi').click(function() {
			$('.status-kerja-view').hide();
			$('.jenjang-karir-view').hide();
			$('.posisi-view').show();
			$('.gaji-view').hide();
			$('.equipment-view').hide();
			$('.sp-view').hide();
		});

		$('.gaji').click(function() {
			$('.status-kerja-view').hide();
			$('.jenjang-karir-view').hide();
			$('.posisi-view').hide();
			$('.gaji-view').show();
			$('.equipment-view').hide();
			$('.sp-view').hide();
		});

		$('.equipment').click(function() {
			$('.status-kerja-view').hide();
			$('.jenjang-karir-view').hide();
			$('.posisi-view').hide();
			$('.gaji-view').hide();
			$('.equipment-view').show();
			$('.sp-view').hide();
		});

		$('.sp').click(function() {
			$('.status-kerja-view').hide();
			$('.jenjang-karir-view').hide();
			$('.posisi-view').hide();
			$('.gaji-view').hide();
			$('.equipment-view').hide();
			$('.sp-view').show();
		});

		$('#add-dokumen').click(function(i) {
			$('.dokumen').append('<tbody style="border-top: 0;"><tr><td rowspan="4"><input type="text" name="DOK_KARYAWAN[]" value="" class="form-control"></td><td><input type="checkbox" name="IJAZAH[' + i + 1 + ']" value="ADA_IJAZAH"> IJAZAH <textarea class="form-control" name="KETERANGAN_IJAZAH[]"></textarea></td><td><input type="file" name="FILE_IJAZAH[]" class="form-control"></td><td><input type="text" name="MASA_IJAZAH[]" value="" class="form-control"></td><td rowspan="4"> <span class="input-group-btn"> <button type="button" class="btn btn-danger btn-flat del-dokumen" title="Hapus Data"> <span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span> Hapus Dokumen</button> </span></td></tr><tr><td><input type="checkbox" name="SERTIFIKAT[]" value="ADA_SERTIFIKAT"> SERTIFIKAT <textarea class="form-control" name="KETERANGAN_SERTIFIKAT[]"></textarea></td><td><input type="file" name="FILE_SERTIFIKAT[]" class="form-control"></td><td><input type="text" name="MASA_SERTIFIKAT[]" value="" class="form-control"></td></tr><tr><td><input type="checkbox" name="SIO[]" value="ADA_SIO"> SK Pegawai Kontrak <textarea class="form-control" name="KETERANGAN_SIO[]"></textarea></td><td><input type="file" name="FILE_SIO[]"  class="form-control"></td><td><input type="text" name="MASA_SIO[]" value="" class="form-control"></td></tr><tr><td><input type="checkbox" name="KTA[]" value="ADA_KTA"> SK Pegawai Tetap <textarea class="form-control" name="KETERANGAN_KTA[]"></textarea></td><td><input type="file" name="FILE_KTA[]"  class="form-control"></td><td><input type="text" name="MASA_KTA[]" value="" class="form-control"></td></tr></tbody>');
			return false;
		});

		$('#add-organisasi').click(function(i) {
			$('.organisasi').append('<tr><td></td><td><input type="text" name="NAMA_ORGANISASI[]" class="form-control"></td><td><input type="text" name="JABATAN_ORGANISASI[]" class="form-control"></td><td><input type="text" name="LOKASI_ORGANISASI[]" class="form-control"></td><td><input type="text" name="PERIODE_ORGANISASI[]" class="form-control"></td><td class="text-center"><span class="input-group-btn"><button type="button" class="btn btn-danger btn-flat del-organisasi" title="Hapus Data"><span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span></button></span></td></tr>');
			return false;
		});

		$('#add-pengalaman').click(function(i) {
			$('.pengalaman').append('<table class="table"><tr><td class="text-right" style="vertical-align: middle; width: 180px;">Nama Perusahaan</td><td><input type="text" name="NAMA_PERUSAHAAN[]" class="form-control"></td><td></td><td class="text-right" style="vertical-align: middle; width: 180px;">Jabatan Awal</td><td><input type="text" name="JABATAN_AWAL[]" class="form-control"></td></tr><tr><td class="text-right" style="vertical-align: middle;">Bergerak di Bidang</td><td><input type="text" name="BIDANG_USAHA[]" class="form-control"></td><td></td><td class="text-right" style="vertical-align: middle;">Jabatan Akhir</td><td><input type="text" name="JABATAN_AWKHIR[]" class="form-control"></td></tr><tr><td class="text-right" style="vertical-align: middle;">Alamat</td><td><input type="text" name="ALAMAT_PERUSAHAAN[]" class="form-control"></td><td></td><td class="text-right" style="vertical-align: middle;">Gaji Pokok</td><td><input type="text" name="GAPOK_SEBELUMNYA[]" class="form-control currency"></td></tr><tr><td class="text-right" style="vertical-align: middle;">Nama Atasan Langsung</td><td><input type="text" name="ATASAN[]" class="form-control"></td><td></td><td class="text-right" style="vertical-align: middle;">Tunjangan Lainnya</td><td><input type="text" name="TUNJANGAN_LAINNYA[]" class="form-control"></td></tr><tr><td class="text-right" style="vertical-align: middle;">No. Telepon</td><td><input type="text" name="NO_TELP_PERUSAHAAN[]" class="form-control"></td><td></td><td class="text-right" style="vertical-align: middle;">Alasan Pengunduran Diri</td><td><input type="text" name="ALASAN_RESIGN[]" class="form-control"></td></tr><tr><td class="text-right" style="vertical-align: middle;">Periode Kerja</td><td><input type="text" name="PERIODE_BEKERJA[]" class="form-control"></td><td></td><td class="text-right" style="vertical-align: middle;">Deskripsi Pekerjaan</td><td><input type="text" name="DESKRIPSI_PEKERJAAN[]" class="form-control"></td></tr><tr><td colspan="5" class="text-right"> <span class="input-group-btn" style="display: inline;"> <button type="button" class="btn btn-danger btn-flat del-pengalaman" title="Hapus Data" style="width: 150px;"> <span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span> Hapus Data </button> </span></td></tr></table>');
			$('.datepicker').datepick({
				dateFormat: 'yyyy-mm-dd'
			});
			$('.currency').mask('000,000,000,000,000', {
				reverse: true
			});
			return false;
		});

		$('#add-penanggung').click(function(i) {
			$('.penanggung').append('<tr><td></td><td><input type="text" name="NAMA_PENANGGUNG[]" class="form-control"></td><td><input type="text" name="ALAMAT_PENANGGUNG[]" class="form-control"></td><td><input type="text" name="TELP_PENANGGUNG[]" class="form-control"></td><td><input type="text" name="HUBUNGAN_PENANGGUNG[]" class="form-control"></td><td> <span class="input-group-btn"> <button type="button" class="btn btn-danger btn-flat del-penanggung" title="Hapus Data"> <span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span> </button> </span></td></tr>');
			return false;
		});

		$('.riwayat-status').hide();
		$('#add-status').on('click', function() {
			$('.riwayat-status').toggle();
			$(this).find('span')
				.toggleClass('glyphicon-minus')
				.toggleClass('glyphicon-plus')
		});

		$('.riwayat-level-jabatan').hide();
		$('#add-level-jabatan').on('click', function() {
			$('.riwayat-level-jabatan').toggle();
			$(this).find('span')
				.toggleClass('glyphicon-minus')
				.toggleClass('glyphicon-plus')
		});

		$('.riwayat-posisi').hide();
		$('#add-posisi').on('click', function() {
			$('.riwayat-posisi').toggle();
			$(this).find('span')
				.toggleClass('glyphicon-minus')
				.toggleClass('glyphicon-plus')
		});

		$('.riwayat-gaji').hide();
		$('#add-gaji').on('click', function() {
			$('.riwayat-gaji').toggle();
			$(this).find('span')
				.toggleClass('glyphicon-minus')
				.toggleClass('glyphicon-plus')
		});

	});

	function delete_dokumen() {
		$(document).on('click', '.del-dokumen', function() {
			$(this).closest('tbody').remove();
		});
	}

	function delete_doc_detail() {
		$(document).on('click', '.btn-del-doc', function() {
			$(this).closest('span').remove();
		});
	}

	function delete_organisasi() {
		$(document).on('click', '.del-organisasi', function() {
			$(this).closest('tr').remove();
		});
	}

	function delete_pengalaman() {
		$(document).on('click', '.del-pengalaman', function() {
			$(this).closest('table').remove();
		});
	}

	function delete_penanggung() {
		$(document).on('click', '.del-penanggung', function() {
			$(this).closest('tr').remove();
		});
	}
</script>

<script type="text/javascript">
	function approved_1(pesan = "Are you sure ?", id = null, tipe = '') {
		if (confirm(pesan)) {
			var btn = "";
			if (tipe == 'organisasi') btn = "#btn_organisasi_" + id;
			else if (tipe == 'keluarga') btn = "#btn_keluarga_" + id;
			else if (tipe == 'kerja') btn = "#btn_kerja_" + id;
			//pendidikan
			else if (tipe == 'pendidikan_formal') btn = "#btn_pendidikan_formal_" + id;
			else if (tipe == 'pendidikan_kursus') btn = "#btn_pendidikan_kursus_" + id;
			else if (tipe == 'pendidikan_bahasa') btn = "#btn_pendidikan_bahasa_" + id;
			//DOKUMEN

			else if (tipe == 'dok_ijazah') btn = "#btn_dok_ijazah_" + id;
			else if (tipe == 'dok_sertifikat') btn = "#btn_dok_sertifikat_" + id;
			else if (tipe == 'dok_sio') btn = "#btn_dok_sio_" + id;
			else if (tipe == 'dok_kta') btn = "#btn_dok_kta_" + id;

			$.ajax({
				type: "POST",
				data: {
					id: id,
					tipe: tipe
				},
				url: "karyawan-approve-1.php",
				dataType: 'html',
				success: function(data) {

					if (data == "sukses") alert("Data Berhasil di setujui");
					//if(data == "sukses")alert(btn);
					$(btn).hide();
					//location.reload();
				}
			});

		}
	}
</script>

<?php
include 'footer.php';
?>