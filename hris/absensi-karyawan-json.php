<?php

include 'app-load.php';

is_login('absensi.view');

set_search('ABSENSI', array('sort', 'order', 'PROJECT_ID', 'PERIODE_ID', 'VIEW_MODE', 'TGL_MULAI', 'TGL_SELESAI', 'NAMA'));
if (get_input('clear')) clear_search('ABSENSI', array('PROJECT_ID', 'PERIODE_ID', 'VIEW_MODE', 'TGL_MULAI', 'TGL_SELESAI', 'NAMA'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'NAMA';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if ($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 500 : $PER_PAGE;
$OFFSET = ($PAGE - 1) * $PER_PAGE;

$PERIODE_ID = get_input('PERIODE_ID');
$PERIODE = db_first(" SELECT * FROM periode WHERE PERIODE_ID='$PERIODE_ID' ");
$TAHUN = $PERIODE->TAHUN;
$BULAN = $PERIODE->BULAN;

$TGL_MULAI = get_input('TGL_MULAI');
$TGL_SELESAI = get_input('TGL_SELESAI');
$RANGE = date_range($TGL_MULAI, $TGL_SELESAI);

$where = '';
$wh = array();
$wh[] = " NIK <> '' ";
if ($PROJECT_ID = get_input('PROJECT_ID') and !empty($PROJECT_ID)) $wh[] = " J.PROJECT_ID = '$PROJECT_ID' ";
if ($NAMA = get_input('NAMA') and !empty($NAMA)) $wh[] = " UPPER(NAMA) LIKE UPPER('%$NAMA%') ";
if (count($wh) > 0) $where = " WHERE " . implode(' AND ', $wh);

$rs = db_fetch("
	SELECT KARYAWAN_ID
	FROM karyawan A
	LEFT JOIN jabatan J ON J.JABATAN_ID = A.JABATAN_ID
	{$where}
");

$KARYAWAN_ID = array();
if (count($rs) > 0) {
	foreach ($rs as $row) {
		$KARYAWAN_ID[] = $row->KARYAWAN_ID;
	}
}

if (count($KARYAWAN_ID) > 0) {
	$rs = db_fetch("
		SELECT *, K.SHIFT_CODE
		FROM shift_karyawan K
			LEFT JOIN shift S ON S.SHIFT_CODE = K.SHIFT_CODE
		WHERE
			K.PROJECT_ID='$PROJECT_ID' AND
			KARYAWAN_ID IN (" . implode(',', $KARYAWAN_ID) . ") AND
			(DATE >= '$TGL_MULAI' AND DATE <= '$TGL_SELESAI')
	");
	$SHIFT = array();
	if (count($rs) > 0) {
		foreach ($rs as $row) {
			$SHIFT[$row->KARYAWAN_ID][$row->DATE] = $row;
		}
	}
}

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM karyawan A
	LEFT JOIN jabatan J ON J.JABATAN_ID = A.JABATAN_ID
	LEFT JOIN posisi P ON P.POSISI_ID = A.POSISI_ID
	{$where}
");
$NUM_ROWS = $rs->cnt;

$rs = db_fetch_limit("
	SELECT KARYAWAN_ID,NIK,NAMA,TGL_MASUK,J.*,P.POSISI
	FROM karyawan A
	LEFT JOIN jabatan J ON J.JABATAN_ID = A.JABATAN_ID
	LEFT JOIN posisi P ON P.POSISI_ID = A.POSISI_ID
	{$where}
	ORDER BY $SORT $ORDER
", $PER_PAGE, $OFFSET);

$rows = $t = array();
$key = 0;
if (count($rs) > 0) {
	foreach ($rs as $row) {
		//$SCAN = parse_scan_app($row->KARYAWAN_ID, $TGL_MULAI, $TGL_SELESAI, $SHIFT);
		$SCAN = parse_scan_new_app($row->KARYAWAN_ID, $TGL_MULAI, $TGL_SELESAI, $SHIFT);

		$TOTAL_DAY = isset($SCAN['total_day']) ? $SCAN['total_day'] : '';
		$TOTAL_HK = isset($SCAN['total_working_day']) ? $SCAN['total_working_day'] : '';
		$TOTAL_OFF = isset($SCAN['total_holiday']) ? $SCAN['total_holiday'] : '';
		$TOTAL_ATT = isset($SCAN['total_attendance']) ? $SCAN['total_attendance'] : '';
		$TOTAL_ABS = isset($SCAN['total_absent']) ? $SCAN['total_absent'] : '';
		$TOTAL_LATE = isset($SCAN['total_late']) ? $SCAN['total_late'] : '';
		$TOTAL_EARLY = isset($SCAN['total_early']) ? $SCAN['total_early'] : '';
		$TOTAL_SAKIT = isset($SCAN['total_sakit']) ? $SCAN['total_sakit'] : '';
		$TOTAL_IJIN = isset($SCAN['total_ijin']) ? $SCAN['total_ijin'] : '';
		$TOTAL_IJIN_LE = isset($SCAN['total_ijin_le']) ? $SCAN['total_ijin_le'] : '';
		$TOTAL_SKD = isset($SCAN['total_skd']) ? $SCAN['total_skd'] : '';
		$TOTAL_ALL_SAKIT = isset($SCAN['total_all_sakit']) ? $SCAN['total_all_sakit'] : '';
		$TOTAL_CI = isset($SCAN['total_ci']) ? $SCAN['total_ci'] : '';
		$TOTAL_CT = isset($SCAN['total_ct']) ? $SCAN['total_ct'] : '';
		$TOTAL_TO = isset($SCAN['total_to']) ? $SCAN['total_to'] : '';
		$TOTAL_TS = isset($SCAN['total_ts']) ? $SCAN['total_ts'] : '';
		$TOTAL_BM = isset($SCAN['total_bm']) ? $SCAN['total_bm'] : '';
		$TOTAL_R = isset($SCAN['total_r']) ? $SCAN['total_r'] : '';
		$TOTAL_BACKUP = isset($SCAN['total_backup']) ? $SCAN['total_backup'] : '';
		$TOTAL_CM = isset($SCAN['total_cm']) ? $SCAN['total_cm'] : '';
		$TOTAL_DINAS = isset($SCAN['total_dinas']) ? $SCAN['total_dinas'] : '';
		$TOTAL_UL = isset($SCAN['total_ul']) ? $SCAN['total_ul'] : '';
		$TOTAL_LEMBUR = isset($SCAN['total_lembur']) ? $SCAN['total_lembur'] : '';
		$TOTAL_SM = isset($SCAN['total_sm']) ? $SCAN['total_sm'] : '';

		if (count($RANGE) > 0) {
			foreach ($RANGE as $date) {
				$S = isset($SCAN[$date]) ? $SCAN[$date] : array();
				$_DAY = isset($S['day']) ? $S['day'] : '';
				$_OFF = isset($S['holiday']) ? $S['holiday'] : '';
				$_HK = isset($S['working_day']) ? $S['working_day'] : 0;
				$SCAN_IN = isset($S['scan_in']) ? $S['scan_in'] : '';
				$SCAN_OUT = isset($S['scan_out']) ? $S['scan_out'] : '';
				$_LATE = isset($S['late']) ? $S['late'] : '';
				$_EARLY = isset($S['early']) ? $S['early'] : '';
				$_ABS = isset($S['absent']) ? $S['absent'] : 0;
				$_SAKIT = isset($S['SAKIT']) ? $S['SAKIT'] : 0;
				$_IJIN = isset($S['IJIN']) ? $S['IJIN'] : 0;
				$_IJIN_LE = isset($S['IJIN_LE']) ? $S['IJIN_LE'] : 0;
				$_SKD = isset($S['SKD']) ? $S['SKD'] : 0;
				$_CI = isset($S['CI']) ? $S['CI'] : 0;
				$_CT = isset($S['CT']) ? $S['CT'] : 0;
				$_TO_IN = isset($S['TO_IN']) ? $S['TO_IN'] : 0;
				$_TO_OUT = isset($S['TO_OUT']) ? $S['TO_OUT'] : 0;
				$_TS = isset($S['TS']) ? $S['TS'] : 0;
				$_TS_IN = isset($S['TS_IN']) ? $S['TS_IN'] : 0;
				$_TS_OUT = isset($S['TS_OUT']) ? $S['TS_OUT'] : 0;
				$_R = isset($S['R']) ? $S['R'] : 0;
				$_BM = isset($S['BM']) ? $S['BM'] : 0;
				$_BACKUP = isset($S['BACKUP']) ? $S['BACKUP'] : 0;
				$_CM = isset($S['CM']) ? $S['CM'] : 0;
				$_DINAS = isset($S['DINAS']) ? $S['DINAS'] : 0;
				$_UL = isset($S['UL']) ? $S['UL'] : 0;
				$_SM = isset($S['SM']) ? $S['SM'] : 0;
				$_LEMBUR = isset($S['lembur']) ? $S['lembur'] : 0;
				$NOTE = isset($S['note']) ? $S['note'] : 0;

				$_MINUTE = isset($S['real_minute']) ? $S['real_minute'] : '';

				//if(in_array($_DAY,array('0','6'))) $bg = '#ffdfdf';
				/*
				$bg = 'transparent';
				$font_color = 'color:#ffffff';
				$bg = 'green';
				*/

				$bg = '#B0FFC4';

				$check_rule  = db_first(" SELECT R_9JAM FROM jabatan J LEFT JOIN karyawan K ON K.JABATAN_ID = J.JABATAN_ID WHERE K.KARYAWAN_ID='$row->KARYAWAN_ID' ");
				$NINE_OUR    = empty($check_rule->R_9JAM) ? 0 : $check_rule->R_9JAM;

				if ($_LATE > 0) {
					if ($NINE_OUR != 1) {
						$bg = '#FFB0B0';
					} else {
						if ($_MINUTE < 540) {
							$bg = '#FFB0B0';
						}
					}
				}

				if ($_EARLY > 0) {
					if ($NINE_OUR != 1) {
						$bg = '#FFB0B0';
					} else {
						if ($_MINUTE < 540) {
							$bg = '#FFB0B0';
						}
					}
				}

				/* 
				if ($_LATE > 0 && $_MINUTE < 540) {
					$bg = '#FFB0B0';
				}

				if ($_EARLY > 0 && $_MINUTE < 540) {
					$bg = '#FFB0B0';
				}
			 	*/

				$minute = '';

				if ($_MINUTE > 0) {
					$minute = $_MINUTE;
				}

				$t['TGL_' . date('Ymd', strtotime($date))] = '<div data-minute="' . $minute . '" style="color:#000000;background-color:' . $bg . ';">' . $SCAN_IN . '-' . $SCAN_OUT . '</div>';

				if ($_ABS == '1') {
					if ($SCAN_IN) {
						$t['TGL_' . date('Ymd', strtotime($date))] = '<div style="background-color:#888;color:#ffffff;text-align:center;">'  . $SCAN_IN . '</div>';
					} else if ($SCAN_OUT) {
						$t['TGL_' . date('Ymd', strtotime($date))] = '<div style="background-color:#888;color:#ffffff;text-align:center;">'  . $SCAN_OUT . '</div>';
					} else if (($SCAN_IN) and ($SCAN_OUT)) {
						$t['TGL_' . date('Ymd', strtotime($date))] = '<div style="background-color:#888;color:#ffffff;text-align:center;">'  . $SCAN_IN . '-' . $SCAN_OUT . '</div>';
					} else {
						$t['TGL_' . date('Ymd', strtotime($date))] = '<div style="background-color:black;color:#ffffff;text-align:center;">ABS</div>';
					}
				}
				if ($_OFF == '1') {
					$t['TGL_' . date('Ymd', strtotime($date))] = '<div style="color:red;text-align:center;">OFF</div>';
				}
				if ($_LEMBUR == '1') {
					$t['TGL_' . date('Ymd', strtotime($date))] = '<div style="color:' . get_option('F_LEMBUR') . ';background-color:' . get_option('C_LEMBUR') . ';" title="' . $NOTE . '" class="tip">' . $SCAN_IN . '-' . $SCAN_OUT . '</div>';
					if ($_OFF == '1') {
						$t['TGL_' . date('Ymd', strtotime($date))] = '<div style="color:' . get_option('F_LEMBUR') . ';background-color:' . get_option('C_LEMBUR') . ';" title="' . $NOTE . '" class="tip">LEMBUR</div>';
					}
				}

				if ($_SAKIT == '1') $t['TGL_' . date('Ymd', strtotime($date))] = '<div title="' . $NOTE . '" class="tip" style="background-color:' . get_option('C_SAKIT') . ';color:' . get_option('F_SAKIT') . ';text-align:center;font-weight:bold;">SAKIT</div>';
				if ($_IJIN == '1') $t['TGL_' . date('Ymd', strtotime($date))] = '<div title="' . $NOTE . '" class="tip" style="background-color:' . get_option('C_IJIN') . ';color:' . get_option('F_IJIN') . ';text-align:center;font-weight:bold;">IJIN</div>';
				if ($_SKD == '1') $t['TGL_' . date('Ymd', strtotime($date))] = '<div title="' . $NOTE . '" class="tip" style="background-color:' . get_option('C_SKD') . ';color:' . get_option('F_SKD') . ';text-align:center;font-weight:bold;">SKD</div>';
				if ($_CI == '1') $t['TGL_' . date('Ymd', strtotime($date))] = '<div title="' . $NOTE . '" class="tip" style="background-color:' . get_option('C_CI') . ';color:' . get_option('F_CI') . ';text-align:center;font-weight:bold;">CI</div>';
				if ($_CT == '1') $t['TGL_' . date('Ymd', strtotime($date))] = '<div title="' . $NOTE . '" class="tip" style="background-color:' . get_option('C_CT') . ';color:' . get_option('F_CT') . ';text-align:center;font-weight:bold;">CT</div>';
				if ($_TO_IN == '1') $t['TGL_' . date('Ymd', strtotime($date))] = '<div title="TUKAR OFF (in) : ' . $NOTE . '" class="tip" style="background-color:' . get_option('C_TO') . ';color:' . get_option('F_TO') . ';text-align:center;font-weight:bold;">' . $SCAN_IN . '-' . $SCAN_OUT . '</div>';
				if ($_TO_OUT == '1') $t['TGL_' . date('Ymd', strtotime($date))] = '<div title="TUKAR OFF (out) : ' . $NOTE . '" class="tip" style="background-color:' . get_option('C_TO') . ';color:' . get_option('F_TO') . ';text-align:center;font-weight:bold;">TO-OUT</div>';
				if ($_TS == '1') $t['TGL_' . date('Ymd', strtotime($date))] = '<div title="TUKAR SHIFT (Same-day) : ' . $NOTE . '" class="tip" style="background-color:' . get_option('C_TS') . ';color:' . get_option('F_TS') . ';text-align:center;font-weight:bold;">' . $SCAN_IN . '-' . $SCAN_OUT . '</div>';
				if ($_TS_IN == '1') $t['TGL_' . date('Ymd', strtotime($date))] = '<div title="TUKAR SHIFT (in) : ' . $NOTE . '" class="tip" style="background-color:' . get_option('C_TS') . ';color:' . get_option('F_TS') . ';text-align:center;font-weight:bold;">' . $SCAN_IN . '-' . $SCAN_OUT . '</div>';
				if ($_TS_OUT == '1') $t['TGL_' . date('Ymd', strtotime($date))] = '<div title="TUKAR SHIFT (out) : ' . $NOTE . '" class="tip" style="background-color:' . get_option('C_TS') . ';color:' . get_option('F_TS') . ';text-align:center;font-weight:bold;">TS-OUT</div>';
				if ($_R == '1') $t['TGL_' . date('Ymd', strtotime($date))] = '<div title="' . $NOTE . '" class="tip" style="background-color:' . get_option('C_R') . ';color:' . get_option('F_R') . ';text-align:center;font-weight:bold;">R</div>';
				if ($_BM == '1') $t['TGL_' . date('Ymd', strtotime($date))] = '<div title="' . $NOTE . '" class="tip" style="background-color:' . get_option('C_BM') . ';color:' . get_option('F_BM') . ';text-align:center;font-weight:bold;">BM</div>';
				if ($_CM == '1') $t['TGL_' . date('Ymd', strtotime($date))] = '<div title="' . $NOTE . '" class="tip" style="background-color:' . get_option('C_CM') . ';color:' . get_option('F_CM') . ';text-align:center;font-weight:bold;">CM</div>';
				if ($_DINAS == '1') $t['TGL_' . date('Ymd', strtotime($date))] = '<div title="' . $NOTE . '" class="tip" style="background-color:' . get_option('C_DINAS') . ';color:' . get_option('F_DINAS') . ';text-align:center;font-weight:bold;">DINAS</div>';
				if ($_UL == '1') $t['TGL_' . date('Ymd', strtotime($date))] = '<div title="' . $NOTE . '" class="tip" style="background-color:' . get_option('C_UL') . ';color:' . get_option('F_UL') . ';text-align:center;font-weight:bold;">UL</div>';
				if ($_IJIN_LE == '1') $t['TGL_' . date('Ymd', strtotime($date))] = '<div title="' . $NOTE . '" class="tip" style="background-color:' . get_option('C_IJIN') . ';color:' . get_option('F_IJIN') . ';text-align:center;font-weight:bold;">' . $SCAN_IN . '-' . $SCAN_OUT . '</div>';
				if ($_SM == '1') $t['TGL_' . date('Ymd', strtotime($date))] = '<div title="' . $NOTE . '" class="tip" style="background-color:' . get_option('C_SM') . ';color:' . get_option('F_SM') . ';text-align:center;font-weight:bold;">SM</div>';

				$SC = 'BACKUP';
				if (!empty($SCAN_IN) and !empty($SCAN_OUT)) {
					$SC = $SCAN_IN . '-' . $SCAN_OUT;
				}
				if ($_BACKUP == '1') $t['TGL_' . date('Ymd', strtotime($date))] = '<div title="' . $NOTE . '" class="tip" style="background-color:#ff0000;text-align:center;font-weight:bold;">' . $SC . '</div>';
			}
		}

		if (get_input('VIEW_MODE') == 'PAYROLL') {
			$JOIN_DATE = $row->TGL_MASUK;
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
			if ($JOIN_DAY >= 365 or $row->R_CUTI == '0') {
				$KUOTA_CUTI = 12;
			} else {
				$CUTI_BERJALAN = cuti_berjalan($JOIN_DATE);
				$KUOTA_CUTI = isset($CUTI_BERJALAN[$CURR_MONTH]) ? $CUTI_BERJALAN[$CURR_MONTH] : 0;
			}

			$TOTAL_CT = isset($SCAN['total_ct']) ? $SCAN['total_ct'] : 0;
			$TOTAL_ABS = isset($SCAN['total_absent']) ? $SCAN['total_absent'] : 0;
			$TOTAL_SAKIT = isset($SCAN['total_sakit']) ? $SCAN['total_sakit'] : 0;
			$TOTAL_IJIN = isset($SCAN['total_ijin']) ? $SCAN['total_ijin'] : 0;
			$TOTAL_NSKD = $TOTAL_SAKIT + $TOTAL_IJIN;
			$CUTI_TERPAKAI = 0;

			$total_cuti = db_first("
			SELECT SUM(CUTI) as TOTAL_CUTI
			FROM cuti_tahunan C
			WHERE
				C.KARYAWAN_ID='$row->KARYAWAN_ID' AND
				C.VALID_UNTIL='$VALID_UNTIL' AND
				C.PERIODE_ID < $PERIODE_ID AND
				C.PERIODE_ID <> '$PERIODE_ID'
			GROUP BY KARYAWAN_ID
		");

			/*echo "SELECT SUM(CUTI) as TOTAL_CUTI
			FROM cuti_tahunan C
			WHERE
				C.KARYAWAN_ID='$row->KARYAWAN_ID' AND
				C.VALID_UNTIL='$VALID_UNTIL' AND
				C.PERIODE_ID < $PERIODE_ID AND
				C.PERIODE_ID <> '$PERIODE_ID'
			GROUP BY KARYAWAN_ID"; die;*/

			$CUTI_PERIODE_SEBELUMNYA = isset($total_cuti->TOTAL_CUTI) ? $total_cuti->TOTAL_CUTI : 0;
			$SISA_CUTI = $SISA_CUTI_ORIGINAL = $KUOTA_CUTI - $CUTI_PERIODE_SEBELUMNYA;

			if ($row->R_POT_ABSEN_CUTI == '1') {
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

			if ($row->R_POT_NSKD_CUTI == '1') {
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

			$CUTI_TERPAKAI = $CUTI_TERPAKAI + $TOTAL_CT;
			$SALDO_CUTI = $KUOTA_CUTI - ($CUTI_PERIODE_SEBELUMNYA + $CUTI_TERPAKAI);
		}

		$t['TGL_MASUK'] = tgl($row->TGL_MASUK);
		$t['VALID_UNTIL'] = tgl($VALID_UNTIL);
		$t['KUOTA_CUTI'] = $KUOTA_CUTI;
		$t['CUTI_PERIODE_SEBELUMNYA'] = $CUTI_PERIODE_SEBELUMNYA;
		$t['CUTI_PERIODE_INI'] = $CUTI_TERPAKAI;
		$t['SISA_CUTI'] = $SALDO_CUTI;

		$t['TOTAL_DAY'] = $TOTAL_DAY;
		$t['TOTAL_HK'] = $TOTAL_HK;
		$t['TOTAL_ATT'] = $TOTAL_ATT;
		$t['TOTAL_ABS'] = $TOTAL_ABS;
		$t['TOTAL_LATE'] = $TOTAL_LATE;
		$t['TOTAL_EARLY'] = $TOTAL_EARLY;
		$t['TOTAL_SAKIT'] = $TOTAL_SAKIT;
		$t['TOTAL_IJIN'] = $TOTAL_IJIN;
		$t['TOTAL_SKD'] = $TOTAL_SKD;
		$t['TOTAL_ALL_SAKIT'] = $TOTAL_ALL_SAKIT;
		$t['TOTAL_CI'] = $TOTAL_CI;
		$t['TOTAL_CT'] = $TOTAL_CT;
		$t['TOTAL_LEMBUR'] = $TOTAL_LEMBUR;
		$t['TOTAL_OFF'] = $TOTAL_OFF;
		$t['TOTAL_TO'] = $TOTAL_TO;
		$t['TOTAL_TS'] = $TOTAL_TS;
		$t['TOTAL_R'] = $TOTAL_R;
		$t['TOTAL_BM'] = $TOTAL_BM;
		$t['TOTAL_BACKUP'] = $TOTAL_BACKUP;
		$t['TOTAL_CM'] = $TOTAL_CM;
		$t['TOTAL_DINAS'] = $TOTAL_DINAS;
		$t['TOTAL_UL'] = $TOTAL_UL;
		$t['TOTAL_SM'] = $TOTAL_SM;

		/*
	$t['TOTAL_HK'] = '<div style="background-color:#ffffc7;font-weight:bold;">'.$TOTAL_HK.'</div>';
	$t['TOTAL_ATT'] = '<div style="background-color:#ffffc7;font-weight:bold;">'.$TOTAL_ATT.'</div>';
	$t['TOTAL_ABS'] = '<div style="background-color:#ffe9e9;font-weight:bold;">'.$TOTAL_ABS.'</div>';
	$t['TOTAL_LATE'] = '<div style="background-color:#ffe9e9;font-weight:bold;">'.$TOTAL_LATE.'</div>';
	$t['TOTAL_EARLY'] = '<div style="background-color:#ffe9e9;font-weight:bold;">'.$TOTAL_EARLY.'</div>';
	$t['TOTAL_SAKIT'] = '<div style="background-color:#ffffc7;font-weight:bold;">'.$TOTAL_SAKIT.'</div>';
	$t['TOTAL_IJIN'] = '<div style="background-color:#ffffc7;font-weight:bold;">'.$TOTAL_IJIN.'</div>';
	$t['TOTAL_SKD'] = '<div style="background-color:#d6ffd6;font-weight:bold;">'.$TOTAL_SKD.'</div>';
	$t['TOTAL_CI'] = '<div style="background-color:#e4bcff;font-weight:bold;">'.$TOTAL_CI.'</div>';
	$t['TOTAL_CT'] = '<div style="background-color:#e4bcff;font-weight:bold;">'.$TOTAL_CT.'</div>';
	$t['TOTAL_LEMBUR'] = '<div style="background-color:#ffff4f;font-weight:bold;">'.$TOTAL_LEMBUR.'</div>';
	$t['TOTAL_OFF'] = '<div style="background-color:#f2f2f2;font-weight:bold;">'.$TOTAL_OFF.'</div>';;
	$t['TOTAL_TO'] = '<div style="background-color:#ffffc7;font-weight:bold;">'.$TOTAL_TO.'</div>';;
	$t['TOTAL_TS'] = '<div style="background-color:#ffffc7;font-weight:bold;">'.$TOTAL_TS.'</div>';;
	$t['TOTAL_R'] = '<div style="background-color:#ffffc7;font-weight:bold;">'.$TOTAL_R.'</div>';;
	$t['TOTAL_BM'] = '<div style="background-color:#ffffc7;font-weight:bold;">'.$TOTAL_BM.'</div>';;
	*/
		$rows[$key] = (object) array_merge((array) $row, $t);
		$key++;
	}
}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);
