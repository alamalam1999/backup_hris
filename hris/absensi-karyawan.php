<?php

include 'app-load.php';

is_login('absensi-karyawan.view');

if (isset($_GET['generate'])) {
  is_login('absensi-karyawan.generate');

  $PERIODE_ID = get_input('PERIODE_ID');
  $PROJECT_ID = get_input('PROJECT_ID');

  $PERIODE = db_first(" SELECT * FROM periode WHERE PERIODE_ID='$PERIODE_ID' ");

  if (!isset($PERIODE->PERIODE_ID)) {
    header('location: absensi-karyawan.php');
    exit;
  }

  if ($PERIODE->STATUS_PERIODE == 'CLOSED') {
    header('location: absensi-karyawan.php?m=closed');
    exit;
  }

  $TAHUN = $PERIODE->TAHUN;
  $BULAN = $PERIODE->BULAN;
  $TGL_MULAI = $PERIODE->TANGGAL_MULAI;
  $TGL_SELESAI = $PERIODE->TANGGAL_SELESAI;

  /*
  $TGL_SEKARANG = date('Y-m-d');
  if ($TGL_SEKARANG < $TGL_SELESAI && $TGL_SEKARANG > $TGL_MULAI) {
    $TGL_SELESAI = $TGL_SEKARANG;
  }
  */

  $rs = db_fetch(" SELECT KARYAWAN_ID 
		FROM karyawan 
		WHERE PROJECT_ID = '$PROJECT_ID' AND ST_KERJA = 'AKTIF' 
	");

  $KARYAWAN_ID = array();
  if (count($rs) > 0) {
    foreach ($rs as $row) {
      $KARYAWAN_ID[] = $row->KARYAWAN_ID;
    }
  }

  if (count($KARYAWAN_ID) > 0) {
    foreach ($KARYAWAN_ID as $key => $id) {
      $rs = db_fetch(" SELECT *, K.SHIFT_CODE
        FROM shift_karyawan K
        LEFT JOIN shift S ON S.SHIFT_CODE = K.SHIFT_CODE
        WHERE K.PROJECT_ID='$PROJECT_ID' AND KARYAWAN_ID IN (" . implode(',', $KARYAWAN_ID) . ") AND (DATE >= '$TGL_MULAI' AND DATE <= '$TGL_SELESAI')
      ");

      $SHIFT = array();
      if (count($rs) > 0) {
        foreach ($rs as $row) {
          $SHIFT[$id][$row->DATE] = $row;
        }
      }

      $SCAN[] = parse_scan_new_app($id, $TGL_MULAI, $TGL_SELESAI, $SHIFT);
    }

    /*
    *** 
    Delete if row absent data (tabel_absen) existing in same date and 
    from_tbl = '2' (from fingerprint absent machine) and 
    from_tbl = '3' (from exception)
    */

    db_execute(" DELETE FROM tabel_absen WHERE (FROM_TBL = 2 OR FROM_TBL = 3) AND DATE(TGL_JADWAL) BETWEEN DATE('$TGL_MULAI') AND DATE('$TGL_SELESAI') ");

    if (count($SCAN) > 0) {
      foreach ($SCAN as $values) {
        foreach ($values as $key => $value) {
          if (date($key) <= date('Y-m-d')) {
            $ID_LOG_MESIN_IN = $value['id_log_mesin_in'];
            $ID_LOG_MESIN_OUT = $value['id_log_mesin_out'];
            $KARYAWAN_ID = $PIN = $value['karyawan_id'];
            $FROM_TBL = 2;
            $TGL_JADWAL = $key;
            $TGL_ABSEN_IN = $value['scan_in_date'];
            $TGL_ABSEN_OUT = $value['scan_out_date'];
            $JENIS_TIDAK_ABSEN = 0;
            $JENIS_IN = 1;
            $JENIS_OUT = 2;
            $NOTE = $value['note'];
            $APPROVED_STATUS = 1;
            $APPROVED_DATE = date('Y-m-d H:i:s');
            $LATITUDE = $value['latitude'];
            $LONGITUDE = $value['longitude'];

            $CU = current_user();
            $USER_ID = $CU->USER_ID;

            if ($value['attendance'] == 1) {
              $STATUS_ABSEN = 1;
            } else {
              $STATUS_ABSEN = 0;
            }

            if ($value['late'] != '') {
              $LATE = 1;
            } else {
              $LATE = 0;
            }

            if ($value['early'] != '') {
              $EARLY = 1;
            } else {
              $EARLY = 0;
            }

            // ONLINE
            // online absent in (insert to tabel_absen)
            if ($value['scan_in'] != '' && $value['online'] == 1) {
              $FROM_TBL = 1;
              $JENIS_ABSEN = 1;
              $JENIS_EKSEPSI = 'ONLINE';
              $LATITUDE_OL_IN = $value['latitude_ol_in'];
              $LONGITUDE_OL_IN = $value['longitude_ol_in'];
              $FOTO_OL_IN = $value['foto_ol_in'];

              // delete from duplicate row from online absent
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND FROM_TBL = 1 AND JENIS = 1 ");

              db_execute(" INSERT INTO tabel_absen 
              (`ID_KARYAWAN`, `FROM_TBL`, `PIN`, `TGL_JADWAL`,`TGL_ABSEN`, `JENIS`, `TERLAMBAT`, `STATUS`, `JENIS_EKSEPSI`, `NOTE`, `FOTO`, `LATITUDE`, `LONGITUDE`, `APPROVE`, `APPROVE_DATE`, `USER_ID`) 
              VALUES 
              ('$KARYAWAN_ID', '$FROM_TBL', '$KARYAWAN_ID', '$TGL_JADWAL', '$TGL_ABSEN_IN', '$JENIS_ABSEN', '$LATE', '$STATUS_ABSEN', '$JENIS_EKSEPSI', '$NOTE', '$LATITUDE_OL_IN', '$LONGITUDE_OL_IN', '$FOTO_OL_IN', '$APPROVED_STATUS', '$APPROVED_DATE', '$USER_ID');
            ");
            }

            // online absent out (insert to tabel_absen)
            if ($value['scan_out'] != '' && $value['online'] == 1) {
              $FROM_TBL = 1;
              $JENIS_ABSEN = 2;
              $JENIS_EKSEPSI = 'ONLINE';
              $LATITUDE_OL_OUT = $value['latitude_ol_out'];
              $LONGITUDE_OL_OUT = $value['longitude_ol_out'];
              $FOTO_OL_OUT = $value['foto_ol_out'];

              // delete from duplicate row from online absent
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND FROM_TBL = 1 AND JENIS = 2 ");

              db_execute(" INSERT INTO tabel_absen 
              (`ID_KARYAWAN`, `FROM_TBL`, `PIN`, `TGL_JADWAL`, `TGL_ABSEN`, `JENIS`, `TERLAMBAT`, `STATUS`, `JENIS_EKSEPSI`, `NOTE`, `FOTO`, `LATITUDE`, `LONGITUDE`,`APPROVE`, `APPROVE_DATE`, `USER_ID`) 
              VALUES 
              ('$KARYAWAN_ID', '$FROM_TBL', '$KARYAWAN_ID', '$TGL_JADWAL', '$TGL_ABSEN_OUT', '$JENIS_ABSEN', '$EARLY', '$STATUS_ABSEN', '$JENIS_EKSEPSI', '$NOTE', '$LATITUDE_OL_OUT', '$LONGITUDE_OL_OUT', '$FOTO_OL_OUT', '$APPROVED_STATUS', '$APPROVED_DATE', '$USER_ID');
            ");
            }
            // END OF ONLINE

            if ($value['scan_in'] != '' && $value['online'] != 1) {
              // delete from duplicate row from online absent
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND FROM_TBL = 1 ");

              // absent in (insert to tabel_absen)
              db_execute(" INSERT INTO tabel_absen 
              (`ID_LOG_MESIN`, `ID_KARYAWAN`, `FROM_TBL`, `PIN`, `TGL_JADWAL`, `TGL_ABSEN`, `JENIS`, `TERLAMBAT`, `STATUS`, `NOTE`, `FOTO`, `LATITUDE`, `LONGITUDE`, `APPROVE`, `APPROVE_DATE`, `USER_ID`) 
              VALUES 
              ('$ID_LOG_MESIN_IN', '$KARYAWAN_ID', '$FROM_TBL', '$KARYAWAN_ID', '$TGL_JADWAL', '$TGL_ABSEN_IN', '$JENIS_IN', '$LATE', '$STATUS_ABSEN', '$NOTE', '', '$LATITUDE', '$LONGITUDE', '$APPROVED_STATUS', '$APPROVED_DATE', '$USER_ID');
            ");
            }

            if ($value['scan_out'] != '' && $value['online'] != 1) {
              // delete from duplicate row from online absent
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND FROM_TBL = 1 ");

              // absent out (insert to tabel_absen)
              db_execute(" INSERT INTO tabel_absen 
              (`ID_LOG_MESIN`, `ID_KARYAWAN`, `FROM_TBL`, `PIN`, `TGL_JADWAL`, `TGL_ABSEN`, `JENIS`, `TERLAMBAT`, `STATUS`, `NOTE`, `FOTO`, `LATITUDE`, `LONGITUDE`, `APPROVE`, `APPROVE_DATE`, `USER_ID`) 
              VALUES 
              ('$ID_LOG_MESIN_OUT', '$KARYAWAN_ID', '$FROM_TBL', '$KARYAWAN_ID', '$TGL_JADWAL', '$TGL_ABSEN_OUT', '$JENIS_OUT', '$EARLY', '$STATUS_ABSEN', '$NOTE', '', '$LATITUDE', '$LONGITUDE', '$APPROVED_STATUS', '$APPROVED_DATE', '$USER_ID');
            ");
            }

            if ($value['scan_in'] == '' && $value['scan_out'] == '' && $value['working_day'] == 1 && $value['online'] != 1) {
              // delete from duplicate row from online absent
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND FROM_TBL = 1 ");

              // not absent but insert in one row (insert to tabel_absen)
              db_execute(" INSERT INTO tabel_absen 
              (`ID_KARYAWAN`, `FROM_TBL`, `PIN`, `TGL_JADWAL`, `STATUS`, `NOTE`, `FOTO`, `LATITUDE`, `LONGITUDE`, `APPROVE`, `APPROVE_DATE`, `USER_ID`) 
              VALUES 
              ('$KARYAWAN_ID', '$FROM_TBL', '$KARYAWAN_ID', '$TGL_JADWAL', '$STATUS_ABSEN', '$NOTE', '', '$LATITUDE', '$LONGITUDE', '$APPROVED_STATUS', '$APPROVED_DATE', '$USER_ID');
            ");
            }

            // EKSEPSI

            // eksepsi sakit (insert to tabel_absen)
            if ($value['SAKIT'] == 1) {
              $FROM_TBL = 3;
              $JENIS_ABSEN = 0;
              $JENIS_EKSEPSI = 'SAKIT';

              // delete from duplicate row
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND STATUS = 0 AND (FROM_TBL = 2 OR FROM_TBL=3) ");

              // delete from duplicate row from online absent
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND FROM_TBL = 1 ");

              db_execute(" INSERT INTO tabel_absen 
              (`ID_KARYAWAN`, `FROM_TBL`, `PIN`, `TGL_JADWAL`, `JENIS`, `STATUS`, `JENIS_EKSEPSI`, `NOTE`, `APPROVE`, `APPROVE_DATE`, `USER_ID`) 
              VALUES 
              ('$KARYAWAN_ID', '$FROM_TBL', '$KARYAWAN_ID', '$TGL_JADWAL', '$JENIS_ABSEN', '$STATUS_ABSEN', '$JENIS_EKSEPSI', '$NOTE', '$APPROVED_STATUS', '$APPROVED_DATE', '$USER_ID');
            ");
            }

            // eksepsi ijin (insert to tabel_absen)
            if ($value['IJIN'] == 1) {
              $FROM_TBL = 3;
              $JENIS_ABSEN = 0;
              $JENIS_EKSEPSI = 'IJIN';

              // delete from duplicate row
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND STATUS = 0 AND (FROM_TBL = 2 OR FROM_TBL=3) ");

              // delete from duplicate row from online absent
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND FROM_TBL = 1 ");

              db_execute(" INSERT INTO tabel_absen 
              (`ID_KARYAWAN`, `FROM_TBL`, `PIN`, `TGL_JADWAL`, `JENIS`, `STATUS`, `JENIS_EKSEPSI`, `NOTE`, `APPROVE`, `APPROVE_DATE`, `USER_ID`) 
              VALUES 
              ('$KARYAWAN_ID', '$FROM_TBL', '$KARYAWAN_ID', '$TGL_JADWAL', '$JENIS_ABSEN', '$STATUS_ABSEN', '$JENIS_EKSEPSI', '$NOTE', '$APPROVED_STATUS', '$APPROVED_DATE', '$USER_ID');
            ");
            }

            // eksepsi skd/surat keterangan dokter (insert to tabel_absen)
            if ($value['SKD'] == 1) {
              $FROM_TBL = 3;
              $JENIS_ABSEN = 0;
              $JENIS_EKSEPSI = 'SKD';

              // delete from duplicate row
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND STATUS = 0 AND (FROM_TBL = 2 OR FROM_TBL=3) ");

              // delete from duplicate row from online absent
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND FROM_TBL = 1 ");

              db_execute(" INSERT INTO tabel_absen 
              (`ID_KARYAWAN`, `FROM_TBL`, `PIN`, `TGL_JADWAL`, `JENIS`, `STATUS`, `JENIS_EKSEPSI`, `NOTE`, `APPROVE`, `APPROVE_DATE`, `USER_ID`) 
              VALUES 
              ('$KARYAWAN_ID', '$FROM_TBL', '$KARYAWAN_ID', '$TGL_JADWAL', '$JENIS_ABSEN', '$STATUS_ABSEN', '$JENIS_EKSEPSI', '$NOTE', '$APPROVED_STATUS', '$APPROVED_DATE', '$USER_ID');
            ");
            }

            // eksepsi cuti tahunan (insert to tabel_absen)
            if ($value['CT'] == 1) {
              $FROM_TBL = 3;
              $JENIS_ABSEN = 0;
              $JENIS_EKSEPSI = 'CT';

              // delete from duplicate row
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND STATUS = 0 AND (FROM_TBL = 2 OR FROM_TBL=3) ");

              // delete from duplicate row from online absent
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND FROM_TBL = 1 ");

              db_execute(" INSERT INTO tabel_absen 
              (`ID_KARYAWAN`, `FROM_TBL`, `PIN`, `TGL_JADWAL`, `JENIS`, `STATUS`, `JENIS_EKSEPSI`, `NOTE`, `APPROVE`, `APPROVE_DATE`, `USER_ID`) 
              VALUES 
              ('$KARYAWAN_ID', '$FROM_TBL', '$KARYAWAN_ID', '$TGL_JADWAL', '$JENIS_ABSEN', '$STATUS_ABSEN', '$JENIS_EKSEPSI', '$NOTE', '$APPROVED_STATUS', '$APPROVED_DATE', '$USER_ID');
            ");
            }

            // eksepsi belum masuk (insert to tabel_absen)
            if ($value['BM'] == 1) {
              $FROM_TBL = 3;
              $JENIS_ABSEN = 0;
              $JENIS_EKSEPSI = 'BM';

              // delete from duplicate row
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND STATUS = 0 AND (FROM_TBL = 2 OR FROM_TBL=3) ");

              // delete from duplicate row from online absent
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND FROM_TBL = 1 ");

              db_execute(" INSERT INTO tabel_absen 
              (`ID_KARYAWAN`, `FROM_TBL`, `PIN`, `TGL_JADWAL`, `JENIS`, `STATUS`, `JENIS_EKSEPSI`, `NOTE`, `APPROVE`, `APPROVE_DATE`, `USER_ID`) 
              VALUES 
              ('$KARYAWAN_ID', '$FROM_TBL', '$KARYAWAN_ID', '$TGL_JADWAL', '$JENIS_ABSEN', '$STATUS_ABSEN', '$JENIS_EKSEPSI', '$NOTE', '$APPROVED_STATUS', '$APPROVED_DATE', '$USER_ID');
            ");
            }

            // eksepsi cuti istimewa (insert to tabel_absen)
            if ($value['CI'] == 1) {
              $FROM_TBL = 3;
              $JENIS_ABSEN = 0;
              $JENIS_EKSEPSI = 'CI';

              // delete from duplicate row from online absent
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND FROM_TBL = 1 ");

              // delete from duplicate row
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND STATUS = 0 AND (FROM_TBL = 2 OR FROM_TBL=3) ");

              db_execute(" INSERT INTO tabel_absen 
              (`ID_KARYAWAN`, `FROM_TBL`, `PIN`, `TGL_JADWAL`, `JENIS`, `STATUS`, `JENIS_EKSEPSI`, `NOTE`, `APPROVE`, `APPROVE_DATE`, `USER_ID`) 
              VALUES 
              ('$KARYAWAN_ID', '$FROM_TBL', '$KARYAWAN_ID', '$TGL_JADWAL', '$JENIS_ABSEN', '$STATUS_ABSEN', '$JENIS_EKSEPSI', '$NOTE', '$APPROVED_STATUS', '$APPROVED_DATE', '$USER_ID');
            ");
            }

            // eksepsi unpaid leave (insert to tabel_absen)
            if ($value['UL'] == 1) {
              $FROM_TBL = 3;
              $JENIS_ABSEN = 0;
              $JENIS_EKSEPSI = 'UL';

              // delete from duplicate row from online absent
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND FROM_TBL = 1 ");

              // delete from duplicate row
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND STATUS = 0 AND (FROM_TBL = 2 OR FROM_TBL=3) ");

              db_execute(" INSERT INTO tabel_absen 
              (`ID_KARYAWAN`, `FROM_TBL`, `PIN`, `TGL_JADWAL`, `JENIS`, `STATUS`, `JENIS_EKSEPSI`, `NOTE`, `APPROVE`, `APPROVE_DATE`, `USER_ID`) 
              VALUES 
              ('$KARYAWAN_ID', '$FROM_TBL', '$KARYAWAN_ID', '$TGL_JADWAL', '$JENIS_ABSEN', '$STATUS_ABSEN', '$JENIS_EKSEPSI', '$NOTE', '$APPROVED_STATUS', '$APPROVED_DATE', '$USER_ID');
            ");
            }

            // eksepsi ijin late early (insert to tabel_absen)
            if ($value['IJIN_LE'] == 1) {
              $FROM_TBL = 3;
              $JENIS_EKSEPSI = 'IJIN_LE';
              $STATUS_ABSEN = 1;

              // delete from duplicate row from online absent
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND FROM_TBL = 1 ");

              // delete from duplicate row from online absent
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND FROM_TBL = 1 ");

              db_execute(" UPDATE tabel_absen SET
              STATUS = '$STATUS_ABSEN', JENIS_EKSEPSI = '$JENIS_EKSEPSI', FROM_TBL = '$FROM_TBL'  
              WHERE TGL_JADWAL = DATE('$key')               
            ");
            }

            // eksepsi dinas (insert to tabel_absen)
            if ($value['DINAS'] == 1) {
              $FROM_TBL = 3;
              $JENIS_ABSEN = 0;
              $JENIS_EKSEPSI = 'DINAS';
              $STATUS_ABSEN = 1;

              // delete from duplicate row from online absent
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND FROM_TBL = 1 ");

              // delete from duplicate row
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND STATUS = 0 AND (FROM_TBL = 2 OR FROM_TBL=3) ");

              db_execute(" INSERT INTO tabel_absen 
              (`ID_KARYAWAN`, `FROM_TBL`, `PIN`, `TGL_JADWAL`, `JENIS`, `STATUS`, `JENIS_EKSEPSI`, `NOTE`, `APPROVE`, `APPROVE_DATE`, `USER_ID`) 
              VALUES 
              ('$KARYAWAN_ID', '$FROM_TBL', '$KARYAWAN_ID', '$TGL_JADWAL', '$JENIS_ABSEN', '$STATUS_ABSEN', '$JENIS_EKSEPSI', '$NOTE', '$APPROVED_STATUS', '$APPROVED_DATE', '$USER_ID');
            ");
            }

            // eksepsi scan manual (insert to tabel_absen)
            if ($value['SM'] == 1) {
              $FROM_TBL = 3;
              $JENIS_ABSEN = 0;
              $JENIS_EKSEPSI = 'SM';
              $STATUS_ABSEN = 1;

              // delete from duplicate row from online absent
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND FROM_TBL = 1 ");

              // delete from duplicate row
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND STATUS = 0 AND (FROM_TBL = 2 OR FROM_TBL=3) ");

              db_execute(" INSERT INTO tabel_absen 
              (`ID_KARYAWAN`, `FROM_TBL`, `PIN`, `TGL_JADWAL`, `JENIS`, `STATUS`, `JENIS_EKSEPSI`, `NOTE`, `APPROVE`, `APPROVE_DATE`, `USER_ID`) 
              VALUES 
              ('$KARYAWAN_ID', '$FROM_TBL', '$KARYAWAN_ID', '$TGL_JADWAL', '$JENIS_ABSEN', '$STATUS_ABSEN', '$JENIS_EKSEPSI', '$NOTE', '$APPROVED_STATUS', '$APPROVED_DATE', '$USER_ID');
            ");
            }


            // eksepsi lembur (insert to tabel_absen)
            if ($value['lembur'] == 1) {
              $FROM_TBL = 3;
              $JENIS_ABSEN = 0;
              $JENIS_EKSEPSI = 'LEMBUR';
              $STATUS_ABSEN = 1;

              // delete from duplicate row
              db_execute(" DELETE FROM tabel_absen WHERE ID_KARYAWAN = '$KARYAWAN_ID' AND TGL_JADWAL = DATE('$key') AND (FROM_TBL = 2 OR FROM_TBL=3) ");

              db_execute(" INSERT INTO tabel_absen 
              (`ID_KARYAWAN`, `FROM_TBL`, `PIN`, `TGL_JADWAL`, `JENIS`, `STATUS`, `JENIS_EKSEPSI`, `NOTE`, `APPROVE`, `APPROVE_DATE`, `USER_ID`) 
              VALUES 
              ('$KARYAWAN_ID', '$FROM_TBL', '$KARYAWAN_ID', '$TGL_JADWAL', '$JENIS_ABSEN', '$STATUS_ABSEN', '$JENIS_EKSEPSI', '$NOTE', '$APPROVED_STATUS', '$APPROVED_DATE', '$USER_ID');
            ");
            }
          }
        }
      }
    }
  }

  header('location: absensi-karyawan.php?m=1');
  exit;
}

if (isset($_GET['generate_demo'])) {
  is_login('absensi-karyawan.generate');

  $PERIODE_ID = get_input('PERIODE_ID');
  $PROJECT_ID = get_input('PROJECT_ID');

  $PERIODE = db_first(" SELECT * FROM periode WHERE PERIODE_ID='$PERIODE_ID' ");

  if (!isset($PERIODE->PERIODE_ID)) {
    header('location: absensi-karyawan.php');
    exit;
  }

  if ($PERIODE->STATUS_PERIODE == 'CLOSED') {
    header('location: absensi-karyawan.php?m=closed');
    exit;
  }

  $TAHUN = $PERIODE->TAHUN;
  $BULAN = $PERIODE->BULAN;
  $TGL_MULAI = $PERIODE->TANGGAL_MULAI;
  $TGL_SELESAI = $PERIODE->TANGGAL_SELESAI;

  /*
  $TGL_SEKARANG = date('Y-m-d');
  if ($TGL_SEKARANG < $TGL_SELESAI && $TGL_SEKARANG > $TGL_MULAI) {
    $TGL_SELESAI = $TGL_SEKARANG;
  }
  */

  $rs = db_fetch(" SELECT KARYAWAN_ID 
		FROM karyawan 
		WHERE PROJECT_ID = '$PROJECT_ID' AND ST_KERJA = 'AKTIF' 
	");

  $KARYAWAN_ID = array();
  if (count($rs) > 0) {
    foreach ($rs as $row) {
      $KARYAWAN_ID[] = $row->KARYAWAN_ID;
    }
  }

  if (count($KARYAWAN_ID) > 0) {
    foreach ($KARYAWAN_ID as $key => $id) {
      $rs = db_fetch(" SELECT *, K.SHIFT_CODE
        FROM shift_karyawan K
        LEFT JOIN shift S ON S.SHIFT_CODE = K.SHIFT_CODE
        WHERE K.PROJECT_ID='$PROJECT_ID' AND KARYAWAN_ID IN (" . implode(',', $KARYAWAN_ID) . ") AND (DATE >= '$TGL_MULAI' AND DATE <= '$TGL_SELESAI')
      ");

      $SHIFT = array();
      if (count($rs) > 0) {
        foreach ($rs as $row) {
          $SHIFT[$id][$row->DATE] = $row;
        }
      }

      $SCAN[] = parse_scan_new_app($id, $TGL_MULAI, $TGL_SELESAI, $SHIFT);
    }

    print_r($SCAN);
    die();
  }

  header('location: absensi-karyawan.php?m=1');
  exit;
}

$JS[] = 'static/tipsy/jquery.tipsy.js';
$JS[] = 'static/sweetalert/sweetalert2.all.min.js';

$CSS[] = 'static/tipsy/tipsy.css';
$CSS[] = 'static/sweetalert/sweetalert2.min.css';
include 'header.php';

if (get_input('m') == '1') {
  $SUCCESS = 'Absensi berhasil digenerate';
}
if (get_input('m') == 'closed') {
  $ERROR[] = 'Tidak dapat melakukan generate Absensi<br>Periode sudah di tutup';
}
include 'msg.php';
?>

<section class="container-fluid">
  <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="GET">
    <div class="row" style="margin:10px 0;">
      <div class="col-sm-2">
        <div class="dropdown">
          <button class="btn btn-sm btn-default dropdown-toggle" type="button" id="dd1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="width:100%;">
            <i class="fa fa-cog"></i>&nbsp;&nbsp;Action <span class="caret"></span>
          </button>
          <ul class="dropdown-menu" aria-labelledby="dd1">
            <li><a href="javascript:void(0)" id="btn-generate"><i class="fa fa-cog"></i>&nbsp;&nbsp;Generate</a></li>
            <!-- <li><a href="javascript:void(0)" id="btn-generate-demo" style="color: #ddd;"><i class="fa fa-cog"></i>&nbsp;&nbsp;Generate Demo</a></li> -->
            <li><a href="absensi-import.php"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Import</a></li>
          </ul>
        </div>
      </div>
      <div class="col-sm-2">
        <?php echo dropdown('PERIODE_ID', dropdown_option('periode', 'PERIODE_ID', 'PERIODE', 'ORDER BY PERIODE_ID DESC'), get_search('ABSENSI', 'PERIODE_ID'), ' id="PERIODE_ID" class="form-control input-sm" ') ?>
      </div>
      <div class="col-sm-2">
        <?php echo dropdown('PROJECT_ID', project_option_filter(0), get_search('ABSENSI', 'PROJECT_ID'), ' id="PROJECT_ID" class="form-control input-sm" ') ?>
      </div>
      <!-- <div class="col-sm-2">
        <? php # echo dropdown('VIEW', array('ABSENSI' => 'Cutoff Absensi', 'PAYROLL' => 'Cutoff Payroll'), get_search('ABSENSI', 'VIEW_MODE'), ' id="VIEW_MODE" class="form-control input-sm" ') 
        ?>
      </div> -->
      <div class="col-sm-2">
        <input type="text" id="NAMA" value="<?php echo get_search('ABSENSI', 'NAMA') ?>" class="form-control input-sm" placeholder="Nama....">
      </div>
      <div class="col-sm-2">
        <h1 style="margin:0;text-align:right;">Absensi</h1>
      </div>
    </div>
  </form>

  <section class="content">
    <div id="t-responsive" class="table-responsive">
      <table id="t" style="min-height:200px;"></table>
    </div>
  </section>
</section>

<script>
  $(document).ready(function() {
    $('#btn-generate').click(function() {
      /*
      window.location = 'absensi-karyawan.php?generate=1&PERIODE_ID=' + $('#PERIODE_ID').val() + '&PROJECT_ID=' + $('#PROJECT_ID').val();
      return false;
      */
      Swal.fire({
        title: 'Apakah Anda sudah melakukan Import Log, pada periode ini?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Sudah',
        denyButtonText: 'Belum',
      }).then((result) => {
        if (result.isConfirmed) {
          window.location = 'absensi-karyawan.php?generate=1&PERIODE_ID=' + $('#PERIODE_ID').val() + '&PROJECT_ID=' + $('#PROJECT_ID').val();
        } else if (result.isDenied) {
          window.location = 'import-log.php';
        }
      })
    });

    $('#btn-generate-demo').click(function() {
      window.location = 'absensi-karyawan.php?generate_demo=1&PERIODE_ID=' + $('#PERIODE_ID').val() + '&PROJECT_ID=' + $('#PROJECT_ID').val();
      return false;
    });

    make_dg();
    $('#PERIODE_ID, #PROJECT_ID, #VIEW_MODE').change(function() {
      make_dg();
      return false;
    });
    $('#NAMA').keypress(function(e) {
      if (e.which == 13) {
        doSearch();
        e.preventDefault();
      }
    });
    datagrid();
  });

  function make_dg() {
    $.ajax({
      url: 'absensi-karyawan-header-json.php',
      data: {
        'PERIODE_ID': $('#PERIODE_ID').val(),
        'PROJECT_ID': $('#PROJECT_ID').val(),
        'VIEW_MODE': $('#VIEW_MODE').val(),
        'NAMA': $('#NAMA').val(),
      },
      dataType: 'script',
      method: 'GET',
      /*
      success : function(res){
      	lib();
      }
      */
    });
  }

  function datagrid() {
    var wind = parseInt($(window).height());
    var top = parseInt($('.navbar').outerHeight());
    $('#t-responsive').height(wind - top - 70);
    //$('#t').datagrid('resize');
  }

  function doSearch() {
    make_dg();
    $('#t').datagrid('load', {
      PERIODE_ID: $('#PERIODE_ID').val(),
      PROJECT_ID: $('#PROJECT_ID').val(),
      VIEW_MODE: $('#VIEW_MODE').val(),
      NAMA: $('#NAMA').val(),
    });
  }
</script>