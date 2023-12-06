<?php

include 'app-load.php';

is_login('pph.view');

$MODULE = 'PPH';

if( isset($_GET['generate']) )
{
	is_login('pph.generate');
	ini_set('max_execution_time', 0);

	$PERIODE_ID = get_input('PERIODE_ID');
	$COMPANY_ID = get_input('COMPANY_ID');
	
	$PERIODE = db_first(" SELECT * FROM periode WHERE PERIODE_ID='$PERIODE_ID' ");
	
	if( ! isset($PERIODE->PERIODE_ID) )
	{
		header('location: pph.php');
		exit;
	}
	if( $PERIODE->STATUS_PERIODE == 'CLOSED' )
	{
		header('location: pph.php?m=closed');
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


	//$PROJECT = db_first(" SELECT CUTOFF FROM project WHERE PROJECT_ID='$PROJECT_ID' ");
	$company = db_fetch(" SELECT PROJECT_ID FROM project WHERE COMPANY_ID='$COMPANY_ID' ");
	if( count($company) > 0 )
	{
		foreach($company as $c)
		{
			$PROJECT_ID = $c->PROJECT_ID;
			$PROJECT = db_first(" SELECT CUTOFF FROM project WHERE PROJECT_ID='$PROJECT_ID' ");
			$CUTOFF = isset($PROJECT->CUTOFF) ? $PROJECT->CUTOFF : 0;
			
			if( $CUTOFF == '1' )
			{
				$TGL_MULAI2 = $PERIODE->TANGGAL_MULAI2;
				$TGL_SELESAI2 = $PERIODE->TANGGAL_SELESAI2;
			}

			db_execute(" DELETE FROM pph WHERE PERIODE_ID='$PERIODE_ID' AND PROJECT_ID='$PROJECT_ID' ");
			
			$karyawan = db_fetch("
				SELECT *
				FROM karyawan K
				LEFT JOIN jabatan J ON J.JABATAN_ID=K.JABATAN_ID
				WHERE J.PROJECT_ID='$PROJECT_ID' AND ST_KERJA='AKTIF'
			");

			if( count($karyawan) > 0 )
			{
				foreach($karyawan as $k)
				{
					$rs = db_fetch("
						SELECT *, K.SHIFT_CODE
						FROM shift_karyawan K
							LEFT JOIN shift S ON S.SHIFT_CODE = K.SHIFT_CODE
						WHERE
							KARYAWAN_ID='$k->KARYAWAN_ID' AND
							(DATE >= '$TGL_MULAI' AND DATE <= '$TGL_SELESAI')
					");
					
					$SHIFT = array();
					if(count($rs)>0){
						foreach($rs as $row){
							$SHIFT[$k->KARYAWAN_ID][$row->DATE] = $row;
						}
					}
					
					$SCAN = parse_scan($k->KARYAWAN_ID,$TGL_MULAI,$TGL_SELESAI,$SHIFT);
					$SCAN_PAYROLL = $SCAN;
					
					if( $CUTOFF == '1' )
					{
						$rs = db_fetch("
							SELECT *, K.SHIFT_CODE
							FROM shift_karyawan K
								LEFT JOIN shift S ON S.SHIFT_CODE = K.SHIFT_CODE
							WHERE
								KARYAWAN_ID='$k->KARYAWAN_ID' AND
								(DATE >= '$TGL_MULAI2' AND DATE <= '$TGL_SELESAI2')
						");
						$SHIFT2 = array();
						if(count($rs)>0){
							foreach($rs as $row){
								$SHIFT2[$k->KARYAWAN_ID][$row->DATE] = $row;
							}
						}
						$SCAN_PAYROLL = parse_scan($k->KARYAWAN_ID,$TGL_MULAI2,$TGL_SELESAI2,$SHIFT2);
					}

					$GAJI_POKOK = $GAJI_POKOK_PRORATA = $TIDAK_MASUK = $GAJI_POKOK_NET = 0;
					
					if($k->R_GAJI_POKOK=='1')
					{
						$GAJI_POKOK_KOTOR = $GAJI_POKOK_PRORATA = $k->GAJI_POKOK;
						
						$TOTAL_DAY = isset($SCAN_PAYROLL['total_day']) ? $SCAN_PAYROLL['total_day'] : 0;
						$TOTAL_RESIGN = isset($SCAN_PAYROLL['total_r']) ? $SCAN_PAYROLL['total_r'] : 0;
						$TOTAL_BELUM_MASUK = isset($SCAN_PAYROLL['total_bm']) ? $SCAN_PAYROLL['total_bm'] : 0;
						$TOTAL_CM = isset($SCAN_PAYROLL['total_cm']) ? $SCAN_PAYROLL['total_cm'] : 0;
						$TOTAL_UL = isset($SCAN_PAYROLL['total_ul']) ? $SCAN_PAYROLL['total_ul'] : 0;
						$PRORATA = $TOTAL_RESIGN + $TOTAL_BELUM_MASUK + $TOTAL_CM + $TOTAL_UL;
						if( $PRORATA > 0 )
						{
							$PEMOTONG = round(($PRORATA/$TOTAL_DAY) * $k->GAJI_POKOK,0);
							$GAJI_POKOK_PRORATA = $k->GAJI_POKOK - $PEMOTONG;
						}
					}
					
					$JOIN_DATE = $k->TGL_MASUK;
					$CURRENT_DATE = $TGL_SELESAI;
					$JOIN_TIME = strtotime($JOIN_DATE);
					$CURRENT_TIME = strtotime($CURRENT_DATE);
					$CURR_MONTH = $TAHUN .'-'. $BULAN .'-'. date('d',$JOIN_TIME);
					$JOIN_MONTH = $TAHUN .'-'. date('m',$JOIN_TIME) .'-'. date('d',$JOIN_TIME);
					if( $CURR_MONTH >= $JOIN_MONTH )
					{
						$VALID_UNTIL = date('Y-m-d',strtotime($JOIN_MONTH . " + 1 year -1 month "));
					}
					if( $CURR_MONTH < $JOIN_MONTH )
					{
						$VALID_UNTIL = date('Y-m-d',strtotime($JOIN_MONTH . " -1 month "));
					}
					
					$KUOTA_CUTI = 0;
					$JOIN_DAY = round((strtotime($CURR_MONTH) - strtotime($JOIN_DATE))/86400,0);
					if( $JOIN_DAY >= 365 OR $k->R_CUTI=='0' )
					{
						$KUOTA_CUTI = 12;
					}
					else
					{
						$CUTI_BERJALAN = cuti_berjalan($JOIN_DATE);
						$KUOTA_CUTI = isset($CUTI_BERJALAN[$CURR_MONTH]) ? $CUTI_BERJALAN[$CURR_MONTH] : 0;
					}
					
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
					
					if($k->R_POT_ABSEN_CUTI=='1')
					{
						if($SISA_CUTI >= $TOTAL_ABS)
						{
							$CUTI_TERPAKAI = $CUTI_TERPAKAI + $TOTAL_ABS;
							$SISA_CUTI = $SISA_CUTI - $TOTAL_ABS;
							$TOTAL_ABS = 0;
						}
						else if (($SISA_CUTI > 0) AND ($SISA_CUTI < $TOTAL_ABS))
						{
							$CUTI_TERPAKAI = $CUTI_TERPAKAI + $SISA_CUTI;
							$SISA_CUTI_TMP = $SISA_CUTI;
							$SISA_CUTI = $SISA_CUTI - $TOTAL_ABS;
							$TOTAL_ABS = $TOTAL_ABS - $SISA_CUTI_TMP;
						}
					}
					
					if($k->R_POT_NSKD_CUTI=='1')
					{
						if($SISA_CUTI >= $TOTAL_NSKD)
						{
							$CUTI_TERPAKAI = $CUTI_TERPAKAI + $TOTAL_NSKD;
							$SISA_CUTI = $SISA_CUTI - $TOTAL_NSKD;
							$TOTAL_NSKD = 0;
						}
						else if (($SISA_CUTI > 0) AND ($SISA_CUTI < $TOTAL_NSKD))
						{
							$CUTI_TERPAKAI = $CUTI_TERPAKAI + $SISA_CUTI;
							$SISA_CUTI_TMP = $SISA_CUTI;
							$SISA_CUTI = $SISA_CUTI - $TOTAL_NSKD;
							$TOTAL_NSKD = $TOTAL_NSKD - $SISA_CUTI_TMP;
							if( $SISA_CUTI < 0) $SISA_CUTI = 0;
						}
					}
					
					$CUTI_TERPAKAI = $CUTI_TERPAKAI + $TOTAL_CT;
					$SALDO_CUTI = $KUOTA_CUTI - ($CUTI_PERIODE_SEBELUMNYA + $CUTI_TERPAKAI);
					if($CUTI_TERPAKAI > 0)
					{
						db_execute(" INSERT INTO cuti_tahunan (KARYAWAN_ID,PERIODE_ID,VALID_UNTIL,CUTI) VALUES ('$k->KARYAWAN_ID','$PERIODE_ID','$VALID_UNTIL','$CUTI_TERPAKAI') ");
					}
					
					if($k->R_POT_ABSEN_GP=='1')
					{
						$TIDAK_MASUK = $TIDAK_MASUK + ($k->GAJI_POKOK * (1/25)) * $TOTAL_ABS;
					}
					if($k->R_POT_NSKD_GP=='1')
					{
						$TIDAK_MASUK = $TIDAK_MASUK + ($k->GAJI_POKOK * (1/25)) * $TOTAL_NSKD;
					}
					
					$GAJI_POKOK_NET = $GAJI_POKOK_PRORATA - $TIDAK_MASUK;
					
					$TUNJ_JABATAN = 0;
					if($k->R_TUNJ_JABATAN=='1')
					{
						$TUNJ_JABATAN = $k->TUNJ_JABATAN;
					}
					
					$TUNJ_KEAHLIAN = 0;
					if($k->R_TUNJ_KEAHLIAN=='1')
					{
						$TUNJ_KEAHLIAN = $k->TUNJ_KEAHLIAN;
						if($k->R_POT_ABSEN_GP=='1')
						{
							$TUNJ_KEAHLIAN = $TUNJ_KEAHLIAN - (($TUNJ_KEAHLIAN * (1/25)) * $TOTAL_ABS);
						}
						if($k->R_POT_NSKD_GP=='1')
						{
							$TUNJ_KEAHLIAN = $TUNJ_KEAHLIAN - (($TUNJ_KEAHLIAN * (1/25)) * $TOTAL_NSKD);
						}
					}
					
					$TUNJ_KOMUNIKASI = 0;
					if($k->R_TUNJ_KOMUNIKASI=='1')
					{
						$TUNJ_KOMUNIKASI = $k->TUNJ_KOMUNIKASI;
						if($k->R_POT_ABSEN_GP=='1')
						{
							$TUNJ_KOMUNIKASI = $TUNJ_KOMUNIKASI - (($TUNJ_KOMUNIKASI * (1/25)) * $TOTAL_ABS);
						}
						if($k->R_POT_NSKD_GP=='1')
						{
							$TUNJ_KOMUNIKASI = $TUNJ_KOMUNIKASI - (($TUNJ_KOMUNIKASI * (1/25)) * $TOTAL_NSKD);
						}
					}
					
					$TUNJ_PROYEK = 0;
					if($k->R_TUNJ_PROYEK=='1')
					{
						$TUNJ_PROYEK = $k->TUNJ_PROYEK;
						if($k->R_POT_ABSEN_GP=='1')
						{
							$TUNJ_PROYEK = $TUNJ_PROYEK - (($TUNJ_PROYEK * (1/25)) * $TOTAL_ABS);
						}
						if($k->R_POT_NSKD_GP=='1')
						{
							$TUNJ_PROYEK = $TUNJ_PROYEK - (($TUNJ_PROYEK * (1/25)) * $TOTAL_NSKD);
						}
					}
					
					$TUNJ_SHIFT = 0;
					if($k->R_TUNJ_SHIFT=='1')
					{
						$TUNJ_SHIFT = $k->TUNJ_SHIFT;
						if($k->R_POT_ABSEN_GP=='1')
						{
							$TUNJ_SHIFT = $TUNJ_SHIFT - (($TUNJ_SHIFT * (1/25)) * $TOTAL_ABS);
						}
						if($k->R_POT_NSKD_GP=='1')
						{
							$TUNJ_SHIFT = $TUNJ_SHIFT - (($TUNJ_SHIFT * (1/25)) * $TOTAL_NSKD);
						}
					}
					
					$TUNJ_BACKUP = 0;
					if($k->R_TUNJ_BACKUP=='1')
					{
						$TOTAL_BACKUP = isset($SCAN['total_backup']) ? $SCAN['total_backup'] : 0;
						$TUNJ_BACKUP = $k->TUNJ_BACKUP * $TOTAL_BACKUP;
					}
					
					$TUNJ_MAKAN = 0;
					if($k->R_TUNJ_MAKAN=='1')
					{
						$TOTAL_ATT = 22;
						$TUNJ_MAKAN = $k->TUNJ_MAKAN * $TOTAL_ATT;
						if($k->R_POT_ABSEN_TUNJ_MAKAN=='1')
						{
							$TOTAL_ABSENT = isset($SCAN['total_absent']) ? $SCAN['total_absent'] : 0;
							$TUNJ_MAKAN = $TUNJ_MAKAN - ($k->TUNJ_MAKAN * $TOTAL_ABSENT);
						}
						
						if($k->R_POT_NSKD_TUNJ_MAKAN=='1')
						{
							$TOTAL_SAKIT = isset($SCAN['total_sakit']) ? $SCAN['total_sakit'] : 0;
							$TOTAL_IJIN = isset($SCAN['total_ijin']) ? $SCAN['total_ijin'] : 0;
							$TUNJ_MAKAN = $TUNJ_MAKAN - ($k->TUNJ_MAKAN * ($TOTAL_SAKIT + $TOTAL_IJIN));
						}
						
						if($k->R_POT_LATE_TUNJ_MAKAN=='1')
						{
							$TOTAL_LATE = isset($SCAN['total_late']) ? $SCAN['total_late'] : 0;
							$TUNJ_MAKAN = $TUNJ_MAKAN - ($k->TUNJ_MAKAN * $TOTAL_LATE);
							if($TUNJ_MAKAN < 0) $TUNJ_MAKAN = 0;
						}
					}
					
					$TUNJ_TRANSPORT = 0;
					if($k->R_TUNJ_TRANSPORT=='1')
					{
						$TOTAL_ATT = 22;
						$TUNJ_TRANSPORT = $k->TUNJ_TRANSPORT * $TOTAL_ATT;
						if($k->R_POT_ABSEN_TUNJ_TRANSPORT=='1')
						{
							$TOTAL_ABSENT = isset($SCAN['total_absent']) ? $SCAN['total_absent'] : 0;
							$TUNJ_TRANSPORT = $TUNJ_TRANSPORT - ($k->TUNJ_TRANSPORT * $TOTAL_ABSENT);
						}
						
						if($k->R_POT_NSKD_TUNJ_TRANSPORT=='1')
						{
							$TOTAL_SAKIT = isset($SCAN['total_sakit']) ? $SCAN['total_sakit'] : 0;
							$TOTAL_IJIN = isset($SCAN['total_ijin']) ? $SCAN['total_ijin'] : 0;
							$TUNJ_TRANSPORT = $TUNJ_TRANSPORT - ($k->TUNJ_TRANSPORT * ($TOTAL_SAKIT + $TOTAL_IJIN));
						}
					}
					
					$TUNJ_KEHADIRAN = 0;
					if($k->R_TUNJ_KEHADIRAN=='1')
					{
						$TUNJ_KEHADIRAN = round((2/25)*$k->GAJI_POKOK,0);
						
						if($k->R_POT_ABSEN_TUNJ_KEHADIRAN=='1')
						{
							$TOTAL_ABSENT = isset($SCAN['total_absent']) ? $SCAN['total_absent'] : 0;
							if( $TOTAL_ABSENT > 0 ) $TUNJ_KEHADIRAN = 0;
						}
						
						if($k->R_POT_NSKD_TUNJ_KEHADIRAN=='1')
						{
							$TOTAL_SAKIT = isset($SCAN['total_sakit']) ? $SCAN['total_sakit'] : 0;
							$TOTAL_IJIN = isset($SCAN['total_ijin']) ? $SCAN['total_ijin'] : 0;
							if( ($TOTAL_SAKIT + $TOTAL_IJIN) > 0 ) $TUNJ_KEHADIRAN = 0;
						}
						
						if($k->R_POT_SKD_TUNJ_KEHADIRAN=='1')
						{
							$TOTAL_SKD = isset($SCAN['total_skd']) ? $SCAN['total_skd'] : 0;
							if( $TOTAL_SKD > 0 ) $TUNJ_KEHADIRAN = 0;
						}
						
						if($k->R_POT_LATE_TUNJ_KEHADIRAN=='1')
						{
							$TOTAL_LATE = isset($SCAN['total_late']) ? $SCAN['total_late'] : 0;
							if( $TOTAL_LATE > 0 ) $TUNJ_KEHADIRAN = 0;
						}
						
						if($k->R_POT_EARLY_TUNJ_KEHADIRAN=='1')
						{
							$TOTAL_EARLY = isset($SCAN['total_early']) ? $SCAN['total_early'] : 0;
							if( $TOTAL_EARLY > 0 ) $TUNJ_KEHADIRAN = 0;
						}
					}
					
					$LHK = $LHL = $IHB = 0;
					if($k->R_LEMBUR=='1')
					{
						$lembur = db_fetch(" SELECT * FROM lembur WHERE KARYAWAN_ID='$k->KARYAWAN_ID' AND PERIODE_ID='$PERIODE_ID' AND STATUS='APPROVED' ");
						if(count($lembur) > 0)
						{
							foreach($lembur as $row)
							{
								$JENIS_LEMBUR = isset($row->JENIS) ? $row->JENIS : '';
								$TOTAL_JAM = isset($row->TOTAL_JAM) ? $row->TOTAL_JAM : '';
								if($JENIS_LEMBUR=='LHK'){
									$LEMBUR = hitung_lembur_LHK($row->TOTAL_JAM,$k->GAJI_POKOK,$row->ADJUSMENT);
									$LHK = $LHK + $LEMBUR['TOTAL'];
								}
								if($JENIS_LEMBUR=='LHL'){
									$LEMBUR = hitung_lembur_LHL($row->TOTAL_JAM,$k->GAJI_POKOK,$row->ADJUSMENT);
									$LHL = $LHL + $LEMBUR['TOTAL'];
								}
								if($JENIS_LEMBUR=='IHB'){
									$LEMBUR = hitung_lembur_IHB($row->TOTAL_JAM,$k->GAJI_POKOK,$row->ADJUSMENT);
									$IHB = $IHB + $LEMBUR['TOTAL'];
								}
							}
						}
					}
					
					$MEDICAL = 0;
					if($k->R_MEDICAL_CASH=='1')
					{
						$med = db_first(" SELECT SUM(TOTAL) as TOTAL FROM medical WHERE KARYAWAN_ID='$k->KARYAWAN_ID' AND PERIODE_ID='$PERIODE_ID' AND STATUS='APPROVED' ");
						$MEDICAL = isset($med->TOTAL) ? $med->TOTAL : 0;
					}
					
					$adj = db_first(" SELECT SUM(TOTAL) as TOTAL FROM adjusment WHERE KARYAWAN_ID='$k->KARYAWAN_ID' AND PERIODE_ID='$PERIODE_ID' AND STATUS='APPROVED' ");
					$ADJUSMENT = isset($adj->TOTAL) ? $adj->TOTAL : 0;
					
					$TOTAL_TUNJANGAN = $TUNJ_JABATAN + $TUNJ_KEAHLIAN + $TUNJ_KOMUNIKASI + $TUNJ_PROYEK + $TUNJ_SHIFT + $TUNJ_BACKUP + $TUNJ_KEHADIRAN + $TUNJ_MAKAN + $TUNJ_TRANSPORT + $LHK + $LHL + $IHB + $MEDICAL + $ADJUSMENT + $THR;
					
					$TOTAL_GAJI_KOTOR = $GAJI_POKOK_NET + $TOTAL_TUNJANGAN;
					
					$BPJS_JHT = 0;
					if($k->R_BPJS_JHT)
					{
						$BPJS_JHT = isset($k->BPJS_JHT) ? $k->BPJS_JHT : 0;
					}
					
					$BPJS_JP = 0;
					if($k->R_BPJS_JP)
					{
						$BPJS_JP = isset($k->BPJS_JP) ? $k->BPJS_JP : 0;
					}
					
					$BPJS_KES = 0;
					if($k->R_BPJS_KES)
					{
						$BPJS_KES = isset($k->BPJS_KES) ? $k->BPJS_KES : 0;
					}
				
					$angsuran = db_first(" SELECT SUM(TOTAL) as TOTAL_ANGSURAN FROM angsuran WHERE KARYAWAN_ID='$k->KARYAWAN_ID' AND PERIODE_ID='$PERIODE_ID' AND STATUS='APPROVED' ");
					$ANGSURAN = isset($angsuran->TOTAL_ANGSURAN) ? $angsuran->TOTAL_ANGSURAN : 0;
					
					$pinjaman = db_first(" SELECT SUM(TOTAL) as TOTAL_PINJAMAN FROM pinjaman WHERE KARYAWAN_ID='$k->KARYAWAN_ID' AND PERIODE_ID='$PERIODE_ID' AND STATUS='APPROVED' ");
					$PINJAMAN = isset($pinjaman->TOTAL_PINJAMAN) ? $pinjaman->TOTAL_PINJAMAN : 0;
					
					$TOTAL_POTONGAN = $BPJS_JHT + $BPJS_JP + $BPJS_KES + $ANGSURAN + $PINJAMAN;
					
					$TOTAL_GAJI_BERSIH = $TOTAL_GAJI_KOTOR - $TOTAL_POTONGAN;

					$TOTAL_GAJI_SETAHUN = $TOTAL_GAJI_BERSIH * 12;

					$KARYAWAN_ID = $k->KARYAWAN_ID;

					$PPH_VALUES .= "('".$PERIODE_ID."','".$COMPANY_ID."','".$PROJECT_ID."','".$k->KARYAWAN_ID."','".$GAJI_POKOK_KOTOR."','".$GAJI_POKOK_PRORATA."','".$TIDAK_MASUK."','".$GAJI_POKOK_NET."','".$TUNJ_JABATAN."','".$TUNJ_KEAHLIAN."','".$TUNJ_KOMUNIKASI."','".$TUNJ_BACKUP."','".$TUNJ_KEHADIRAN."','".$TUNJ_MAKAN."','".$TUNJ_TRANSPORT."','".$TUNJ_PROYEK."','".$TUNJ_SHIFT."','".$LHK."','".$LHL."','".$IHB."','".$MEDICAL."','".$ADJUSMENT."','".$TOTAL_TUNJANGAN."','".$TOTAL_GAJI_KOTOR."','".$BPJS_JHT."','".$BPJS_JP."','".$BPJS_KES."','".$ANGSURAN."','".$PINJAMAN."','".$TOTAL_POTONGAN."','".$TOTAL_GAJI_BERSIH."','".$KUOTA_CUTI."','".$CUTI_PERIODE_SEBELUMNYA."','".$CUTI_TERPAKAI."','".$SALDO_CUTI."'),";
				}
			}
			$total_karyawan = 0;
			$total_karyawan = count($karyawan) + $total_karyawan;
		}


		$PPH_VALUES = rtrim($PPH_VALUES,',');
		if($total_karyawan < 1000){
			db_execute(" INSERT IGNORE pph (PERIODE_ID,COMPANY_ID,PROJECT_ID,KARYAWAN_ID,GAJI_POKOK,GAJI_POKOK_PRORATA,TIDAK_MASUK,GAJI_POKOK_NET,TUNJ_JABATAN,TUNJ_KEAHLIAN,TUNJ_KOMUNIKASI,TUNJ_BACKUP,TUNJ_KEHADIRAN,TUNJ_MAKAN,TUNJ_TRANSPORT,TUNJ_PROYEK,TUNJ_SHIFT,LHK,LHL,IHB,MEDICAL,ADJUSMENT,TOTAL_TUNJANGAN,TOTAL_GAJI_KOTOR,BPJS_JHT,BPJS_JP,BPJS_KES,ANGSURAN,PINJAMAN,TOTAL_POTONGAN,TOTAL_GAJI_BERSIH,KUOTA_CUTI,CUTI_PERIODE_SEBELUMNYA,CUTI_PERIODE_INI,SISA_CUTI) VALUES $PPH_VALUES ");
		}else{
			// must to split by total statement per 1000 rows
			db_execute(" INSERT IGNORE pph (PERIODE_ID,COMPANY_ID,PROJECT_ID,KARYAWAN_ID,GAJI_POKOK,GAJI_POKOK_PRORATA,TIDAK_MASUK,GAJI_POKOK_NET,TUNJ_JABATAN,TUNJ_KEAHLIAN,TUNJ_KOMUNIKASI,TUNJ_BACKUP,TUNJ_KEHADIRAN,TUNJ_MAKAN,TUNJ_TRANSPORT,TUNJ_PROYEK,TUNJ_SHIFT,LHK,LHL,IHB,MEDICAL,ADJUSMENT,TOTAL_TUNJANGAN,TOTAL_GAJI_KOTOR,BPJS_JHT,BPJS_JP,BPJS_KES,ANGSURAN,PINJAMAN,TOTAL_POTONGAN,TOTAL_GAJI_BERSIH,KUOTA_CUTI,CUTI_PERIODE_SEBELUMNYA,CUTI_PERIODE_INI,SISA_CUTI) VALUES $PPH_VALUES ");
		}
	}

	header('location: pph.php?m=1');
	exit;
}

if( isset($_GET['export_induk']) )
{
	$PERIODE_ID = get_input('PERIODE_ID');
	$COMPANY_ID = get_input('COMPANY_ID');
	header('location: export-spt-induk.php?PERIODE_ID='.$PERIODE_ID.'&COMPANY_ID='.$COMPANY_ID);
}

$JS[] = 'static/tipsy/jquery.tipsy.js';
$CSS[] = 'static/tipsy/tipsy.css';
include 'header.php';
?>

<?php
if(get_input('m') == '1'){
	$SUCCESS = 'Penggajian berhasil dibuat';
}
if(get_input('m') == 'closed'){
	$ERROR[] = 'Tidak dapat membuat laporan gaji<br>Periode pph sudah di tutup';
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
				<!-- <li><a href="javascript:void(0)" id="btn-export-induk"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Export SPT Induk</a></li> -->
				</ul>
			</div>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('PERIODE_ID',dropdown_option('periode','PERIODE_ID','PERIODE','ORDER BY PERIODE_ID DESC'),get_search($MODULE,'PERIODE_ID'),' id="PERIODE_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('COMPANY_ID',dropdown_option('company','COMPANY_ID','COMPANY','ORDER BY COMPANY_ID DESC'),get_search($MODULE,'COMPANY_ID'),' id="COMPANY_ID" class="form-control input-sm" ') ?>
			<?php //echo dropdown('COMPANY_ID',company_option(0),get_search($MODULE,'COMPANY_ID'),' id="COMPANY_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<input type="text" id="NAMA" value="<?php echo get_search($MODULE,'NAMA') ?>" class="form-control input-sm" placeholder="Nama....">
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
$(document).ready(function(){
	$('#t').datagrid({
		queryParams: { 'PERIODE_ID': $('#PERIODE_ID').val(), 'COMPANY_ID': $('#COMPANY_ID').val(), 'NAMA': $('#NAMA').val() },
		url:'pph-json.php',
		fit: true,
		border: true,
		nowrap: false,
		striped: true,
		collapsible:true,
		remoteSort: true,
		sortName: 'NAMA',
		sortOrder: 'asc',
		singleSelect:false,
		pagination: true,
		pageSize:50,
		pageList: [50,100],
		rownumbers:true,
		frozenColumns:[[
			{field:'ck',checkbox:true},
			{field:'NAMA',title:'Nama',width:200,sortable:true,align:'left'},
			{field:'TGL_MASUK',title:'Join Date',width:90,sortable:true,align:'center'},
			{field:'STATUS_PTKP',title:'PTKP',width:90,sortable:true,align:'center'},
		]],
		columns:[[
			{field:'KUOTA_CUTI',title:'Kuota<br>Cuti',width:70,sortable:false,align:'center',rowspan:2},
			{field:'CUTI_PERIODE_SEBELUMNYA',title:'Pot. Cuti<br>Sblmnya',width:70,sortable:false,align:'center',rowspan:2},
			{field:'CUTI_PERIODE_INI',title:'Pot. Cuti<br>Sekarang',width:70,sortable:false,align:'center',rowspan:2},
			{field:'SISA_CUTI',title:'Sisa<br>Cuti',width:70,sortable:false,align:'center',rowspan:2},
			{title:'GAJI POKOK',align:'center',colspan:4},
			{title:'TUNJANGAN TETAP',align:'center',colspan:5},
			{title:'TUNJANGAN TIDAK TETAP',align:'center',colspan:9},
			{field:'TOTAL_TUNJANGAN',title:'Total Tunjangan',width:100,sortable:false,align:'right',rowspan:2},
			{field:'TOTAL_GAJI_KOTOR',title:'Penghasilan',width:100,sortable:false,align:'right',rowspan:2},
			{title:'POTONGAN',align:'center',colspan:5},
			{field:'TOTAL_POTONGAN',title:'Total Potongan',width:100,sortable:false,align:'right',rowspan:2},
			{field:'TOTAL_GAJI_BERSIH',title:'Total Diterima',width:100,sortable:false,align:'right',rowspan:2},
		],[
			{field:'GAJI_POKOK',title:'GP Baru',width:80,sortable:false,align:'right'},
			{field:'GAJI_POKOK_PRORATA',title:'GP Prorata',width:80,sortable:false,align:'right'},
			{field:'TIDAK_MASUK',title:'Tidak Masuk',width:80,sortable:false,align:'right'},
			{field:'GAJI_POKOK_NET',title:'GP Nett',width:80,sortable:false,align:'right'},
			
			{field:'TUNJ_JABATAN',title:'T. Jabatan',width:80,sortable:false,align:'right'},
			{field:'TUNJ_KEAHLIAN',title:'T. Keahlian',width:80,sortable:false,align:'right'},
			{field:'TUNJ_KOMUNIKASI',title:'T. Kmnikasi',width:80,sortable:false,align:'right'},
			{field:'TUNJ_PROYEK',title:'T. Proyek',width:80,sortable:false,align:'right'},
			{field:'TUNJ_SHIFT',title:'T. Shift',width:80,sortable:false,align:'right'},
			
			{field:'TUNJ_BACKUP',title:'Backup',width:80,sortable:false,align:'right'},
			{field:'LHK',title:'Lembur HK',width:80,sortable:false,align:'right'},
			{field:'LHL',title:'Lembur HL',width:80,sortable:false,align:'right'},
			{field:'IHB',title:'IHB',width:80,sortable:false,align:'right'},
			{field:'MEDICAL',title:'Medical',width:80,sortable:false,align:'right'},
			{field:'TUNJ_KEHADIRAN',title:'I. Kehadiran',width:80,sortable:false,align:'right'},
			{field:'TUNJ_MAKAN',title:'T. Makan',width:80,sortable:false,align:'right'},
			{field:'TUNJ_TRANSPORT',title:'T. Transport',width:80,sortable:false,align:'right'},
			{field:'ADJUSMENT',title:'Adjusment',width:80,sortable:false,align:'right'},
			
			{field:'BPJS_JHT',title:'BPJS JHT',width:80,sortable:false,align:'right'},
			{field:'BPJS_JP',title:'BPJS JP',width:80,sortable:false,align:'right'},
			{field:'BPJS_KES',title:'BPJS KES',width:80,sortable:false,align:'right'},
			{field:'ANGSURAN',title:'Angsuran',width:80,sortable:false,align:'right'},
			{field:'PINJAMAN',title:'Pinjaman',width:80,sortable:false,align:'right'},
		]],
		onLoadSuccess: function(data){
			$('.tip').tipsy({
				opacity : 1,
			});
		}
	});
	$(window).resize(function(){ datagrid(); });
	
	$('#btn-print').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else window.location = 'lembur-action.php?op=edit&id='+sel.LEMBUR_ID;
		return false;
	});
	
	$('#btn-generate').click(function(){
		window.location = 'pph.php?generate=1&PERIODE_ID='+$('#PERIODE_ID').val()+'&COMPANY_ID='+$('#COMPANY_ID').val();
		return false;
	});

	$('#btn-export-induk').click(function(){
		window.location = 'pph.php?export_induk=1&PERIODE_ID='+$('#PERIODE_ID').val()+'&COMPANY_ID='+$('#COMPANY_ID').val();
		return false;
	});

	$('#btn-search').click(function(){
		doSearch();
		return false;
	});

	$('#PERIODE_ID, #COMPANY_ID').change(function(){
		doSearch();
		return false;
	});

	$('.input-search, #NAMA').keypress(function (e) {
		if (e.which == 13) {
			doSearch();
			e.preventDefault();
		}
	});
	
	$('#btn-reset').click(function(){
		$('#PERIODE_ID').val("");
		$('#COMPANY_ID').val("");
		$('#NAMA').val("");
		doSearch();
		return false;
	});
	datagrid();
});
function datagrid()
{
	var wind = parseInt($(window).height());
	var top = parseInt($('.navbar').outerHeight());
	$('#t-responsive').height(wind - top - 70);
	$('#t').datagrid('resize');
}
function doSearch(){
	$('#t').datagrid('load',{
		PERIODE_ID: $('#PERIODE_ID').val(),
		COMPANY_ID: $('#COMPANY_ID').val(),
		NAMA: $('#NAMA').val(),
	});
}
</script>

<?php
include 'footer.php';
?>