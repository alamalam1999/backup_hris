<?php

include 'app-load.php';

is_login('penggajian.view');

$MODULE = 'PENGGAJIAN';

if (isset($_GET['generate'])) {
	is_login('penggajian.generate');

	$PERIODE_ID = get_input('PERIODE_ID');
	$PROJECT_ID = get_input('PROJECT_ID');

	$PERIODE = db_first(" SELECT * FROM periode WHERE PERIODE_ID='$PERIODE_ID' ");

	if (!isset($PERIODE->PERIODE_ID)) {
		header('location: penggajian.php');
		exit;
	}
	if ($PERIODE->STATUS_PERIODE == 'CLOSED') {
		header('location: penggajian.php?m=closed');
		exit;
	}

	$TAHUN = $PERIODE->TAHUN;
	$BULAN = $PERIODE->BULAN;
	$TGL_MULAI = $PERIODE->TANGGAL_MULAI;
	$TGL_SELESAI = $PERIODE->TANGGAL_SELESAI;
	$TGL_MULAI2 = $PERIODE->TANGGAL_MULAI;
	$TGL_SELESAI2 = $PERIODE->TANGGAL_SELESAI;
	$THR_IDUL_FITRI = $PERIODE->THR_IDUL_FITRI;
	$TGL_IDUL_FITRI = $PERIODE->TGL_IDUL_FITRI;
	$THR_KUNINGAN = $PERIODE->THR_KUNINGAN;
	$TGL_KUNINGAN = $PERIODE->TGL_KUNINGAN;

	$PROJECT = db_first(" SELECT CUTOFF FROM project WHERE PROJECT_ID='$PROJECT_ID' ");
	$CUTOFF = isset($PROJECT->CUTOFF) ? $PROJECT->CUTOFF : 0;

	if ($CUTOFF == '1') {
		$TGL_MULAI2 = $PERIODE->TANGGAL_MULAI2;
		$TGL_SELESAI2 = $PERIODE->TANGGAL_SELESAI2;
	}

	db_execute(" DELETE FROM penggajian WHERE PERIODE_ID='$PERIODE_ID' AND PROJECT_ID='$PROJECT_ID' ");
	db_execute(" DELETE FROM bpjs WHERE PERIODE_ID='$PERIODE_ID' AND PROJECT_ID='$PROJECT_ID' ");

	$karyawan = db_fetch("
		SELECT K.*, J.*, K.R_BPJS_JHT AS RULES_BPJS_JHT, K.R_BPJS_JP AS RULES_BPJS_JP, K.R_BPJS_JKK AS RULES_BPJS_JKK, K.R_BPJS_JKM AS RULES_BPJS_JKM, K.R_BPJS_KES AS RULES_BPJS_KES
		FROM karyawan K
		LEFT JOIN jabatan J ON J.JABATAN_ID=K.JABATAN_ID
		WHERE J.PROJECT_ID='$PROJECT_ID' AND ST_KERJA='AKTIF'
	");
	// echo "<pre>";
	// print_r($karyawan); die();
	if (count($karyawan) > 0) {
		foreach ($karyawan as $k) {
			$rs = db_fetch("
				SELECT *, K.SHIFT_CODE
				FROM shift_karyawan K
					LEFT JOIN shift S ON S.SHIFT_CODE = K.SHIFT_CODE
				WHERE
					KARYAWAN_ID ='$k->KARYAWAN_ID' AND
					(DATE >= '$TGL_MULAI' AND DATE <= '$TGL_SELESAI')
			");

			$SHIFT = array();
			if (count($rs) > 0) {
				foreach ($rs as $row) {
					$SHIFT[$k->KARYAWAN_ID][$row->DATE] = $row;
				}
			}

			$SCAN = parse_scan($k->KARYAWAN_ID, $TGL_MULAI, $TGL_SELESAI, $SHIFT);
			$SCAN_PAYROLL = $SCAN;

			if ($CUTOFF == '1') {
				$rs = db_fetch("
					SELECT *, K.SHIFT_CODE
					FROM shift_karyawan K
						LEFT JOIN shift S ON S.SHIFT_CODE = K.SHIFT_CODE
					WHERE
						KARYAWAN_ID='$k->KARYAWAN_ID' AND
						(DATE >= '$TGL_MULAI2' AND DATE <= '$TGL_SELESAI2')
				");
				$SHIFT2 = array();
				if (count($rs) > 0) {
					foreach ($rs as $row) {
						$SHIFT2[$k->KARYAWAN_ID][$row->DATE] = $row;
					}
				}
				$SCAN_PAYROLL = parse_scan($k->KARYAWAN_ID, $TGL_MULAI2, $TGL_SELESAI2, $SHIFT2);
			}

			$GAJI_POKOK = $GAJI_POKOK_PRORATA = $TIDAK_MASUK = $GAJI_POKOK_NET = 0;

			//if ($k->R_GAJI_POKOK == '1') {
			$GAJI_POKOK_KOTOR =  $k->GAJI_POKOK;

			$TOTAL_DAY = isset($SCAN_PAYROLL['total_day']) ? $SCAN_PAYROLL['total_day'] : 0;
			$TOTAL_RESIGN = isset($SCAN_PAYROLL['total_r']) ? $SCAN_PAYROLL['total_r'] : 0;
			$TOTAL_BELUM_MASUK = isset($SCAN_PAYROLL['total_bm']) ? $SCAN_PAYROLL['total_bm'] : 0;
			$TOTAL_CM = isset($SCAN_PAYROLL['total_cm']) ? $SCAN_PAYROLL['total_cm'] : 0;
			$TOTAL_UL = isset($SCAN_PAYROLL['total_ul']) ? $SCAN_PAYROLL['total_ul'] : 0;
			$PRORATA = $TOTAL_RESIGN + $TOTAL_BELUM_MASUK + $TOTAL_CM + $TOTAL_UL;
			if ($PRORATA > 0) {
				$PEMOTONG = round(($PRORATA / $TOTAL_DAY) * $k->GAJI_POKOK, 0);
				$GAJI_POKOK_PRORATA = $k->GAJI_POKOK - $PEMOTONG;
			}
			//}

			$JOIN_DATE = $k->TGL_MASUK;
			$CURRENT_DATE = $TGL_SELESAI;
			$JOIN_TIME = strtotime($JOIN_DATE);
			$CURRENT_TIME = strtotime($CURRENT_DATE);
			$CURR_MONTH = $TAHUN . '-' . $BULAN . '-' . date('d', $JOIN_TIME);
			$JOIN_MONTH = $TAHUN . '-' . date('m', $JOIN_TIME) . '-' . date('d', $JOIN_TIME);
			if ($CURR_MONTH >= $JOIN_MONTH) {
				$VALID_UNTIL = date('Y-m-d', strtotime($JOIN_MONTH . " + 1 year -1 month "));
			}
			if ($CURR_MONTH < $JOIN_MONTH) {
				$VALID_UNTIL = date('Y-m-d', strtotime($JOIN_MONTH . " -1 month "));
			}

			$KUOTA_CUTI = 0;
			$JOIN_DAY = round((strtotime($CURR_MONTH) - strtotime($JOIN_DATE)) / 86400, 0);
			if ($JOIN_DAY >= 365 or $k->R_CUTI == '0') {
				$KUOTA_CUTI = 12;
			} else {
				$CUTI_BERJALAN = cuti_berjalan($JOIN_DATE);
				$KUOTA_CUTI = isset($CUTI_BERJALAN[$CURR_MONTH]) ? $CUTI_BERJALAN[$CURR_MONTH] : 0;
			}

			/*
			if($k->KARYAWAN_ID=='24')
			{
				echo '<pre>';
				print_r($CUTI_BERJALAN);
				echo '</pre>';
				echo 'Join : '.$JOIN_DATE.'<br>';
				echo 'Current : '.$CURR_MONTH.'<br>';
				echo 'Valid : '.$VALID_UNTIL.'<br>';
				echo 'Quota : '.$KUOTA_CUTI.'<br>';
				die;
			}
			*/

			$TOTAL_CT = isset($SCAN_PAYROLL['total_ct']) ? $SCAN_PAYROLL['total_ct'] : 0;
			$TOTAL_ABS = isset($SCAN_PAYROLL['total_absent']) ? $SCAN_PAYROLL['total_absent'] : 0;
			$TOTAL_SAKIT = isset($SCAN_PAYROLL['total_sakit']) ? $SCAN_PAYROLL['total_sakit'] : 0;
			$TOTAL_IJIN = isset($SCAN_PAYROLL['total_ijin']) ? $SCAN_PAYROLL['total_ijin'] : 0;
			$TOTAL_NSKD = $TOTAL_SAKIT + $TOTAL_IJIN;
			$CUTI_TERPAKAI = 0;

			db_execute(" DELETE FROM cuti_tahunan WHERE KARYAWAN_ID='$k->KARYAWAN_ID' AND PERIODE_ID='$PERIODE_ID' ");
			$row = db_first("
				SELECT SUM(CUTI) as TOTAL_CUTI
				FROM cuti_tahunan
				WHERE
					KARYAWAN_ID='$k->KARYAWAN_ID' AND
					VALID_UNTIL='$VALID_UNTIL' AND
					PERIODE_ID < $PERIODE_ID AND
					PERIODE_ID <> '$PERIODE_ID'
				GROUP BY KARYAWAN_ID
			");
			$CUTI_PERIODE_SEBELUMNYA = isset($row->TOTAL_CUTI) ? $row->TOTAL_CUTI : 0;
			$SISA_CUTI = $SISA_CUTI_ORIGINAL = $KUOTA_CUTI - $CUTI_PERIODE_SEBELUMNYA;

			/*
			if( $TOTAL_CT > 0 )
			{
				$CUTI_TERPAKAI = $TOTAL_CT;
				$SISA_CUTI = $SISA_CUTI - $CUTI_TERPAKAI;
				if($SISA_CUTI < 0) $SISA_CUTI = 0;
			}
			*/

			#if($k->KARYAWAN_ID=='28')
			#{
			#	echo 'SISA : '.$SISA_CUTI.'<br>';
			#	echo 'ABS : '.$TOTAL_ABS.'<br>';
			#}

			if ($k->R_POT_ABSEN_CUTI == '1') {
				if ($SISA_CUTI >= $TOTAL_ABS) {
					$CUTI_TERPAKAI = $CUTI_TERPAKAI + $TOTAL_ABS;
					$SISA_CUTI = $SISA_CUTI - $TOTAL_ABS;
					$TOTAL_ABS = 0;
				} else if (($SISA_CUTI > 0) and ($SISA_CUTI < $TOTAL_ABS)) {
					$CUTI_TERPAKAI = $CUTI_TERPAKAI + $SISA_CUTI;
					$SISA_CUTI_TMP = $SISA_CUTI;
					$SISA_CUTI = $SISA_CUTI - $TOTAL_ABS;
					$TOTAL_ABS = $TOTAL_ABS - $SISA_CUTI_TMP;
				}
			}

			#if($k->KARYAWAN_ID=='28')
			#{
			#	echo 'ABS : '.$TOTAL_ABS.'<br>';
			#	echo 'SISA : '.$SISA_CUTI.'<br>';
			#	echo 'NSKD : '.$TOTAL_NSKD.'<br>';
			#}

			if ($k->R_POT_NSKD_CUTI == '1') {
				if ($SISA_CUTI >= $TOTAL_NSKD) {
					$CUTI_TERPAKAI = $CUTI_TERPAKAI + $TOTAL_NSKD;
					$SISA_CUTI = $SISA_CUTI - $TOTAL_NSKD;
					$TOTAL_NSKD = 0;
				} else if (($SISA_CUTI > 0) and ($SISA_CUTI < $TOTAL_NSKD)) {
					$CUTI_TERPAKAI = $CUTI_TERPAKAI + $SISA_CUTI;
					$SISA_CUTI_TMP = $SISA_CUTI;
					$SISA_CUTI = $SISA_CUTI - $TOTAL_NSKD;
					$TOTAL_NSKD = $TOTAL_NSKD - $SISA_CUTI_TMP;
					if ($SISA_CUTI < 0) $SISA_CUTI = 0;
				}
			}

			#if($k->KARYAWAN_ID=='28')
			#{
			#	echo 'SISA : '.$SISA_CUTI.'<br>';
			#	echo 'NSKD : '.$TOTAL_NSKD.'<br>';
			#}

			#if($k->KARYAWAN_ID=='28'){
			#	echo 'SISA : '.$SISA_CUTI;
			#	die;
			#}

			$CUTI_TERPAKAI = $CUTI_TERPAKAI + $TOTAL_CT;
			$SALDO_CUTI = $KUOTA_CUTI - ($CUTI_PERIODE_SEBELUMNYA + $CUTI_TERPAKAI);
			if ($CUTI_TERPAKAI > 0) {
				db_execute(" INSERT INTO cuti_tahunan (KARYAWAN_ID,PERIODE_ID,VALID_UNTIL,CUTI) VALUES ('$k->KARYAWAN_ID','$PERIODE_ID','$VALID_UNTIL','$CUTI_TERPAKAI') ");
			}

			if ($k->R_POT_ABSEN_GP == '1') {
				$TIDAK_MASUK = $TIDAK_MASUK + ($k->GAJI_POKOK * (1 / 25)) * $TOTAL_ABS;
			}
			if ($k->R_POT_NSKD_GP == '1') {
				$TIDAK_MASUK = $TIDAK_MASUK + ($k->GAJI_POKOK * (1 / 25)) * $TOTAL_NSKD;
			}

			$GAJI_POKOK_NET = $GAJI_POKOK_PRORATA - $TIDAK_MASUK;

			$TUNJ_JABATAN = 0;
			if ($k->R_TUNJ_JABATAN == '1') {
				$TUNJ_JABATAN = $k->TUNJ_JABATAN;
				/*if($k->R_POT_ABSEN_GP=='1')
				{
					$TUNJ_JABATAN = $TUNJ_JABATAN - (($TUNJ_JABATAN * (1/25)) * $TOTAL_ABS);
				}
				if($k->R_POT_NSKD_GP=='1')
				{
					$TUNJ_JABATAN = $TUNJ_JABATAN - (($TUNJ_JABATAN * (1/25)) * $TOTAL_NSKD);
				}*/
			}

			$TUNJ_KEAHLIAN = 0;
			if ($k->R_TUNJ_KEAHLIAN == '1') {
				$TUNJ_KEAHLIAN = $k->TUNJ_KEAHLIAN;
				if ($k->R_POT_ABSEN_GP == '1') {
					$TUNJ_KEAHLIAN = $TUNJ_KEAHLIAN - (($TUNJ_KEAHLIAN * (1 / 25)) * $TOTAL_ABS);
				}
				if ($k->R_POT_NSKD_GP == '1') {
					$TUNJ_KEAHLIAN = $TUNJ_KEAHLIAN - (($TUNJ_KEAHLIAN * (1 / 25)) * $TOTAL_NSKD);
				}
			}

			$TUNJ_KOMUNIKASI = 0;
			if ($k->R_TUNJ_KOMUNIKASI == '1') {
				$TUNJ_KOMUNIKASI = $k->TUNJ_KOMUNIKASI;
				if ($k->R_POT_ABSEN_GP == '1') {
					$TUNJ_KOMUNIKASI = $TUNJ_KOMUNIKASI - (($TUNJ_KOMUNIKASI * (1 / 25)) * $TOTAL_ABS);
				}
				if ($k->R_POT_NSKD_GP == '1') {
					$TUNJ_KOMUNIKASI = $TUNJ_KOMUNIKASI - (($TUNJ_KOMUNIKASI * (1 / 25)) * $TOTAL_NSKD);
				}
			}

			$TUNJ_PROYEK = 0;
			if ($k->R_TUNJ_PROYEK == '1') {
				$TUNJ_PROYEK = $k->TUNJ_PROYEK;
				if ($k->R_POT_ABSEN_GP == '1') {
					$TUNJ_PROYEK = $TUNJ_PROYEK - (($TUNJ_PROYEK * (1 / 25)) * $TOTAL_ABS);
				}
				if ($k->R_POT_NSKD_GP == '1') {
					$TUNJ_PROYEK = $TUNJ_PROYEK - (($TUNJ_PROYEK * (1 / 25)) * $TOTAL_NSKD);
				}
			}

			$TUNJ_SHIFT = 0;
			if ($k->R_TUNJ_SHIFT == '1') {
				$TUNJ_SHIFT = $k->TUNJ_SHIFT;
				if ($k->R_POT_ABSEN_GP == '1') {
					$TUNJ_SHIFT = $TUNJ_SHIFT - (($TUNJ_SHIFT * (1 / 25)) * $TOTAL_ABS);
				}
				if ($k->R_POT_NSKD_GP == '1') {
					$TUNJ_SHIFT = $TUNJ_SHIFT - (($TUNJ_SHIFT * (1 / 25)) * $TOTAL_NSKD);
				}
			}

			$TUNJ_BACKUP = 0;
			if ($k->R_TUNJ_BACKUP == '1') {
				$TOTAL_BACKUP = isset($SCAN['total_backup']) ? $SCAN['total_backup'] : 0;
				$TUNJ_BACKUP = $k->TUNJ_BACKUP * $TOTAL_BACKUP;
			}

			$TUNJ_MAKAN = 0;
			if ($k->R_TUNJ_MAKAN == '1') {
				#$TOTAL_ATT = isset($SCAN['total_attendance']) ? $SCAN['total_attendance'] : 0;
				$TOTAL_ATT = 22;
				$TUNJ_MAKAN = $k->TUNJ_MAKAN * $TOTAL_ATT;
				if ($k->R_POT_ABSEN_TUNJ_MAKAN == '1') {
					$TOTAL_ABSENT = isset($SCAN['total_absent']) ? $SCAN['total_absent'] : 0;
					$TUNJ_MAKAN = $TUNJ_MAKAN - ($k->TUNJ_MAKAN * $TOTAL_ABSENT);
				} else {
					#$TOTAL_ABSENT = isset($SCAN['total_absent']) ? $SCAN['total_absent'] : 0;
					#$TUNJ_MAKAN = $TUNJ_MAKAN + ($k->TUNJ_MAKAN * $TOTAL_ABSENT);
				}

				if ($k->R_POT_NSKD_TUNJ_MAKAN == '1') {
					$TOTAL_SAKIT = isset($SCAN['total_sakit']) ? $SCAN['total_sakit'] : 0;
					$TOTAL_IJIN = isset($SCAN['total_ijin']) ? $SCAN['total_ijin'] : 0;
					$TUNJ_MAKAN = $TUNJ_MAKAN - ($k->TUNJ_MAKAN * ($TOTAL_SAKIT + $TOTAL_IJIN));
				} else {
					#$TOTAL_SAKIT = isset($SCAN['total_sakit']) ? $SCAN['total_sakit'] : 0;
					#$TOTAL_IJIN = isset($SCAN['total_ijin']) ? $SCAN['total_ijin'] : 0;
					#$TUNJ_MAKAN = $TUNJ_MAKAN + ($k->TUNJ_MAKAN * ($TOTAL_SAKIT + $TOTAL_IJIN));
				}

				if ($k->R_POT_LATE_TUNJ_MAKAN == '1') {
					$TOTAL_LATE = isset($SCAN['total_late']) ? $SCAN['total_late'] : 0;
					$TUNJ_MAKAN = $TUNJ_MAKAN - ($k->TUNJ_MAKAN * $TOTAL_LATE);
					if ($TUNJ_MAKAN < 0) $TUNJ_MAKAN = 0;
				}

				#$TOTAL_SKD = isset($SCAN['total_skd']) ? $SCAN['total_skd'] : 0;
				#$TUNJ_MAKAN = $TUNJ_MAKAN + ($k->TUNJ_MAKAN * $TOTAL_SKD);
			}

			$TUNJ_TRANSPORT = 0;
			if ($k->R_TUNJ_TRANSPORT == '1') {
				#$TOTAL_ATT = isset($SCAN['total_attendance']) ? $SCAN['total_attendance'] : 0;
				$TOTAL_ATT = 22;
				$TUNJ_TRANSPORT = $k->TUNJ_TRANSPORT * $TOTAL_ATT;
				if ($k->R_POT_ABSEN_TUNJ_TRANSPORT == '1') {
					$TOTAL_ABSENT = isset($SCAN['total_absent']) ? $SCAN['total_absent'] : 0;
					$TUNJ_TRANSPORT = $TUNJ_TRANSPORT - ($k->TUNJ_TRANSPORT * $TOTAL_ABSENT);
				} else {
					#$TOTAL_ABSENT = isset($SCAN['total_absent']) ? $SCAN['total_absent'] : 0;
					#$TUNJ_TRANSPORT = $TUNJ_TRANSPORT + ($k->TUNJ_TRANSPORT * $TOTAL_ABSENT);
				}

				if ($k->R_POT_NSKD_TUNJ_TRANSPORT == '1') {
					$TOTAL_SAKIT = isset($SCAN['total_sakit']) ? $SCAN['total_sakit'] : 0;
					$TOTAL_IJIN = isset($SCAN['total_ijin']) ? $SCAN['total_ijin'] : 0;
					$TUNJ_TRANSPORT = $TUNJ_TRANSPORT - ($k->TUNJ_TRANSPORT * ($TOTAL_SAKIT + $TOTAL_IJIN));
				} else {
					#$TOTAL_SAKIT = isset($SCAN['total_sakit']) ? $SCAN['total_sakit'] : 0;
					#$TOTAL_IJIN = isset($SCAN['total_ijin']) ? $SCAN['total_ijin'] : 0;
					#$TUNJ_TRANSPORT = $TUNJ_TRANSPORT + ($k->TUNJ_TRANSPORT * ($TOTAL_SAKIT + $TOTAL_IJIN));
				}

				#$TOTAL_SKD = isset($SCAN['total_skd']) ? $SCAN['total_skd'] : 0;
				#$TUNJ_TRANSPORT = $TUNJ_TRANSPORT + ($k->TUNJ_TRANSPORT * $TOTAL_SKD);
			}

			$TUNJ_KEHADIRAN = 0;
			if ($k->R_TUNJ_KEHADIRAN == '1') {
				$TUNJ_KEHADIRAN = round((2 / 25) * $k->GAJI_POKOK, 0);

				if ($k->R_POT_ABSEN_TUNJ_KEHADIRAN == '1') {
					$TOTAL_ABSENT = isset($SCAN['total_absent']) ? $SCAN['total_absent'] : 0;
					if ($TOTAL_ABSENT > 0) $TUNJ_KEHADIRAN = 0;
				}

				if ($k->R_POT_NSKD_TUNJ_KEHADIRAN == '1') {
					$TOTAL_SAKIT = isset($SCAN['total_sakit']) ? $SCAN['total_sakit'] : 0;
					$TOTAL_IJIN = isset($SCAN['total_ijin']) ? $SCAN['total_ijin'] : 0;
					if (($TOTAL_SAKIT + $TOTAL_IJIN) > 0) $TUNJ_KEHADIRAN = 0;
				}

				if ($k->R_POT_SKD_TUNJ_KEHADIRAN == '1') {
					$TOTAL_SKD = isset($SCAN['total_skd']) ? $SCAN['total_skd'] : 0;
					if ($TOTAL_SKD > 0) $TUNJ_KEHADIRAN = 0;
				}

				if ($k->R_POT_LATE_TUNJ_KEHADIRAN == '1') {
					$TOTAL_LATE = isset($SCAN['total_late']) ? $SCAN['total_late'] : 0;
					if ($TOTAL_LATE > 0) $TUNJ_KEHADIRAN = 0;
				}

				if ($k->R_POT_EARLY_TUNJ_KEHADIRAN == '1') {
					$TOTAL_EARLY = isset($SCAN['total_early']) ? $SCAN['total_early'] : 0;
					if ($TOTAL_EARLY > 0) $TUNJ_KEHADIRAN = 0;
				}
			}

			$LHK = $LHL = $IHB = 0;
			if ($k->R_LEMBUR == '1') {
				$LEMBUR_LHK = db_first(" SELECT SUM(UANG_LEMBUR) AS UANG_LEMBUR FROM lembur WHERE KARYAWAN_ID='$k->KARYAWAN_ID' AND PERIODE_ID='$PERIODE_ID' AND STATUS='APPROVED' AND JENIS='LHK' ")->UANG_LEMBUR;

				$LEMBUR_LHL = db_first(" SELECT SUM(UANG_LEMBUR) AS UANG_LEMBUR FROM lembur WHERE KARYAWAN_ID='$k->KARYAWAN_ID' AND PERIODE_ID='$PERIODE_ID' AND STATUS='APPROVED' AND JENIS='LHL' ")->UANG_LEMBUR;

				$LHK = $LEMBUR_LHK;
				$LHL = $LEMBUR_LHL;

				//print_r($LEMBUR_LHK); die();


				// $lembur = db_fetch(" SELECT * FROM lembur WHERE KARYAWAN_ID='$k->KARYAWAN_ID' AND PERIODE_ID='$PERIODE_ID' AND STATUS='APPROVED' ");
				// if (count($lembur) > 0) {
				// 	foreach ($lembur as $row) {
				// 		$JENIS_LEMBUR = isset($row->JENIS) ? $row->JENIS : '';
				// 		$TOTAL_JAM = isset($row->TOTAL_JAM) ? $row->TOTAL_JAM : '';
				// 		if ($JENIS_LEMBUR == 'LHK') {
				// 			$LEMBUR = hitung_lembur_LHK($row->TOTAL_JAM, $k->GAJI_POKOK, $row->ADJUSMENT);


				// 			//$LHK = $LHK + $LEMBUR['TOTAL'];

				// 			//NEW FUNCTION
				// 			$LEMBUR_LHK = db_first(" SELECT SUM(UANG_LEMBUR) AS UANG_LEMBUR FROM lembur WHERE KARYAWAN_ID='$k->KARYAWAN_ID' AND PERIODE_ID='$PERIODE_ID' AND STATUS='APPROVED' AND JENIS='LHK' ")->UANG_LEMBUR;
				// 			$LHK = $LEMBUR_LHK;
				// 		}
				// 		if ($JENIS_LEMBUR == 'LHL') {
				// 			$LEMBUR = hitung_lembur_LHL($row->TOTAL_JAM, $k->GAJI_POKOK, $row->ADJUSMENT);
				// 			//$LHL = $LHL + $LEMBUR['TOTAL'];

				// 			//NEW FUNCTION
				// 			$LEMBUR_LHL = db_first(" SELECT SUM(UANG_LEMBUR) AS UANG_LEMBUR FROM lembur WHERE KARYAWAN_ID='$k->KARYAWAN_ID' AND PERIODE_ID='$PERIODE_ID' AND STATUS='APPROVED' AND JENIS='LHL' ")->UANG_LEMBUR;
				// 			$LHK = $LEMBUR_LHL;
				// 		}
				// 		if ($JENIS_LEMBUR == 'IHB') {
				// 			$LEMBUR = hitung_lembur_IHB($row->TOTAL_JAM, $k->GAJI_POKOK, $row->ADJUSMENT);
				// 			$IHB = $IHB + $LEMBUR['TOTAL'];
				// 		}
				// 	}
				// }
			}

			$MEDICAL = 0;
			if ($k->R_MEDICAL_CASH == '1') {
				$med = db_first(" SELECT SUM(TOTAL) as TOTAL FROM medical WHERE KARYAWAN_ID='$k->KARYAWAN_ID' AND PERIODE_ID='$PERIODE_ID' AND STATUS='APPROVED' ");
				$MEDICAL = isset($med->TOTAL) ? $med->TOTAL : 0;
			}

			$adj_plus = db_first(" SELECT SUM(TOTAL) as TOTAL FROM adjusment WHERE TIPE_ADJUSMENT='PLUS' AND KARYAWAN_ID='$k->KARYAWAN_ID' AND PERIODE_ID='$PERIODE_ID' AND STATUS='APPROVED' ");
			$ADJUSMENT_PLUS = isset($adj_plus->TOTAL) ? $adj_plus->TOTAL : 0;

			$adj_minus = db_first(" SELECT SUM(TOTAL) as TOTAL FROM adjusment WHERE TIPE_ADJUSMENT='MINUS' AND KARYAWAN_ID='$k->KARYAWAN_ID' AND PERIODE_ID='$PERIODE_ID' AND STATUS='APPROVED' ");
			$ADJUSMENT_MINUS = isset($adj_minus->TOTAL) ? $adj_minus->TOTAL : 0;

			#$potongan = db_first(" SELECT SUM(TOTAL_POTONGAN) as TOTAL_POTONGAN FROM potongan WHERE KARYAWAN_ID='$k->KARYAWAN_ID' AND PERIODE_ID='$PERIODE_ID' AND STATUS='APPROVED' ");
			#$POTONGAN = isset($potongan->TOTAL_POTONGAN) ? $potongan->TOTAL_POTONGAN : 0;

			$angsuran = db_first(" SELECT SUM(TOTAL) as TOTAL_ANGSURAN FROM angsuran WHERE KARYAWAN_ID='$k->KARYAWAN_ID' AND PERIODE_ID='$PERIODE_ID' AND STATUS='APPROVED' ");
			$ANGSURAN = isset($angsuran->TOTAL_ANGSURAN) ? $angsuran->TOTAL_ANGSURAN : 0;

			$GAJI_POKOK_PRORATA_NET = 0;
			$TUNJ_KELUARGA = $k->TUNJ_KELUARGA;
			if ($GAJI_POKOK_PRORATA > 0) $GAJI_POKOK_NET = $GAJI_POKOK_PRORATA + $TUNJ_KELUARGA;
			if ($GAJI_POKOK_PRORATA > 0) $GAJI_POKOK_PRORATA_NET = $GAJI_POKOK_PRORATA + $TUNJ_KELUARGA;
			if ($GAJI_POKOK_PRORATA == 0) $GAJI_POKOK_NET = $GAJI_POKOK_KOTOR + $TUNJ_KELUARGA;

			$CALC_K_JHT = db_first(" SELECT val FROM options WHERE var='K_JHT' ")->val;
			
			
			
			
			$BPJS_JHT = 0;
			$BPJS_JHT_PERUSAHAAN = 0;
			if($k->RULES_BPJS_JHT == 1){
				if ($CALC_K_JHT != '') {
					$CALC_K_JHT = (int)$CALC_K_JHT;
				} else {
					$CALC_K_JHT = 0;
				}
				$BPJS_JHT = ($GAJI_POKOK_NET * $CALC_K_JHT) / 100;

				$CALC_P_JHT = db_first(" SELECT val FROM options WHERE var='P_JHT' ")->val;
				if ($CALC_P_JHT != '') {
					$CALC_P_JHT = (int)$CALC_P_JHT;
				} else {
					$CALC_P_JHT = 0;
				}
				$BPJS_JHT_PERUSAHAAN = ($GAJI_POKOK_NET * $CALC_P_JHT) / 100;
			}

			
			// if ($k->R_BPJS_JHT == '1') {
			// 	$BPJS_JHT = isset($k->BPJS_JHT) ? $k->BPJS_JHT : 0;
			// 	$BPJS_JHT_PERUSAHAAN = isset($k->BPJS_JHT_PERUSAHAAN) ? $k->BPJS_JHT_PERUSAHAAN : 0;
			// }
			
			$BPJS_JKK = 0;
			$BPJS_JKK_PERUSAHAAN = 0;
			if($k->RULES_BPJS_JKK == 1){
				$CALC_K_JKK = db_first(" SELECT val FROM options WHERE var='K_JKK' ")->val;
				if ($CALC_K_JKK != '') {
					$CALC_K_JKK = (int)$CALC_K_JKK;
				} else {
					$CALC_K_JKK = 0;
				}
				$BPJS_JKK = ($GAJI_POKOK_NET * $CALC_K_JKK) / 100;

				$CALC_P_JKK = db_first(" SELECT val FROM options WHERE var='P_JKK' ")->val;
				if ($CALC_P_JKK != '') {
					$CALC_P_JKK = (int)$CALC_P_JKK;
				} else {
					$CALC_P_JKK = 0;
				}
				$BPJS_JKK_PERUSAHAAN = ($GAJI_POKOK_NET * $CALC_P_JKK) / 100;
			}
			
			
			

			$BPJS_JKM = 0;
			$BPJS_JKM_PERUSAHAAN = 0;
			if($k->RULES_BPJS_JKM == 1){

				$CALC_K_JKM = db_first(" SELECT val FROM options WHERE var='K_JKM' ")->val;

				if ($CALC_K_JKM != '') {
					$CALC_K_JKM = (int)$CALC_K_JKM;
				} else {
					$CALC_K_JKM = 0;
				}
				$BPJS_JKM = ($GAJI_POKOK_NET * $CALC_K_JKM) / 100;

				$CALC_P_JKM = db_first(" SELECT val FROM options WHERE var='P_JKM' ")->val;
				if ($CALC_P_JKM != '') {
					$CALC_P_JKM = (int)$CALC_P_JKM;
				} else {
					$CALC_P_JKM = 0;
				}
				$BPJS_JKM_PERUSAHAAN = ($GAJI_POKOK_NET * $CALC_P_JKM) / 100;
			}
			
			// $BPJS_JKK = isset($k->BPJS_JKK) ? $k->BPJS_JKK : 0;
			// $BPJS_JKM = isset($k->BPJS_JKM) ? $k->BPJS_JKM : 0;
			// $BPJS_JP = 0;
			// $BPJS_JP_PERUSAHAAN = 0;
			// if ($k->R_BPJS_JP == '1') {
			// 	$BPJS_JP = isset($k->BPJS_JP) ? $k->BPJS_JP : 0;
			// 	$BPJS_JP_PERUSAHAAN = isset($k->BPJS_JP_PERUSAHAAN) ? $k->BPJS_JP_PERUSAHAAN : 0;
			// }

			$BPJS_KES = 0;
			$BPJS_KES_PERUSAHAAN = 0;

			if($k->RULES_BPJS_KES == 1){
				$CALC_K_BPJS = db_first(" SELECT val FROM options WHERE var='K_BPJS' ")->val;
				if ($CALC_K_BPJS != '') {
					$CALC_K_BPJS = (int)$CALC_K_BPJS;
				} else {
					$CALC_K_BPJS = 0;
				}
				$BPJS_KES = ($GAJI_POKOK_NET * $CALC_K_BPJS) / 100;
				$CALC_P_KES = db_first(" SELECT val FROM options WHERE var='P_KES' ")->val;
				if ($CALC_P_KES != '') {
					$CALC_P_KES = (int)$CALC_P_KES;
				} else {
					$CALC_P_KES = 0;
				}
				$BPJS_KES_PERUSAHAAN = ($GAJI_POKOK_NET * $CALC_P_KES) / 100;
			}
			

			$BPJS_JP = 0;
			$BPJS_JP_PERUSAHAAN = 0;

			if($k->RULES_BPJS_JP == 1){

				$CALC_K_JP = db_first(" SELECT val FROM options WHERE var='K_JP' ")->val;
				if ($CALC_K_JP != '') {
					$CALC_K_JP = (int)$CALC_K_JP;
				} else {
					$CALC_K_JP = 0;
				}
				$BPJS_JP = ($GAJI_POKOK_NET * $CALC_K_JP) / 100;
				
				$CALC_P_JP = db_first(" SELECT val FROM options WHERE var='P_JP' ")->val;
				if ($CALC_P_JP != '') {
					$CALC_P_JP = (int)$CALC_P_JP;
				} else {
					$CALC_P_JP = 0;
				}
				$BPJS_JP_PERUSAHAAN = ($GAJI_POKOK_NET * $CALC_P_JP) / 100;
			}

			// if ($k->R_BPJS_KES == '1') {
			// 	$BPJS_KES = isset($k->BPJS_KES) ? $k->BPJS_KES : 0;
			// 	$BPJS_KES_PERUSAHAAN = isset($k->BPJS_KES_PERUSAHAAN) ? $k->BPJS_KES_PERUSAHAAN : 0;
			// }

			$TOTAL_BPJS_KARYAWAN = $BPJS_JHT+$BPJS_JP+$BPJS_JKK+$BPJS_JKM+$BPJS_KES;
			$TOTAL_BPJS_PERUSAHAAN = $BPJS_JHT_PERUSAHAAN+$BPJS_JP_PERUSAHAAN+$BPJS_JKK_PERUSAHAAN+$BPJS_JKM_PERUSAHAAN+$BPJS_KES_PERUSAHAAN;
			//PINJAMAN
			$pinjaman = db_first(" SELECT SUM(TOTAL) as TOTAL_PINJAMAN FROM pinjaman_angsuran WHERE KARYAWAN_ID='$k->KARYAWAN_ID' AND PERIODE_ID='$PERIODE_ID' AND STATUS_PINJAMAN='APPROVED' ");
			$pinjaman_detail = db_fetch(" SELECT * FROM pinjaman_angsuran WHERE KARYAWAN_ID='$k->KARYAWAN_ID' AND PERIODE_ID='$PERIODE_ID' AND STATUS_PINJAMAN='APPROVED' ");
			$PINJAMAN_KOPERASI_DINATERA = 0;
			$IURAN_KOPERASI_DINATERA = 0;
			$IURAN_KOPERASI_AVICENNA = 0;
			$PINJAMAN_BANK_BWS = 0;
			$EKSES_KLAIM = 0;
			$BIAYA_PEND_ANAK = 0;
			$BIAYA_LAPTOP = 0;
			$PINJAMAN_KOPERASI_AVICENNA = 0;
			foreach ($pinjaman_detail as $row_pinjaman) {
				if ($row_pinjaman->JENIS_POTONGAN == 'PINJAMAN KOPERASI DINATERA') $PINJAMAN_KOPERASI_DINATERA = $row_pinjaman->TOTAL;
				if ($row_pinjaman->JENIS_POTONGAN == 'IURAN KOPERASI DINATERA') $IURAN_KOPERASI_DINATERA = $row_pinjaman->TOTAL;
				if ($row_pinjaman->JENIS_POTONGAN == 'IURAN KOPERASI AVICENNA') $IURAN_KOPERASI_AVICENNA = $row_pinjaman->TOTAL;
				if ($row_pinjaman->JENIS_POTONGAN == 'PINJAMAN BANK BWS') $PINJAMAN_BANK_BWS = $row_pinjaman->TOTAL;
				if ($row_pinjaman->JENIS_POTONGAN == 'EKSES KLAIM') $EKSES_KLAIM = $row_pinjaman->TOTAL;
				if ($row_pinjaman->JENIS_POTONGAN == 'BIAYA PEND. ANAK') $BIAYA_PEND_ANAK = $row_pinjaman->TOTAL;
				if ($row_pinjaman->JENIS_POTONGAN == 'BIAYA LAPTOP') $BIAYA_LAPTOP = $row_pinjaman->TOTAL;
				if ($row_pinjaman->JENIS_POTONGAN == 'PINJAMAN KOPERASI AVICENNA') $PINJAMAN_KOPERASI_AVICENNA = $row_pinjaman->TOTAL;
			}
			
			$PINJAMAN = isset($pinjaman->TOTAL_PINJAMAN) ? $pinjaman->TOTAL_PINJAMAN : 0;

			
			$TUNJ_OTTW = $k->TUNJ_OTTW;
			$TUNJ_LAINNYA_1 = $k->TUNJ_LAINNYA_1;
			$TUNJ_LAINNYA_2 = $k->TUNJ_LAINNYA_2;
			$TUNJ_LAINNYA_3 = $k->TUNJ_LAINNYA_3;
			$TUNJ_LAINNYA_4 = $k->TUNJ_LAINNYA_4;
			$TUNJ_LAINNYA_5 = $k->TUNJ_LAINNYA_5;
			$TUNJ_JABATAN = $TUNJ_OTTW + $TUNJ_LAINNYA_1 + $TUNJ_LAINNYA_2 + $TUNJ_LAINNYA_3 + $TUNJ_LAINNYA_4 + $TUNJ_LAINNYA_5;
			// $TOTAL_TUNJANGAN = $TUNJ_JABATAN + $TUNJ_KEAHLIAN + $TUNJ_KOMUNIKASI + $TUNJ_PROYEK + $TUNJ_SHIFT + $TUNJ_BACKUP + $TUNJ_KEHADIRAN + $TUNJ_MAKAN + $TUNJ_TRANSPORT + $LHK + $LHL + $IHB + $MEDICAL + $ADJUSMENT_PLUS + $THR;
			$TOTAL_TUNJANGAN = $TUNJ_OTTW + $TUNJ_LAINNYA_1 + $TUNJ_LAINNYA_2 + $TUNJ_LAINNYA_3 + $TUNJ_LAINNYA_4 + $TUNJ_LAINNYA_5  + $TUNJ_SHIFT + $TUNJ_BACKUP + $TUNJ_KEHADIRAN + $TUNJ_MAKAN + $TUNJ_TRANSPORT + $LHK + $LHL + $IHB + $MEDICAL;
			
			// echo $GAJI_POKOK_NET.'<br>'; 
			// echo $TUNJ_KELUARGA.'<br>'; 

			// die();
			$TOTAL_POTONGAN = $BPJS_JHT + $BPJS_JKK + $BPJS_JKM + $BPJS_KES + $BPJS_JP + $ANGSURAN + $PINJAMAN;

			$DATA_KELEBIHAN_JAM_AJAR = db_first(" SELECT * FROM kelebihan_jamajar WHERE PERIODE_ID='$PERIODE_ID' AND KARYAWAN_ID='$k->KARYAWAN_ID'  AND STATUS = 'APPROVED' ");

			$KELEBIHAN_JAM_AJAR = $DATA_KELEBIHAN_JAM_AJAR->TOTAL;

			$NILAI_KELEBIHAN_JAM_AJAR = $DATA_KELEBIHAN_JAM_AJAR->NILAI;

			//print_r($KELEBIHAN_JAM_AJAR); die();

			$TOTAL_GAJI_KOTOR = $GAJI_POKOK_NET + $TOTAL_TUNJANGAN - $TOTAL_POTONGAN;


			$TOTAL_GAJI_BERSIH = $TOTAL_GAJI_KOTOR + $ADJUSMENT_PLUS - $ADJUSMENT_MINUS;

			$TOTAL_GAJI_SETAHUN = $TOTAL_GAJI_BERSIH * 12;

			

			db_execute("
				INSERT IGNORE penggajian
				(
				PERIODE_ID,PROJECT_ID,KARYAWAN_ID,GAJI_POKOK,GAJI_POKOK_PRORATA,TIDAK_MASUK,GAJI_POKOK_NET,TUNJ_JABATAN,TUNJ_KEAHLIAN,TUNJ_KOMUNIKASI,
				TUNJ_BACKUP,TUNJ_KEHADIRAN,TUNJ_MAKAN,TUNJ_TRANSPORT,TUNJ_PROYEK,TUNJ_SHIFT,LHK,LHL,IHB,MEDICAL,ADJUSMENT_PLUS,ADJUSMENT_MINUS,TOTAL_TUNJANGAN,TOTAL_GAJI_KOTOR,
				BPJS_JHT,BPJS_JP,BPJS_KES,BPJS_JHT_PERUSAHAAN,BPJS_JP_PERUSAHAAN,BPJS_KES_PERUSAHAAN,BPJS_JKK,BPJS_JKM,ANGSURAN,PINJAMAN,TOTAL_POTONGAN,TOTAL_GAJI_BERSIH,KUOTA_CUTI,CUTI_PERIODE_SEBELUMNYA,CUTI_PERIODE_INI,SISA_CUTI,TUNJ_KELUARGA,TUNJ_OTTW,TUNJ_LAINNYA_1,TUNJ_LAINNYA_2,TUNJ_LAINNYA_3,TUNJ_LAINNYA_4,TUNJ_LAINNYA_5,PINJAMAN_KOPERASI_DINATERA,IURAN_KOPERASI_DINATERA,IURAN_KOPERASI_AVICENNA,PINJAMAN_BANK_BWS,EKSES_KLAIM,BIAYA_PEND_ANAK,BIAYA_LAPTOP,PINJAMAN_KOPERASI_AVICENNA, KELEBIHAN_JAM_AJAR, NILAI_KELEBIHAN_JAM_AJAR
				)
				VALUES
				(
				'$PERIODE_ID','$PROJECT_ID','$k->KARYAWAN_ID','$GAJI_POKOK_KOTOR','$GAJI_POKOK_PRORATA_NET','$TIDAK_MASUK','$GAJI_POKOK_NET','$TUNJ_JABATAN','$TUNJ_KEAHLIAN','$TUNJ_KOMUNIKASI',
				'$TUNJ_BACKUP','$TUNJ_KEHADIRAN','$TUNJ_MAKAN','$TUNJ_TRANSPORT','$TUNJ_PROYEK','$TUNJ_SHIFT','$LHK','$LHL','$IHB','$MEDICAL','$ADJUSMENT_PLUS','$ADJUSMENT_MINUS','$TOTAL_TUNJANGAN','$TOTAL_GAJI_KOTOR',
				'$BPJS_JHT','$BPJS_JP','$BPJS_KES','$BPJS_JHT_PERUSAHAAN','$BPJS_JP_PERUSAHAAN','$BPJS_KES_PERUSAHAAN','$BPJS_JKK','$BPJS_JKM','$ANGSURAN','$PINJAMAN','$TOTAL_POTONGAN','$TOTAL_GAJI_BERSIH','$KUOTA_CUTI','$CUTI_PERIODE_SEBELUMNYA','$CUTI_TERPAKAI','$SALDO_CUTI','$TUNJ_KELUARGA','$TUNJ_OTTW','$TUNJ_LAINNYA_1','$TUNJ_LAINNYA_2','$TUNJ_LAINNYA_3','$TUNJ_LAINNYA_4','$TUNJ_LAINNYA_5','$PINJAMAN_KOPERASI_DINATERA','$IURAN_KOPERASI_DINATERA','$IURAN_KOPERASI_AVICENNA','$PINJAMAN_BANK_BWS','$EKSES_KLAIM','$BIAYA_PEND_ANAK','$BIAYA_LAPTOP','$PINJAMAN_KOPERASI_AVICENNA', '$KELEBIHAN_JAM_AJAR', '$NILAI_KELEBIHAN_JAM_AJAR'
				)
			");

			db_execute("
				INSERT IGNORE bpjs
				(
				PERIODE_ID,PROJECT_ID,KARYAWAN_ID,GAJI_POKOK,GAJI_POKOK_PRORATA,BPJS_JHT,BPJS_JP,BPJS_JKK,BPJS_JKM,BPJS_KES,BPJS_JHT_PERUSAHAAN,BPJS_JP_PERUSAHAAN,BPJS_JKK_PERUSAHAAN,BPJS_JKM_PERUSAHAAN,BPJS_KES_PERUSAHAAN,TOTAL_BPJS_KARYAWAN,TOTAL_BPJS_PERUSAHAAN
				)
				VALUES
				(
				'$PERIODE_ID','$PROJECT_ID','$k->KARYAWAN_ID','$GAJI_POKOK_NET','$GAJI_POKOK_PRORATA_NET','$BPJS_JHT','$BPJS_JP','$BPJS_JKK','$BPJS_JKM','$BPJS_KES','$BPJS_JHT_PERUSAHAAN','$BPJS_JP_PERUSAHAAN','$BPJS_JKK_PERUSAHAAN','$BPJS_JKM_PERUSAHAAN','$BPJS_KES_PERUSAHAAN','$TOTAL_BPJS_KARYAWAN','$TOTAL_BPJS_PERUSAHAAN'
				)
			");
		}
	}

	header('location: penggajian.php?m=1');
	exit;
}

if (isset($_GET['export'])) {
	$PERIODE_ID = get_input('PERIODE_ID');
	$PROJECT_ID = get_input('PROJECT_ID');
	header('location: export-gaji.php?PERIODE_ID=' . $PERIODE_ID . '&PROJECT_ID=' . $PROJECT_ID);
}

$JS[] = 'static/tipsy/jquery.tipsy.js';
$CSS[] = 'static/tipsy/tipsy.css';
include 'header.php';
?>

<?php
if (get_input('m') == '1') {
	$SUCCESS = 'Penggajian berhasil dibuat';
}
if (get_input('m') == 'closed') {
	$ERROR[] = 'Tidak dapat membuat laporan gaji<br>Periode penggajian sudah di tutup';
}
include 'msg.php';
?>

<section class="container-fluid">

	<div class="row" style="margin:10px 0;">
		<div class="col-sm-2">
			<div class="dropdown">
				<button class="btn btn-sm btn-default dropdown-toggle" type="button" id="dd1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="width:100%;">
					<i class="fa fa-cog"></i>&nbsp;&nbsp;Action <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" aria-labelledby="dd1">
					<li><a href="javascript:void(0)" id="btn-generate"><i class="fa fa-cog"></i>&nbsp;&nbsp;Generate</a></li>
					<li><a href="javascript:void(0)" id="btn-slip"><i class="fa fa-print"></i>&nbsp;&nbsp;Slip Gaji</a></li>
					<li><a href="javascript:void(0)" id="btn-export"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Export</a></li>
					<!-- <li><a href="javascript:void(0)" id="btn-dbank"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;DBank</a></li> -->
					<?php /*<li role="separator" class="divider"></li>*/ ?>
				</ul>
			</div>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('PERIODE_ID', dropdown_option('periode', 'PERIODE_ID', 'PERIODE', 'ORDER BY PERIODE_ID DESC'), get_search('PENGGAJIAN', 'PERIODE_ID'), ' id="PERIODE_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('PROJECT_ID', project_option_filter(0), get_search('PENGGAJIAN', 'PROJECT_ID'), ' id="PROJECT_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<input type="text" id="NAMA" value="<?php echo get_search('PENGGAJIAN', 'NAMA') ?>" class="form-control input-sm" placeholder="Nama....">
		</div>
		<div class="col-sm-4">
			<h1 style="margin:0;text-align:right;">Penggajian</h1>
		</div>
	</div>

	<section class="content">
		<div id="t-responsive" class="table-responsive">
			<table id="t" style="min-height:200px;"></table>
		</div>
	</section>
</section>

<script>
	$(document).ready(function() {
		$('#t').datagrid({
			queryParams: {
				'PERIODE_ID': $('#PERIODE_ID').val(),
				'PROJECT_ID': $('#PROJECT_ID').val(),
				'NAMA': $('#NAMA').val()
			},
			url: 'penggajian-json.php',
			fit: true,
			border: true,
			nowrap: false,
			striped: true,
			collapsible: true,
			remoteSort: true,
			sortName: 'NAMA',
			sortOrder: 'asc',
			singleSelect: false,
			pagination: true,
			pageSize: 50,
			pageList: [50, 100],
			rownumbers: true,
			frozenColumns: [
				[{
						field: 'ck',
						checkbox: true
					},
					//{field:'PERIODE',title:'Periode',width:100,sortable:true,align:'center'},
					{
						field: 'NIK',
						title: 'Nik',
						width: 100,
						sortable: true,
						align: 'left'
					},
					{
						field: 'NAMA',
						title: 'Nama',
						width: 180,
						sortable: true,
						align: 'left'
					},
					// {
					// 	field: 'POSISI',
					// 	title: 'Jabatan',
					// 	width: 140,
					// 	sortable: true,
					// 	align: 'center'
					// },
					{
						field: 'JABATAN',
						title: 'Level Jabatan',
						width: 140,
						sortable: true,
						align: 'center'
					},
					{
						field: 'TGL_MASUK',
						title: 'Join Date',
						width: 90,
						sortable: true,
						align: 'center'
					},
				]
			],
			columns: [
				[
					// {
					// 	field: 'KUOTA_CUTI',
					// 	title: 'Kuota<br>Cuti',
					// 	width: 70,
					// 	sortable: false,
					// 	align: 'center',
					// 	rowspan: 2
					// },
					// {
					// 	field: 'CUTI_PERIODE_SEBELUMNYA',
					// 	title: 'Pot. Cuti<br>Sblmnya',
					// 	width: 70,
					// 	sortable: false,
					// 	align: 'center',
					// 	rowspan: 2
					// },
					// {
					// 	field: 'CUTI_PERIODE_INI',
					// 	title: 'Pot. Cuti<br>Sekarang',
					// 	width: 70,
					// 	sortable: false,
					// 	align: 'center',
					// 	rowspan: 2
					// },
					// {
					// 	field: 'SISA_CUTI',
					// 	title: 'Sisa<br>Cuti',
					// 	width: 70,
					// 	sortable: false,
					// 	align: 'center',
					// 	rowspan: 2
					// },
					{
						title: 'KEL. JAM AJAR',
						align: 'center',
						colspan: 2
					},
					{
						title: 'UPAH TETAP',
						align: 'center',
						colspan: 5
					},
					{
						title: 'TUNJANGAN JABATAN',
						align: 'center',
						colspan: 7
					},
					{
						title: 'TUNJANGAN TIDAK TETAP',
						align: 'center',
						colspan: 6
					},
					{
						field: 'TOTAL_TUNJANGAN',
						title: 'Total Tunjangan',
						width: 100,
						sortable: false,
						align: 'right',
						rowspan: 2
					},

					{
						title: 'BPJS',
						align: 'center',
						colspan: 6
					},

					{
						field: 'TOTAL_POTONGAN',
						title: 'POTONGAN',
						width: 100,
						sortable: false,
						align: 'center',
						colspan: 9

					},
					{
						field: 'TOTAL_POTONGAN',
						title: 'TOTAL POTONGAN',
						width: 100,
						sortable: false,
						align: 'right',
						rowspan: 2
					},
					{
						field: 'TOTAL_GAJI_KOTOR',
						title: 'Penghasilan',
						width: 100,
						sortable: false,
						align: 'right',
						rowspan: 2
					},
					{
						title: 'ADJUSTMENT',
						align: 'center',
						colspan: 2
					},
					{
						field: 'TOTAL_GAJI_BERSIH',
						title: 'Total Diterima',
						width: 100,
						sortable: false,
						align: 'right',
						rowspan: 2
					},
				],
				[	{
						field: 'KELEBIHAN_JAM_AJAR',
						title: 'JAM',
						width: 80,
						sortable: false,
						align: 'center'
					},
					{
						field: 'NILAI_KELEBIHAN_JAM_AJAR',
						title: 'NILAI',
						width: 80,
						sortable: false,
						align: 'center'
					},
					{
						field: 'GAJI_POKOK',
						title: 'GAJI POKOK',
						width: 80,
						sortable: false,
						align: 'right'
					},
					{
						field: 'GAJI_POKOK_PRORATA',
						title: 'GP Prorata',
						width: 80,
						sortable: false,
						align: 'right'
					},
					{
						field: 'TUNJ_KELUARGA',
						title: 'Tunj Keluarga',
						width: 80,
						sortable: false,
						align: 'right'
					},
					{
						field: 'TIDAK_MASUK',
						title: 'Tidak Masuk',
						width: 80,
						sortable: false,
						align: 'right'
					},
					{
						field: 'UPAH_TETAP',
						title: 'TOTAL',
						width: 80,
						sortable: false,
						align: 'right'
					},

					{
						field: 'TUNJ_OTTW',
						title: 'OTTW',
						width: 80,
						sortable: false,
						align: 'right'
					},
					{
						field: 'TUNJ_LAINNYA_1',
						title: 'K/WK.SEK',
						width: 80,
						sortable: false,
						align: 'right'
					},
					{
						field: 'TUNJ_LAINNYA_2',
						title: 'S.Kurikulum',
						width: 80,
						sortable: false,
						align: 'right'
					},
					{
						field: 'TUNJ_LAINNYA_3',
						title: 'K.MGMP',
						width: 80,
						sortable: false,
						align: 'right'
					},
					{
						field: 'TUNJ_LAINNYA_4',
						title: 'Walikelas',
						width: 80,
						sortable: false,
						align: 'right'
					},
					{
						field: 'TUNJ_LAINNYA_5',
						title: 'Kel. Ajar',
						width: 80,
						sortable: false,
						align: 'right'
					},
					{
						field: 'TOTAL_TUNJANGAN_TETAP',
						title: 'TOTAL',
						width: 80,
						sortable: false,
						align: 'right'
					},


					{
						field: 'TUNJ_KEHADIRAN',
						title: 'I. Kehadiran',
						width: 80,
						sortable: false,
						align: 'right'
					},
					{
						field: 'TUNJ_MAKAN_TRANSPORT',
						title: 'T. Makan - Trs',
						width: 80,
						sortable: false,
						align: 'right'
					},
					// {
					// 	field: 'TUNJ_TRANSPORT',
					// 	title: 'T. Transport',
					// 	width: 80,
					// 	sortable: false,
					// 	align: 'right'
					// },
					{
						field: 'LHK',
						title: 'LHK',
						width: 80,
						sortable: false,
						align: 'right'
					},
					{
						field: 'LHL',
						title: 'LHL',
						width: 80,
						sortable: false,
						align: 'right'
					},
					{
						field: 'IHB',
						title: 'IHB',
						width: 80,
						sortable: false,
						align: 'right'
					},
					{
						field: 'TOTAL_TUNJANGAN_TDK_TETAP',
						title: 'TOTAL',
						width: 80,
						sortable: false,
						align: 'right'
					},


					{
						field: 'BPJS_JKK',
						title: 'BPJS JKK',
						width: 80,
						sortable: false,
						align: 'right'
					},
					{
						field: 'BPJS_JHT',
						title: 'BPJS JHT',
						width: 80,
						sortable: false,
						align: 'right'
					},
					{
						field: 'BPJS_JKM',
						title: 'BPJS JKM',
						width: 80,
						sortable: false,
						align: 'right'
					},
					{
						field: 'BPJS_JP',
						title: 'BPJS JP',
						width: 80,
						sortable: false,
						align: 'right'
					},
					{
						field: 'BPJS_KES',
						title: 'BPJS KES',
						width: 80,
						sortable: false,
						align: 'right'
					},
					{
						field: 'TOTAL_BPJS',
						title: 'TOTAL',
						width: 80,
						sortable: false,
						align: 'right'
					},


					{
						field: 'PINJAMAN_KOPERASI_DINATERA',
						title: 'P. KOP. DINATERA',
						width: 105,
						sortable: false,
						align: 'right'
					},
					{
						field: 'IURAN_KOPERASI_DINATERA',
						title: 'I. KOP. DINATERA',
						width: 105,
						sortable: false,
						align: 'right'
					},
					{
						field: 'IURAN_KOPERASI_AVICENNA',
						title: 'I. KOP. AVICENNA',
						width: 105,
						sortable: false,
						align: 'right'
					},
					{
						field: 'PINJAMAN_KOPERASI_AVICENNA',
						title: 'P. KOP. AVICENNA',
						width: 105,
						sortable: false,
						align: 'right'
					},
					{
						field: 'PINJAMAN_BANK_BWS',
						title: 'P. BANK BWS',
						width: 80,
						sortable: false,
						align: 'right'
					},
					{
						field: 'EKSES_KLAIM',
						title: 'E. KLAIM',
						width: 80,
						sortable: false,
						align: 'right'
					},
					{
						field: 'BIAYA_PEND_ANAK',
						title: 'B. PEND. ANAK',
						width: 80,
						sortable: false,
						align: 'right'
					},
					{
						field: 'BIAYA_LAPTOP',
						title: 'B. LAPTOP',
						width: 80,
						sortable: false,
						align: 'right'
					},
					{
						field: 'PINJAMAN',
						title: 'TOTAL',
						width: 80,
						sortable: false,
						align: 'right'
					},

					{
						field: 'ADJUSMENT_PLUS',
						title: 'Adjusment +',
						width: 80,
						sortable: false,
						align: 'right'
					},
					{
						field: 'ADJUSMENT_MINUS',
						title: 'Adjusment -',
						width: 80,
						sortable: false,
						align: 'right'
					},

					// {field: 'ANGSURAN',title: 'Angsuran',width: 80,sortable: false,align: 'right'},

					//{field:'THR',title:'THR',width:80,sortable:false,align:'right'},

					// {
					// 	field: 'LHK',
					// 	title: 'Lembur HK',
					// 	width: 80,
					// 	sortable: false,
					// 	align: 'right'
					// },
					// {
					// 	field: 'LHL',
					// 	title: 'Lembur HL',
					// 	width: 80,
					// 	sortable: false,
					// 	align: 'right'
					// },
					// {
					// 	field: 'IHB',
					// 	title: 'IHB',
					// 	width: 80,
					// 	sortable: false,
					// 	align: 'right'
					// },
					// {
					// 	field: 'MEDICAL',
					// 	title: 'Medical',
					// 	width: 80,
					// 	sortable: false,
					// 	align: 'right'
					// },
				]
			],
			onLoadSuccess: function(data) {
				$('.tip').tipsy({
					opacity: 1,
				});
			}
		});
		$(window).resize(function() {
			datagrid();
		});

		$('#btn-print').click(function() {
			var sel = $('#t').datagrid('getSelected');
			if (sel == undefined) alert('No item selected');
			else window.location = 'lembur-action.php?op=edit&id=' + sel.LEMBUR_ID;
			return false;
		});

		$('#btn-generate').click(function() {
			window.location = 'penggajian.php?generate=1&PERIODE_ID=' + $('#PERIODE_ID').val() + '&PROJECT_ID=' + $('#PROJECT_ID').val();
			return false;
		});
		$('#btn-export').click(function() {
			window.location = 'penggajian.php?export=1&PERIODE_ID=' + $('#PERIODE_ID').val() + '&PROJECT_ID=' + $('#PROJECT_ID').val();
			return false;
		});
		$('#btn-dbank').click(function() {
			window.location = 'penggajian-dbank.php?PERIODE_ID=' + $('#PERIODE_ID').val() + '&PROJECT_ID=' + $('#PROJECT_ID').val();
			return false;
		});
		$('#btn-slip').click(function() {
			var sel = $('#t').datagrid('getSelected');
			if (sel == undefined) alert('No item selected');
			else {
				var rows = $('#t').datagrid('getSelections');
				var QS = '';
				$.each(rows, function(index, value) {
					QS += '&ids[]=' + value.KARYAWAN_ID;
				});
				window.open('penggajian-slip.php?PERIODE_ID=' + $('#PERIODE_ID').val() + '&PROJECT_ID=' + $('#PROJECT_ID').val() + QS, '_blank');
			}
			return false;
		});

		$('#btn-search').click(function() {
			doSearch();
			return false;
		});

		$('#PERIODE_ID, #PROJECT_ID').change(function() {
			doSearch();
			return false;
		});

		$('.input-search, #NAMA').keypress(function(e) {
			if (e.which == 13) {
				doSearch();
				e.preventDefault();
			}
		});

		$('#btn-reset').click(function() {
			$('#PERIODE_ID').val("");
			$('#PROJECT_ID').val("");
			$('#NAMA').val("");
			doSearch();
			return false;
		});
		datagrid();
	});

	function datagrid() {
		var wind = parseInt($(window).height());
		var top = parseInt($('.navbar').outerHeight());
		$('#t-responsive').height(wind - top - 70);
		$('#t').datagrid('resize');
	}

	function doSearch() {
		$('#t').datagrid('load', {
			PERIODE_ID: $('#PERIODE_ID').val(),
			PROJECT_ID: $('#PROJECT_ID').val(),
			NAMA: $('#NAMA').val(),
		});
	}
</script>

<?php
include 'footer.php';
?>