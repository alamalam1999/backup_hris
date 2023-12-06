<?php

session_start();

require __DIR__ . '/lib/flash.php';

include 'app-load.php';
is_login('karyawan.edit');
$ID = get_input('id');

if (empty($ID)) {
  header('Location: karyawan.php');
  exit;
}

$EDIT = db_first(" SELECT KARYAWAN_ID, NIK, NAMA FROM karyawan WHERE KARYAWAN_ID='$ID' ");

$PENDIDIKAN_FORMAL = db_fetch(" SELECT * FROM pendidikan_karyawan WHERE KARYAWAN_ID = '$ID' ");
$PENDIDIKAN_NONFORMAL = db_fetch(" SELECT * FROM kursus_karyawan WHERE KARYAWAN_ID = '$ID' ");
$BAHASA = db_fetch(" SELECT * FROM bahasa_karyawan WHERE KARYAWAN_ID = '$ID' ");
$OP = get_input('op');


/* PENDIDIKAN FORMAL */
if ($OP == 'add_formal') {
  if (isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
    $file_pendidikan_allow_ext = array('png', 'jpg');
    $file_pendidikan_name = isset($_FILES['FILE_PENDIDIKAN']['name']) ? $_FILES['FILE_PENDIDIKAN']['name'] : '';
    $file_pendidikan_tmp = isset($_FILES['FILE_PENDIDIKAN']['tmp_name']) ? $_FILES['FILE_PENDIDIKAN']['tmp_name'] : '';
    $file_pendidikan_ext = strtolower(substr(strrchr($file_pendidikan_name, "."), 1));
    $file_pendidikan_new = 'FILE_PENDIDIKAN_' . $ID . '_' . rand(11111, 99999) . '_' . $file_pendidikan_name;
    $file_pendidikan_dest = 'uploads/karyawan/' . $file_pendidikan_new;

    $REQUIRE = array('TINGKAT', 'INSTITUSI', 'LOKASI', 'TAHUN_MULAI', 'TAHUN_SELESAI', 'GPA', 'APPROVED');
    $ERROR_REQUIRE = 0;
    $ERROR = array();

    foreach ($REQUIRE as $REQ) {
      $IREQ = get_input($REQ);
      if ($IREQ == "") {
        $ERROR_REQUIRE = 1;
      }

      if (!file_exists($_FILES["FILE_PENDIDIKAN"]["tmp_name"])) {
        $REQUIRE[] = 'DOKUMEN PENDIDIKAN FORMAL';
        $ERROR_REQUIRE = 1;
      }
    }

    if ($ERROR_REQUIRE) {
      $ERROR[] = 'Kolom <strong>' . implode(', ', $REQUIRE) . '</strong> wajib di isi.';
    } else {
      $FIELDS = array(
        'TINGKAT',
        'JURUSAN',
        'INSTITUSI',
        'LOKASI',
        'TAHUN_MULAI',
        'TAHUN_SELESAI',
        'GPA',
        'APPROVED',
      );

      $NEW_FILE_PENDIDIKAN = 0;
      if (is_uploaded_file($file_pendidikan_tmp)) {
        if (move_uploaded_file($file_pendidikan_tmp, $file_pendidikan_dest)) {
          $FIELDS[] = 'FILE_PENDIDIKAN';
          $NEW_FILE_PENDIDIKAN = 1;
        }
      }

      foreach ($FIELDS as $F) {
        if ($F == 'FILE_PENDIDIKAN') {
          if ($NEW_FILE_PENDIDIKAN == '1') {
            $INSERT_VAL[$F] = "'" . db_escape($file_pendidikan_new) . "'";
          }
        } else {
          $INSERT_VAL[$F] = "'" . db_escape(get_input($F)) . "'";
        }
      }

      $sql = db_execute(" INSERT INTO pendidikan_karyawan (KARYAWAN_ID, " . implode(',', $FIELDS) . ") VALUES ('$ID', " . implode(',', $INSERT_VAL) . ") ");

      if ($sql) {
        flash('success', 'Dokumen Pendidikan Formal berhasil disimpan', FLASH_SUCCESS);
        header('location: dokumen-pendidikan.php?id=' . $ID . '#formal');
      }

      exit;
    }
  }
}

if ($OP == 'edit_formal') {
  $PENDIDIKAN_KARYAWAN_ID = get_input('pendidikan_karyawan_id');
  $EDIT_FORMAL = db_first(" SELECT * FROM pendidikan_karyawan WHERE PENDIDIKAN_KARYAWAN_ID = '$PENDIDIKAN_KARYAWAN_ID' AND KARYAWAN_ID = '$ID'  ");
}

if ($OP == 'update_formal') {

  $file_pendidikan_allow_ext = array('png', 'jpg');
  $file_pendidikan_name = isset($_FILES['FILE_PENDIDIKAN']['name']) ? $_FILES['FILE_PENDIDIKAN']['name'] : '';
  $file_pendidikan_tmp = isset($_FILES['FILE_PENDIDIKAN']['tmp_name']) ? $_FILES['FILE_PENDIDIKAN']['tmp_name'] : '';
  $file_pendidikan_ext = strtolower(substr(strrchr($file_pendidikan_name, "."), 1));
  $file_pendidikan_new = 'FILE_PENDIDIKAN_' . $ID . '_' . rand(11111, 99999) . '_' . $file_pendidikan_name;
  $file_pendidikan_dest = 'uploads/karyawan/' . $file_pendidikan_new;

  $REQUIRE = array('TINGKAT', 'INSTITUSI', 'LOKASI', 'TAHUN_MULAI', 'TAHUN_SELESAI', 'GPA', 'APPROVED');
  $ERROR_REQUIRE = 0;
  $ERROR = array();

  foreach ($REQUIRE as $REQ) {
    $IREQ = get_input($REQ);
    if ($IREQ == "") {
      $ERROR_REQUIRE = 1;
    }
  }

  if ($ERROR_REQUIRE) {
    $ERROR[] = 'Kolom <strong>' . implode(', ', $REQUIRE) . '</strong> wajib di isi.';
  } else {
    $FIELDS = array(
      'TINGKAT',
      'JURUSAN',
      'INSTITUSI',
      'LOKASI',
      'TAHUN_MULAI',
      'TAHUN_SELESAI',
      'GPA',
      'APPROVED',
      'KETERANGAN_APPROVED',
    );

    $NEW_FILE_PENDIDIKAN = 0;
    if (is_uploaded_file($file_pendidikan_tmp)) {
      if (move_uploaded_file($file_pendidikan_tmp, $file_pendidikan_dest)) {
        $FIELDS[] = 'FILE_PENDIDIKAN';
        $NEW_FILE_PENDIDIKAN = 1;
      }
    }

    foreach ($FIELDS as $F) {
      if ($F == 'FILE_PENDIDIKAN') {
        if ($NEW_FILE_PENDIDIKAN == '1') {
          $UPDATE_VAL[$F] = $F . "='" . db_escape($file_pendidikan_new) . "'";
        }
      } else if ($F == 'KETERANGAN_APPROVED') {
        $KETERANGAN_APPROVED = get_input('KETERANGAN_APPROVED');
        $UPDATE_VAL[$F] = $F . "='" . db_escape($KETERANGAN_APPROVED) . "'";
      } else {
        $UPDATE_VAL[$F] = $F . "='" . db_escape(get_input($F)) . "'";
      }
    }

    $PENDIDIKAN_KARYAWAN_ID = get_input('pendidikan_karyawan_id');
    $ID = get_input('id');

    $sql = db_execute(" UPDATE pendidikan_karyawan SET " . implode(',', $UPDATE_VAL) . " WHERE PENDIDIKAN_KARYAWAN_ID='$PENDIDIKAN_KARYAWAN_ID' AND KARYAWAN_ID = '$ID' ");

    if ($sql) {
      flash('success', 'Dokumen Pendidikan Formal berhasil diperbarui', FLASH_SUCCESS);
      header('location: dokumen-pendidikan.php?id=' . $ID . '#formal');
    }

    exit;
  }
}

if ($OP == 'approve_formal') {
  $PENDIDIKAN_KARYAWAN_ID = get_input('pendidikan_karyawan_id');
  $ID = get_input('id');
  $sql = db_execute(" UPDATE pendidikan_karyawan SET APPROVED='APPROVED', KETERANGAN_APPROVED = '' WHERE PENDIDIKAN_KARYAWAN_ID = '$PENDIDIKAN_KARYAWAN_ID' AND KARYAWAN_ID = '$ID' ");
  if ($sql) {
    flash('success', 'Dokumen Pendidikan Formal berhasil disetujui', FLASH_SUCCESS);
    header('location: dokumen-pendidikan.php?id=' . $ID . '#formal');
  }
  exit;
}

if ($OP == 'delete_formal') {
  $PENDIDIKAN_KARYAWAN_ID = get_input('pendidikan_karyawan_id');
  $ID = get_input('id');
  $sql = db_execute(" DELETE FROM pendidikan_karyawan WHERE PENDIDIKAN_KARYAWAN_ID = '$PENDIDIKAN_KARYAWAN_ID' AND KARYAWAN_ID = '$ID' ");
  if ($sql) {
    flash('success', 'Dokumen Pendidikan Formal berhasil dihapus', FLASH_SUCCESS);
    header('location: dokumen-pendidikan.php?id=' . $ID . '#formal');
  }
  exit;
}
/* END OF PENDIDIKAN FORMAL */

/* PENDIDIKAN NON FORMAL */
if ($OP == 'add_nonformal') {
  if (isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
    $file_kursus_allow_ext = array('png', 'jpg');
    $file_kursus_name = isset($_FILES['FILE_KURSUS']['name']) ? $_FILES['FILE_KURSUS']['name'] : '';
    $file_kursus_tmp = isset($_FILES['FILE_KURSUS']['tmp_name']) ? $_FILES['FILE_KURSUS']['tmp_name'] : '';
    $file_kursus_ext = strtolower(substr(strrchr($file_kursus_name, "."), 1));
    $file_kursus_new = 'FILE_KURSUS_' . $ID . '_' . rand(11111, 99999) . '_' . $file_kursus_name;
    $file_kursus_dest = 'uploads/karyawan/' . $file_kursus_new;

    $REQUIRE = array('NAMA_KURSUS', 'TEMPAT', 'PERIODE_MULAI', 'PERIODE_SELESAI', 'KETERANGAN', 'APPROVED');
    $ERROR_REQUIRE = 0;
    $ERROR = array();

    foreach ($REQUIRE as $REQ) {
      $IREQ = get_input($REQ);
      if ($IREQ == "") {
        $ERROR_REQUIRE = 1;
      }

      if (!file_exists($_FILES["FILE_KURSUS"]["tmp_name"])) {
        $REQUIRE[] = 'DOKUMEN NON FORMAL/KURSUS';
        $ERROR_REQUIRE = 1;
      }
    }

    if ($ERROR_REQUIRE) {
      $ERROR[] = 'Kolom <strong>' . implode(', ', $REQUIRE) . '</strong> wajib di isi.';
    } else {
      $FIELDS = array(
        'NAMA_KURSUS',
        'TEMPAT',
        'PERIODE_MULAI',
        'PERIODE_SELESAI',
        'KETERANGAN',
        'APPROVED',
      );

      $NEW_FILE_KURSUS = 0;
      if (is_uploaded_file($file_kursus_tmp)) {
        if (move_uploaded_file($file_kursus_tmp, $file_kursus_dest)) {
          $FIELDS[] = 'FILE_KURSUS';
          $NEW_FILE_KURSUS = 1;
        }
      }

      foreach ($FIELDS as $F) {
        if ($F == 'FILE_KURSUS') {
          if ($NEW_FILE_KURSUS == '1') {
            $INSERT_VAL[$F] = "'" . db_escape($file_kursus_new) . "'";
          }
        } else {
          $INSERT_VAL[$F] = "'" . db_escape(get_input($F)) . "'";
        }
      }

      $sql = db_execute(" INSERT INTO kursus_karyawan (KARYAWAN_ID, " . implode(',', $FIELDS) . ") VALUES ('$ID', " . implode(',', $INSERT_VAL) . ") ");

      if ($sql) {
        flash('success', 'Dokumen Pendidikan Non Formal berhasil disimpan', FLASH_SUCCESS);
        header('location: dokumen-pendidikan.php?id=' . $ID . '#nonformal');
      }

      exit;
    }
  }
}

if ($OP == 'edit_nonformal') {
  $KURSUS_KARYAWAN_ID = get_input('kursus_karyawan_id');
  $EDIT_NONFORMAL = db_first(" SELECT * FROM kursus_karyawan WHERE KURSUS_KARYAWAN_ID = '$KURSUS_KARYAWAN_ID' AND KARYAWAN_ID = '$ID'  ");
}

if ($OP == 'update_nonformal') {

  $file_kursus_allow_ext = array('png', 'jpg');
  $file_kursus_name = isset($_FILES['FILE_KURSUS']['name']) ? $_FILES['FILE_KURSUS']['name'] : '';
  $file_kursus_tmp = isset($_FILES['FILE_KURSUS']['tmp_name']) ? $_FILES['FILE_KURSUS']['tmp_name'] : '';
  $file_kursus_ext = strtolower(substr(strrchr($file_kursus_name, "."), 1));
  $file_kursus_new = 'FILE_KURSUS_' . $ID . '_' . rand(11111, 99999) . '_' . $file_kursus_name;
  $file_kursus_dest = 'uploads/karyawan/' . $file_kursus_new;

  $REQUIRE = array('NAMA_KURSUS', 'TEMPAT', 'PERIODE_MULAI', 'PERIODE_SELESAI', 'KETERANGAN', 'APPROVED');
  $ERROR_REQUIRE = 0;
  $ERROR = array();

  foreach ($REQUIRE as $REQ) {
    $IREQ = get_input($REQ);
    if ($IREQ == "") {
      $ERROR_REQUIRE = 1;
    }
  }

  if ($ERROR_REQUIRE) {
    $ERROR[] = 'Kolom <strong>' . implode(', ', $REQUIRE) . '</strong> wajib di isi.';
  } else {
    $FIELDS = array(
      'NAMA_KURSUS',
      'TEMPAT',
      'PERIODE_MULAI',
      'PERIODE_SELESAI',
      'KETERANGAN',
      'KETERANGAN_APPROVED',
      'APPROVED',
    );

    $NEW_FILE_KURSUS = 0;
    if (is_uploaded_file($file_kursus_tmp)) {
      if (move_uploaded_file($file_kursus_tmp, $file_kursus_dest)) {
        $FIELDS[] = 'FILE_KURSUS';
        $NEW_FILE_KURSUS = 1;
      }
    }

    foreach ($FIELDS as $F) {
      if ($F == 'FILE_KURSUS') {
        if ($NEW_FILE_KURSUS == '1') {
          $UPDATE_VAL[$F] = $F . "='" . db_escape($file_kursus_new) . "'";
        }
      } else if ($F == 'KETERANGAN_APPROVED') {
        $KETERANGAN_APPROVED = get_input('KETERANGAN_APPROVED');
        $UPDATE_VAL[$F] = $F . "='" . db_escape($KETERANGAN_APPROVED) . "'";
      } else {
        $UPDATE_VAL[$F] = $F . "='" . db_escape(get_input($F)) . "'";
      }
    }

    $KURSUS_KARYAWAN_ID = get_input('kursus_karyawan_id');
    $ID = get_input('id');

    $sql = db_execute(" UPDATE kursus_karyawan SET " . implode(',', $UPDATE_VAL) . " WHERE KURSUS_KARYAWAN_ID='$KURSUS_KARYAWAN_ID' AND KARYAWAN_ID = '$ID' ");

    if ($sql) {
      flash('success', 'Dokumen Pendidikan Non Formal berhasil diperbarui', FLASH_SUCCESS);
      header('location: dokumen-pendidikan.php?id=' . $ID . '#nonformal');
    }

    exit;
  }
}

if ($OP == 'approve_nonformal') {
  $KURSUS_KARYAWAN_ID = get_input('kursus_karyawan_id');
  $ID = get_input('id');
  $sql = db_execute(" UPDATE kursus_karyawan SET APPROVED='APPROVED', KETERANGAN_APPROVED='' WHERE KURSUS_KARYAWAN_ID = '$KURSUS_KARYAWAN_ID' AND KARYAWAN_ID = '$ID' ");
  if ($sql) {
    flash('success', 'Dokumen Pendidikan Non Formal berhasil disetujui', FLASH_SUCCESS);
    header('location: dokumen-pendidikan.php?id=' . $ID . '#nonformal');
  }
  exit;
}

if ($OP == 'delete_nonformal') {
  $KURSUS_KARYAWAN_ID = get_input('kursus_karyawan_id');
  $ID = get_input('id');
  $sql = db_execute(" DELETE FROM kursus_karyawan WHERE KURSUS_KARYAWAN_ID = '$KURSUS_KARYAWAN_ID' AND KARYAWAN_ID = '$ID' ");
  if ($sql) {
    flash('success', 'Dokumen Pendidikan Non Formal berhasil dihapus', FLASH_SUCCESS);
    header('location: dokumen-pendidikan.php?id=' . $ID . '#nonformal');
  }
  exit;
}
/* END OF PENDIDIKAN NON FORMAL */

/* BAHASA */
if ($OP == 'add_bahasa') {
  if (isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
    $REQUIRE = array('BAHASA', 'LISAN', 'TULISAN', 'APPROVED');
    $ERROR_REQUIRE = 0;
    $ERROR = array();

    foreach ($REQUIRE as $REQ) {
      $IREQ = get_input($REQ);
      if ($IREQ == "") {
        $ERROR_REQUIRE = 1;
      }
    }

    if ($ERROR_REQUIRE) {
      $ERROR[] = 'Kolom <strong>' . implode(', ', $REQUIRE) . '</strong> wajib di isi.';
    } else {
      $FIELDS = array(
        'BAHASA',
        'LISAN',
        'TULISAN',
        'APPROVED',
      );

      foreach ($FIELDS as $F) {
        $INSERT_VAL[$F] = "'" . db_escape(get_input($F)) . "'";
      }

      $sql = db_execute(" INSERT INTO bahasa_karyawan (KARYAWAN_ID, " . implode(',', $FIELDS) . ") VALUES ('$ID', " . implode(',', $INSERT_VAL) . ") ");

      if ($sql) {
        flash('success', 'Bahasa yang dikuasai berhasil disimpan', FLASH_SUCCESS);
        header('location: dokumen-pendidikan.php?id=' . $ID . '#bahasa');
      }

      exit;
    }
  }
}

if ($OP == 'edit_bahasa') {
  $BAHASA_KARYAWAN_ID = get_input('bahasa_karyawan_id');
  $EDIT_BAHASA = db_first(" SELECT * FROM bahasa_karyawan WHERE BAHASA_KARYAWAN_ID = '$BAHASA_KARYAWAN_ID' AND KARYAWAN_ID = '$ID'  ");
}

if ($OP == 'update_bahasa') {
  $REQUIRE = array('BAHASA', 'LISAN', 'TULISAN', 'APPROVED');
  $ERROR_REQUIRE = 0;
  $ERROR = array();

  foreach ($REQUIRE as $REQ) {
    $IREQ = get_input($REQ);
    if ($IREQ == "") {
      $ERROR_REQUIRE = 1;
    }
  }

  if ($ERROR_REQUIRE) {
    $ERROR[] = 'Kolom <strong>' . implode(', ', $REQUIRE) . '</strong> wajib di isi.';
  } else {
    $FIELDS = array(
      'BAHASA',
      'LISAN',
      'TULISAN',
      'APPROVED',
    );

    foreach ($FIELDS as $F) {
      $UPDATE_VAL[$F] = $F . "='" . db_escape(get_input($F)) . "'";
    }

    $BAHASA_KARYAWAN_ID = get_input('bahasa_karyawan_id');
    $ID = get_input('id');

    $sql = db_execute(" UPDATE bahasa_karyawan SET " . implode(',', $UPDATE_VAL) . " WHERE BAHASA_KARYAWAN_ID='$BAHASA_KARYAWAN_ID' AND KARYAWAN_ID = '$ID' ");

    if ($sql) {
      flash('success', 'Bahasa yang dikuasai berhasil diperbarui', FLASH_SUCCESS);
      header('location: dokumen-pendidikan.php?id=' . $ID . '#bahasa');
    }

    exit;
  }
}

if ($OP == 'approve_bahasa') {
  $BAHASA_KARYAWAN_ID = get_input('bahasa_karyawan_id');
  $ID = get_input('id');
  $sql = db_execute(" UPDATE bahasa_karyawan SET APPROVED='APPROVED' WHERE BAHASA_KARYAWAN_ID = '$BAHASA_KARYAWAN_ID' AND KARYAWAN_ID = '$ID' ");
  if ($sql) {
    flash('success', 'Bahasa yang dikuasai berhasil disetujui', FLASH_SUCCESS);
    header('location: dokumen-pendidikan.php?id=' . $ID . '#bahasa');
  }
  exit;
}

if ($OP == 'delete_bahasa') {
  $BAHASA_KARYAWAN_ID = get_input('bahasa_karyawan_id');
  $ID = get_input('id');
  $sql = db_execute(" DELETE FROM bahasa_karyawan WHERE BAHASA_KARYAWAN_ID = '$BAHASA_KARYAWAN_ID' AND KARYAWAN_ID = '$ID' ");
  if ($sql) {
    flash('success', 'Bahasa yang dikuasai berhasil dihapus', FLASH_SUCCESS);
    header('location: dokumen-pendidikan.php?id=' . $ID . '#bahasa');
  }
  exit;
}
/* END OF BAHASA */

$JS[] = 'static/sweetalert/sweetalert2.all.min.js';
$CSS[] = 'static/sweetalert/sweetalert2.min.css';
include 'header.php';
?>

<section class="container-fluid" style="margin-top: 25px;">
  <a href="karyawan-action.php?op=edit&id=<?= $ID; ?>" class="btn btn-warning">&laquo; Back to Form</a>

  <?php flash('success'); ?>

  <h1 style="margin-top:20px;" class="border-title">
    Dokumen Pendidikan
    &nbsp;&nbsp;<span class="text-primary"><?php echo isset($EDIT->NAMA) ? strtoupper($EDIT->NAMA) : ''; ?></span>
    &nbsp;&nbsp;&nbsp;<?php echo isset($EDIT->NIK) ? '[NIK : ' . $EDIT->NIK . ']' : ''; ?>
    <?php echo isset($EDIT->KARYAWAN_ID) ? ' &nbsp;&nbsp;&nbsp; [PIN : ' . $EDIT->KARYAWAN_ID . ']' : ''; ?>
  </h1>

  <div id="formal" style="margin-bottom: 35px;">
    <form id="form-formal" class="form-horizontal" action="dokumen-pendidikan.php?op=add_formal&id=<?= $ID; ?>" method="POST" enctype="multipart/form-data">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group bg-info text-center" style="margin: 10px 0 20px; border-bottom: 2px solid #eee;">
            <label class="control-label" style="padding-bottom: 5px;">
              PENDIDIKAN FORMAL
            </label>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label for="tingkat" class="col-sm-3 control-label">Tingkat</label>
            <div class="col-sm-9">
              <?php
              echo dropdown(
                'TINGKAT',
                array(
                  '' => '-- PILIH TINGKAT --',
                  'SD' => 'SD',
                  'SMP' => 'SMP',
                  'SMA' => 'SMA',
                  'SMK' => 'SMK',
                  'D3' => 'DIPLOMA (D3)',
                  'S1' => 'SARJANA (S1)',
                  'S2' => 'PASCA SARJANA (S2)'
                ),
                set_value('TINGKAT', $EDIT_FORMAL->TINGKAT),
                ' id="tingkat" class="form-control" required'
              );
              ?>
            </div>
          </div>

          <div class="form-group">
            <label for="jurusan" class="col-sm-3 control-label">Jurusan</label>
            <div class="col-sm-9">
              <input type="text" name="JURUSAN" id="jurusan" class="form-control" value="<?= set_value('JURUSAN', $EDIT_FORMAL->JURUSAN); ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label for="institusi" class="col-sm-3 control-label">Institusi</label>
            <div class="col-sm-9">
              <input type="text" name="INSTITUSI" id="institusi" class="form-control" value="<?= set_value('INSTITUSI', $EDIT_FORMAL->INSTITUSI); ?>" required>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label for="lokasi" class="col-sm-3 control-label">Lokasi</label>
            <div class="col-sm-9">
              <input type="text" name="LOKASI" id="lokasi" class="form-control" value="<?= set_value('LOKASI', $EDIT_FORMAL->LOKASI); ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label for="tahun_mulai" class="col-sm-3 control-label">Tahun Mulai</label>
            <div class="col-sm-9">
              <input type="number" name="TAHUN_MULAI" id="tahun_mulai" class="form-control" value="<?= set_value('TAHUN_MULAI', $EDIT_FORMAL->TAHUN_MULAI); ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label for="tahun_selesai" class="col-sm-3 control-label">Tahun Selesai</label>
            <div class="col-sm-9">
              <input type="number" name="TAHUN_SELESAI" id="tahun_selesai" class="form-control" value="<?= set_value('TAHUN_SELESAI', $EDIT_FORMAL->TAHUN_SELESAI); ?>" required>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label for="gpa" class="col-sm-3 control-label">GPA</label>
            <div class="col-sm-9">
              <input type="text" name="GPA" id="gpa" class="form-control" value="<?= set_value('GPA', $EDIT_FORMAL->GPA); ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label for="file_pendidikan" class="col-sm-3 control-label">Dokumen</label>
            <div class="col-sm-9">
              <input type="file" name="FILE_PENDIDIKAN" id="file_pendidikan" class="form-control">
            </div>
          </div>

          <div class="form-group">
            <label for="approved_formal" class="col-sm-3 control-label">Status</label>
            <div class="col-sm-9">
              <?php
              echo dropdown(
                'APPROVED',
                array(
                  '' => '-- PILIH STATUS --',
                  'PENDING' => 'PENDING',
                  'APPROVED' => 'APPROVED',
                ),
                set_value('APPROVED', $EDIT_FORMAL->APPROVED),
                ' id="approved_formal" class="form-control" required'
              );
              ?>
            </div>
          </div>

          <?php if ($OP == 'edit_formal' && $PENDIDIKAN_KARYAWAN_ID != '') { ?>
            <div class="form-group" style="display: none;" id="unapproved_formal">
              <label for="keterangan_formal" class="col-sm-3 control-label">Unapproved</label>
              <div class="col-sm-9">
                <input type="text" name="KETERANGAN_APPROVED" id="keterangan_formal" class="form-control" value="<?= set_value('KETERANGAN_APPROVED', $EDIT_FORMAL->KETERANGAN_APPROVED); ?>">
              </div>
            </div>
          <?php } ?>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12">
          <div class="pull-right">
            <button type="submit" class="btn btn-success btn-submit"><i class="fa fa-hdd-o" style="padding-right: 5px;"></i> Simpan</button>
            <a href="dokumen-pendidikan.php?id=<?= $ID ?>#formal" class="btn btn-warning btn-reset"><i class="fa fa-times" style="padding-right: 5px;"></i> Reset</a>
          </div>
        </div>
      </div>
    </form>

    <div class="row">
      <div class="col-sm-12">
        <div class="table-responsive">
          <table class="table table-bordered" style="margin-top: 15px;">
            <thead class="bg-warning">
              <tr>
                <th class="text-center">No</th>
                <th>Tingkat</th>
                <th>Jurusan</th>
                <th>Nama Sekolah/Instansi</th>
                <th>Lokasi</th>
                <th class="text-center">Th. Mulai</th>
                <th class="text-center">Th. Selesai</th>
                <th class="text-right">GPA</th>
                <th class="text-center">Dokumen</th>
                <th class="text-right"></th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($PENDIDIKAN_FORMAL) > 0) {
                foreach ($PENDIDIKAN_FORMAL as $row) {
                  $no_formal = $no_formal + 1; ?>
                  <tr>
                    <td class="text-center"><?= $no_formal; ?></td>
                    <td><?= $row->TINGKAT; ?></td>
                    <td><?= $row->JURUSAN; ?></td>
                    <td><?= $row->INSTITUSI; ?></td>
                    <td><?= $row->LOKASI; ?></td>
                    <td class="text-center"><?= $row->TAHUN_MULAI; ?></td>
                    <td class="text-center"><?= $row->TAHUN_SELESAI; ?></td>
                    <td class="text-right"><?= $row->GPA; ?></td>
                    <td class="text-center"><a href="<?= base_url() . "uploads/karyawan/" . $row->FILE_PENDIDIKAN; ?>" download>Unduh</a></td>
                    <td class="text-right">
                      <?php if ($row->APPROVED == 'PENDING') { ?>
                        <a href="dokumen-pendidikan.php?op=approve_formal&pendidikan_karyawan_id=<?= $row->PENDIDIKAN_KARYAWAN_ID; ?>&id=<?= $row->KARYAWAN_ID; ?>" class="btn btn-success btn-xs btn-approve-formal" title="<?= empty($row->KETERANGAN_APPROVED) ? '' : 'Keterangan tidak disetujui : ' . $row->KETERANGAN_APPROVED; ?>">
                          <i class="fa fa-check-square-o"></i> Approve
                        </a>
                      <?php } ?>
                      <a href="dokumen-pendidikan.php?op=edit_formal&pendidikan_karyawan_id=<?= $row->PENDIDIKAN_KARYAWAN_ID; ?>&id=<?= $row->KARYAWAN_ID; ?>#formal" class="btn btn-primary btn-xs btn-edit-formal">
                        <i class="fa fa-edit"></i> Edit
                      </a>
                      <a href="#" class="btn btn-danger btn-xs btn-delete-formal" data-pendidikan_karyawan_id="<?= $row->PENDIDIKAN_KARYAWAN_ID; ?>" data-karyawan_id="<?= $row->KARYAWAN_ID; ?>">
                        <i class="fa fa-trash"></i> Del
                      </a>
                    </td>
                  </tr>
                <?php }
              } else { ?>
                <tr>
                  <td colspan="9" class="text-center text-warning">Data Pendidikan Formal masih kosong</td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div id="nonformal" style="margin-bottom: 35px;">
    <form id="form-nonformal" class="form-horizontal" action="dokumen-pendidikan.php?op=add_nonformal&id=<?= $ID; ?>" method="POST" enctype="multipart/form-data">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group bg-info text-center" style="margin: 10px 0 20px; border-bottom: 2px solid #eee;">
            <label class="control-label" style="padding-bottom: 5px;">
              PENDIDIKAN NON FORMAL
            </label>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label for="nama_kursus" class="col-sm-3 control-label">Nama Kursus</label>
            <div class="col-sm-9">
              <input type="text" name="NAMA_KURSUS" id="nama_kursus" class="form-control" value="<?= set_value('NAMA_KURSUS', $EDIT_NONFORMAL->NAMA_KURSUS); ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label for="tempat" class="col-sm-3 control-label">Tempat</label>
            <div class="col-sm-9">
              <input type="text" name="TEMPAT" id="tempat" class="form-control" value="<?= set_value('TEMPAT', $EDIT_NONFORMAL->TEMPAT); ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label for="keterangan" class="col-sm-3 control-label">Keterangan</label>
            <div class="col-sm-9">
              <input type="text" name="KETERANGAN" id="keterangan" class="form-control" value="<?= set_value('KETERANGAN', $EDIT_NONFORMAL->KETERANGAN); ?>" required>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label for="periode_mulai" class="col-sm-3 control-label">Tgl Mulai</label>
            <div class="col-sm-9">
              <input type="date" name="PERIODE_MULAI" id="periode_mulai" class="form-control" value="<?= set_value('PERIODE_MULAI', $EDIT_NONFORMAL->PERIODE_MULAI); ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label for="periode_selesai" class="col-sm-3 control-label">Tgl Selesai</label>
            <div class="col-sm-9">
              <input type="date" name="PERIODE_SELESAI" id="periode_selesai" class="form-control" value="<?= set_value('PERIODE_SELESAI', $EDIT_NONFORMAL->PERIODE_SELESAI); ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label for="file_kursus" class="col-sm-3 control-label">Dokumen</label>
            <div class="col-sm-9">
              <input type="file" name="FILE_KURSUS" id="file_kursus" class="form-control">
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label for="approved_nonformal" class="col-sm-3 control-label">Status</label>
            <div class="col-sm-9">
              <?php
              echo dropdown(
                'APPROVED',
                array(
                  '' => '-- PILIH STATUS --',
                  'PENDING' => 'PENDING',
                  'APPROVED' => 'APPROVED',
                ),
                set_value('APPROVED', $EDIT_NONFORMAL->APPROVED),
                ' id="approved_nonformal" class="form-control" required'
              );
              ?>
            </div>
          </div>

          <?php if ($OP == 'edit_nonformal' && $KURSUS_KARYAWAN_ID != '') { ?>
            <div class="form-group" style="display: none;" id="unapproved_nonformal">
              <label for="keterangan_nonformal" class="col-sm-3 control-label">Unapproved</label>
              <div class="col-sm-9">
                <input type="text" name="KETERANGAN_APPROVED" id="keterangan_nonformal" class="form-control" value="<?= set_value('KETERANGAN_APPROVED', $EDIT_NONFORMAL->KETERANGAN_APPROVED); ?>">
              </div>
            </div>
          <?php } ?>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12">
          <div class="pull-right">
            <button type="submit" class="btn btn-success btn-submit"><i class="fa fa-hdd-o" style="padding-right: 5px;"></i> Simpan</button>
            <a href="dokumen-pendidikan.php?id=<?= $ID ?>#nonformal" class="btn btn-warning btn-reset"><i class="fa fa-times" style="padding-right: 5px;"></i> Reset</a>
          </div>
        </div>
      </div>
    </form>

    <div class="row">
      <div class="col-sm-12">
        <div class="table-responsive">
          <table class="table table-bordered" style="margin-top: 15px;">
            <thead class="bg-warning">
              <tr>
                <th class="text-center">No</th>
                <th>Nama Kursus</th>
                <th>Tempat</th>
                <th class="text-center">Tgl. Mulai</th>
                <th class="text-center">Tgl. Selesai</th>
                <th>Keterangan</th>
                <th class="text-center">Dokumen</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($PENDIDIKAN_NONFORMAL) > 0) {
                foreach ($PENDIDIKAN_NONFORMAL as $row) {
                  $no_nonformal = $no_nonformal + 1; ?>
                  <tr>
                    <td class="text-center"><?= $no_nonformal; ?></td>
                    <td><?= $row->NAMA_KURSUS; ?></td>
                    <td><?= $row->TEMPAT; ?></td>
                    <td class="text-center"><?= $row->PERIODE_MULAI; ?></td>
                    <td class="text-center"><?= $row->PERIODE_SELESAI; ?></td>
                    <td><?= $row->KETERANGAN; ?></td>
                    <td class="text-center"><a href="<?= base_url() . "uploads/karyawan/" . $row->FILE_PENDIDIKAN; ?>" download>Unduh</a></td>
                    <td class="text-right">
                      <?php if ($row->APPROVED == 'PENDING') { ?>
                        <a href="dokumen-pendidikan.php?op=approve_nonformal&kursus_karyawan_id=<?= $row->KURSUS_KARYAWAN_ID; ?>&id=<?= $row->KARYAWAN_ID; ?>" class="btn btn-success btn-xs btn-approve-formal" title="<?= empty($row->KETERANGAN_APPROVED) ? '' : 'Keterangan tidak disetujui : ' . $row->KETERANGAN_APPROVED; ?>">
                          <i class="fa fa-check-square-o"></i> Approve
                        </a>
                      <?php } ?>
                      <a href="dokumen-pendidikan.php?op=edit_nonformal&kursus_karyawan_id=<?= $row->KURSUS_KARYAWAN_ID; ?>&id=<?= $row->KARYAWAN_ID; ?>#nonformal" class="btn btn-primary btn-xs btn-edit-formal">
                        <i class="fa fa-edit"></i> Edit
                      </a>
                      <a href="#" class="btn btn-danger btn-xs btn-delete-nonformal" data-kursus_karyawan_id="<?= $row->KURSUS_KARYAWAN_ID; ?>" data-karyawan_id="<?= $row->KARYAWAN_ID; ?>">
                        <i class="fa fa-trash"></i> Del
                      </a>
                    </td>
                  </tr>
                <?php }
              } else { ?>
                <tr>
                  <td colspan="8" class="text-center text-warning">Data Pendidikan Non Formal masih kosong</td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div id="bahasa" style="margin-bottom: 35px;">
    <form id="form-bahasa" class="form-horizontal" action="dokumen-pendidikan.php?op=add_bahasa&id=<?= $ID; ?>" method="POST" enctype="multipart/form-data">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group bg-info text-center" style="margin: 10px 0 20px; border-bottom: 2px solid #eee;">
            <label class="control-label" style="padding-bottom: 5px;">
              BAHASA ASING YANG DIKUASAI
            </label>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label for="bahasa" class="col-sm-3 control-label">Bahasa</label>
            <div class="col-sm-9">
              <input type="text" name="BAHASA" id="bahasa" class="form-control" value="<?= set_value('BAHASA', $EDIT_BAHASA->BAHASA); ?>" required>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label for="lisan" class="col-sm-3 control-label">Lisan</label>
            <div class="col-sm-9">
              <?php
              echo dropdown(
                'LISAN',
                array(
                  '' => '-- PILIH KEMAMPUAN LISAN --',
                  'kurang' => 'KURANG',
                  'cukup' => 'CUKUP',
                  'baik' => 'BAIK',
                ),
                set_value('LISAN', $EDIT_BAHASA->LISAN),
                ' id="lisan" class="form-control" required'
              );
              ?>
            </div>
          </div>

          <div class="form-group">
            <label for="tulisan" class="col-sm-3 control-label">Tulisan</label>
            <div class="col-sm-9">
              <?php
              echo dropdown(
                'TULISAN',
                array(
                  '' => '-- PILIH KEMAMPUAN TULISAN --',
                  'kurang' => 'KURANG',
                  'cukup' => 'CUKUP',
                  'baik' => 'BAIK',
                ),
                set_value('TULISAN', $EDIT_BAHASA->TULISAN),
                ' id="tulisan" class="form-control" required'
              );
              ?>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label for="approved_bahasa" class="col-sm-3 control-label">Status</label>
            <div class="col-sm-9">
              <?php
              echo dropdown(
                'APPROVED',
                array(
                  '' => '-- PILIH STATUS --',
                  'PENDING' => 'PENDING',
                  'APPROVED' => 'APPROVED',
                ),
                set_value('APPROVED', $EDIT_BAHASA->APPROVED),
                ' id="approved_bahasa" class="form-control" required'
              );
              ?>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12">
          <div class="pull-right">
            <button type="submit" class="btn btn-success btn-submit"><i class="fa fa-hdd-o" style="padding-right: 5px;"></i> Simpan</button>
            <a href="dokumen-pendidikan.php?id=<?= $ID ?>#bahasa" class="btn btn-warning btn-reset"><i class="fa fa-times" style="padding-right: 5px;"></i> Reset</a>
          </div>
        </div>
      </div>
    </form>

    <div class="row">
      <div class="col-sm-12">
        <div class="table-responsive">
          <table class="table table-bordered" style="margin-top: 15px;">
            <thead class="bg-warning">
              <tr>
                <th class="text-center" rowspan="2">No</th>
                <th rowspan="2">Bahasa</th>
                <th class="text-center" colspan="3">Lisan</th>
                <th class="text-center" colspan="3">Tulisan</th>
                <th rowspan="2"></th>
              </tr>
              <tr>
                <th class="text-center">Kurang</th>
                <th class="text-center">Cukup</th>
                <th class="text-center">Baik</th>
                <th class="text-center">Kurang</th>
                <th class="text-center">Cukup</th>
                <th class="text-center">Baik</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($BAHASA) > 0) {
                foreach ($BAHASA as $row) {
                  $no_bahasa = $no_bahasa + 1; ?>
                  <tr>
                    <td class="text-center"><?= $no_bahasa; ?></td>
                    <td><?= $row->BAHASA; ?></td>
                    <td class="text-center text-success"><?php if ($row->LISAN == 'kurang') echo '<i class="fa fa-check"></i>'; ?></td>
                    <td class="text-center text-success"><?php if ($row->LISAN == 'cukup') echo '<i class="fa fa-check"></i>'; ?></td>
                    <td class="text-center text-success"><?php if ($row->LISAN == 'baik') echo '<i class="fa fa-check"></i>'; ?></td>
                    <td class="text-center text-success"><?php if ($row->TULISAN == 'kurang') echo '<i class="fa fa-check"></i>'; ?></td>
                    <td class="text-center text-success"><?php if ($row->TULISAN == 'cukup') echo '<i class="fa fa-check"></i>'; ?></td>
                    <td class="text-center text-success"><?php if ($row->TULISAN == 'baik') echo '<i class="fa fa-check"></i>'; ?></td>
                    <td class="text-right">
                      <?php if ($row->APPROVED == 'PENDING') { ?>
                        <a href="dokumen-pendidikan.php?op=approve_bahasa&bahasa_karyawan_id=<?= $row->BAHASA_KARYAWAN_ID; ?>&id=<?= $row->KARYAWAN_ID; ?>" class="btn btn-success btn-xs btn-approve-formal" title="<?= empty($row->KETERANGAN_APPROVED) ? '' : 'Keterangan tidak disetujui : ' . $row->KETERANGAN_APPROVED; ?>">
                          <i class="fa fa-check-square-o"></i> Approve
                        </a>
                      <?php } ?>
                      <a href="dokumen-pendidikan.php?op=edit_bahasa&bahasa_karyawan_id=<?= $row->BAHASA_KARYAWAN_ID; ?>&id=<?= $row->KARYAWAN_ID; ?>#bahasa" class="btn btn-primary btn-xs btn-edit-formal">
                        <i class="fa fa-edit"></i> Edit
                      </a>
                      <a href="#" class="btn btn-danger btn-xs btn-delete-bahasa" data-bahasa_karyawan_id="<?= $row->BAHASA_KARYAWAN_ID; ?>" data-karyawan_id="<?= $row->KARYAWAN_ID; ?>">
                        <i class="fa fa-trash"></i> Del
                      </a>
                    </td>
                  </tr>
                <?php }
              } else { ?>
                <tr>
                  <td colspan="5" class="text-center text-warning">Data Bahasa Asing yang dikuasai masih kosong</td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
  $(document).ready(function() {
    $('#form-formal, #form-nonformal').on('submit', function() {
      $('.btn-submit').hide();
      $('.btn-reset').hide();
    });

    // formal
    <?php if ($OP == 'edit_formal') { ?>
      console.log('edit pendidikan formal karyawan...');
      let pendidikan_karyawan_id = <?= get_input('pendidikan_karyawan_id'); ?>;
      let id = <?= get_input('id'); ?>;

      $("#form-formal").attr('action', 'dokumen-pendidikan.php?op=update_formal&pendidikan_karyawan_id=' + pendidikan_karyawan_id + '&id=' + id);

      let status = $('#approved_formal').val();
      if (status == 'PENDING') {
        $('#unapproved_formal').show();
      } else {
        $('#unapproved_formal').hide();
        $('#keterangan_formal').val('');
      }

      $('#approved_formal').on('change', function() {
        let status = this.value;
        if (status == 'PENDING') {
          $('#unapproved_formal').show();
        } else {
          $('#unapproved_formal').hide();
          $('#keterangan_formal').val('');
        }
      });
    <?php } ?>

    $('.btn-delete-formal').click(function() {
      console.log('delete pendidikan formal karyawan...');
      let pendidikan_karyawan_id = $(this).data("pendidikan_karyawan_id");
      let id = $(this).data("karyawan_id");

      Swal.fire({
        title: 'Apakah Anda yakin, data akan dihapus?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
      }).then((result) => {
        if (result.isConfirmed) {
          window.location = 'dokumen-pendidikan.php?op=delete_formal&pendidikan_karyawan_id=' + pendidikan_karyawan_id + '&id=' + id;
        }
      });
      return false;
    });

    // nonformal
    <?php if ($OP == 'edit_nonformal') { ?>
      console.log('edit pendidikan non formal / kursus karyawan...');
      let kursus_karyawan_id = <?= get_input('kursus_karyawan_id'); ?>;
      let id = <?= get_input('id'); ?>;

      $("#form-nonformal").attr('action', 'dokumen-pendidikan.php?op=update_nonformal&kursus_karyawan_id=' + kursus_karyawan_id + '&id=' + id);

      let status = $('#approved_nonformal').val();
      if (status == 'PENDING') {
        $('#unapproved_nonformal').show();
      } else {
        $('#unapproved_nonformal').hide();
        $('#keterangan_formal').val('');
      }

      $('#approved_nonformal').on('change', function() {
        let status = this.value;
        if (status == 'PENDING') {
          $('#unapproved_nonformal').show();
        } else {
          $('#unapproved_nonformal').hide();
          $('#keterangan_nonformal').val('');
        }
      });
    <?php } ?>

    $('.btn-delete-nonformal').click(function() {
      console.log('delete pendidikan nonformal karyawan...');
      let kursus_karyawan_id = $(this).data("kursus_karyawan_id");
      let id = $(this).data("karyawan_id");

      Swal.fire({
        title: 'Apakah Anda yakin, data akan dihapus?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
      }).then((result) => {
        if (result.isConfirmed) {
          window.location = 'dokumen-pendidikan.php?op=delete_nonformal&kursus_karyawan_id=' + kursus_karyawan_id + '&id=' + id;
        }
      });
      return false;
    });

    // bahasa
    <?php if ($OP == 'edit_bahasa') { ?>
      console.log('edit bahasa karyawan...');
      let bahasa_karyawan_id = <?= get_input('bahasa_karyawan_id'); ?>;
      let id = <?= get_input('id'); ?>;

      $("#form-bahasa").attr('action', 'dokumen-pendidikan.php?op=update_bahasa&bahasa_karyawan_id=' + bahasa_karyawan_id + '&id=' + id);
    <?php } ?>

    $('.btn-delete-bahasa').click(function() {
      console.log('delete pendidikan bahasa karyawan...');
      let bahasa_karyawan_id = $(this).data("bahasa_karyawan_id");
      let id = $(this).data("karyawan_id");

      Swal.fire({
        title: 'Apakah Anda yakin, data akan dihapus?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
      }).then((result) => {
        if (result.isConfirmed) {
          window.location = 'dokumen-pendidikan.php?op=delete_bahasa&bahasa_karyawan_id=' + bahasa_karyawan_id + '&id=' + id;
        }
      });
      return false;
    });
  });
</script>
<?php include 'footer.php'; ?>