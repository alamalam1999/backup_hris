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

$KELUARGA_INTI = db_fetch(" SELECT * FROM keluarga_karyawan WHERE KARYAWAN_ID = '$ID' AND JENIS_KELUARGA = 'INTI' ");
$KELUARGA_BESAR = db_fetch(" SELECT * FROM keluarga_karyawan WHERE KARYAWAN_ID = '$ID' AND JENIS_KELUARGA = 'BESAR' ");
$OP = get_input('op');


/* KELUARGA INTI */
if ($OP == 'add_inti') {
  if (isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
    $file_akte_allow_ext = array('png', 'jpg');
    $file_akte_name = isset($_FILES['FILE_AKTE']['name']) ? $_FILES['FILE_AKTE']['name'] : '';
    $file_akte_tmp = isset($_FILES['FILE_AKTE']['tmp_name']) ? $_FILES['FILE_AKTE']['tmp_name'] : '';
    $file_akte_ext = strtolower(substr(strrchr($file_akte_name, "."), 1));
    $file_akte_new = 'FILE_AKTE_' . $ID . '_' . rand(11111, 99999) . '_' . $file_akte_name;
    $file_akte_dest = 'uploads/karyawan/' . $file_akte_new;

    $REQUIRE = array('ANGGOTA_KELUARGA', 'NAMA_KELUARGA', 'GENDER', 'TP_LAHIR_KELUARGA', 'TGL_LAHIR_KELUARGA', 'PENDIDIKAN_KELUARGA', 'APPROVED');
    $ERROR_REQUIRE = 0;
    $ERROR = array();

    foreach ($REQUIRE as $REQ) {
      $IREQ = get_input($REQ);
      if ($IREQ == "") {
        $ERROR_REQUIRE = 1;
      }

      if (!file_exists($_FILES["FILE_AKTE"]["tmp_name"])) {
        $REQUIRE[] = 'DOKUMEN AKTE';
        $ERROR_REQUIRE = 1;
      }
    }

    if ($ERROR_REQUIRE) {
      $ERROR[] = 'Kolom <strong>' . implode(', ', $REQUIRE) . '</strong> wajib di isi.';
    } else {
      $FIELDS = array(
        'ANGGOTA_KELUARGA',
        'NAMA_KELUARGA',
        'GENDER',
        'TP_LAHIR_KELUARGA',
        'TGL_LAHIR_KELUARGA',
        'PENDIDIKAN_KELUARGA',
        'PEKERJAAN_KELUARGA',
        'APPROVED'
      );

      $NEW_FILE_AKTE = 0;
      if (is_uploaded_file($file_akte_tmp)) {
        if (move_uploaded_file($file_akte_tmp, $file_akte_dest)) {
          $FIELDS[] = 'FILE_AKTE';
          $NEW_FILE_AKTE = 1;
        }
      }

      foreach ($FIELDS as $F) {
        if ($F == 'FILE_AKTE') {
          if ($NEW_FILE_AKTE == '1') {
            $INSERT_VAL[$F] = "'" . db_escape($file_akte_new) . "'";
          }
        } else {
          $INSERT_VAL[$F] = "'" . db_escape(get_input($F)) . "'";
        }
      }

      $sql = db_execute(" INSERT INTO keluarga_karyawan (KARYAWAN_ID, " . implode(',', $FIELDS) . ", JENIS_KELUARGA) VALUES ('$ID', " . implode(',', $INSERT_VAL) . ",'INTI') ");

      if ($sql) {
        flash('success', 'Susunan Keluarga Inti berhasil disimpan', FLASH_SUCCESS);
        header('location: dokumen-keluarga.php?id=' . $ID . '#inti');
      }

      exit;
    }
  }
}

if ($OP == 'edit_inti') {
  $KELUARGA_KARYAWAN_ID = get_input('keluarga_karyawan_id');
  $EDIT_INTI = db_first(" SELECT * FROM keluarga_karyawan WHERE KELUARGA_KARYAWAN_ID = '$KELUARGA_KARYAWAN_ID' AND KARYAWAN_ID = '$ID' AND JENIS_KELUARGA = 'INTI' ");
}

if ($OP == 'update_inti') {

  $file_akte_allow_ext = array('png', 'jpg');
  $file_akte_name = isset($_FILES['FILE_AKTE']['name']) ? $_FILES['FILE_AKTE']['name'] : '';
  $file_akte_tmp = isset($_FILES['FILE_AKTE']['tmp_name']) ? $_FILES['FILE_AKTE']['tmp_name'] : '';
  $file_akte_ext = strtolower(substr(strrchr($file_akte_name, "."), 1));
  $file_akte_new = 'FILE_AKTE_' . $ID . '_' . rand(11111, 99999) . '_' . $file_akte_name;
  $file_akte_dest = 'uploads/karyawan/' . $file_akte_new;

  $REQUIRE = array('ANGGOTA_KELUARGA', 'NAMA_KELUARGA', 'GENDER', 'TP_LAHIR_KELUARGA', 'TGL_LAHIR_KELUARGA', 'PENDIDIKAN_KELUARGA', 'APPROVED');
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
      'ANGGOTA_KELUARGA',
      'NAMA_KELUARGA',
      'GENDER',
      'TP_LAHIR_KELUARGA',
      'TGL_LAHIR_KELUARGA',
      'PENDIDIKAN_KELUARGA',
      'PEKERJAAN_KELUARGA',
      'APPROVED',
      'KETERANGAN_APPROVED',
    );

    $NEW_FILE_AKTE = 0;
    if (is_uploaded_file($file_akte_tmp)) {
      if (move_uploaded_file($file_akte_tmp, $file_akte_dest)) {
        $FIELDS[] = 'FILE_AKTE';
        $NEW_FILE_AKTE = 1;
      }
    }

    foreach ($FIELDS as $F) {
      if ($F == 'FILE_AKTE') {
        if ($NEW_FILE_AKTE == '1') {
          $UPDATE_VAL[$F] = $F . "='" . db_escape($file_akte_new) . "'";
        }
      } else if ($F == 'KETERANGAN_APPROVED') {
        $KETERANGAN_APPROVED = get_input('KETERANGAN_APPROVED');
        $UPDATE_VAL[$F] = $F . "='" . db_escape($KETERANGAN_APPROVED) . "'";
      } else {
        $UPDATE_VAL[$F] = $F . "='" . db_escape(get_input($F)) . "'";
      }
    }

    $KELUARGA_KARYAWAN_ID = get_input('keluarga_karyawan_id');
    $ID = get_input('id');

    $sql = db_execute(" UPDATE keluarga_karyawan SET " . implode(',', $UPDATE_VAL) . " WHERE KELUARGA_KARYAWAN_ID='$KELUARGA_KARYAWAN_ID' AND KARYAWAN_ID = '$ID' ");

    if ($sql) {
      flash('success', 'Susunan Keluarga Inti berhasil diperbarui', FLASH_SUCCESS);
      header('location: dokumen-keluarga.php?id=' . $ID . '#inti');
    }

    exit;
  }
}

if ($OP == 'approve_inti') {
  $KELUARGA_KARYAWAN_ID = get_input('keluarga_karyawan_id');
  $ID = get_input('id');
  $sql = db_execute(" UPDATE keluarga_karyawan SET APPROVED='APPROVED', KETERANGAN_APPROVED = '' WHERE KELUARGA_KARYAWAN_ID = '$KELUARGA_KARYAWAN_ID' AND KARYAWAN_ID = '$ID' AND JENIS_KELUARGA = 'INTI' ");
  if ($sql) {
    flash('success', 'Susunan Keluarga Inti berhasil disetujui', FLASH_SUCCESS);
    header('location: dokumen-keluarga.php?id=' . $ID . '#inti');
  }
  exit;
}

if ($OP == 'delete_inti') {
  $KELUARGA_KARYAWAN_ID = get_input('keluarga_karyawan_id');
  $ID = get_input('id');
  $sql = db_execute(" DELETE FROM keluarga_karyawan WHERE KELUARGA_KARYAWAN_ID = '$KELUARGA_KARYAWAN_ID' AND KARYAWAN_ID = '$ID' AND JENIS_KELUARGA = 'INTI' ");
  if ($sql) {
    flash('success', 'Susunan Keluarga Inti berhasil dihapus', FLASH_SUCCESS);
    header('location: dokumen-keluarga.php?id=' . $ID . '#inti');
  }
  exit;
}
/* END OF KELUARGA INTI */

/* KELUARGA BESAR */
if ($OP == 'add_besar') {
  if (isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
    $REQUIRE = array('ANGGOTA_KELUARGA', 'NAMA_KELUARGA', 'GENDER', 'TP_LAHIR_KELUARGA', 'TGL_LAHIR_KELUARGA', 'PENDIDIKAN_KELUARGA', 'APPROVED');
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
        'ANGGOTA_KELUARGA',
        'NAMA_KELUARGA',
        'GENDER',
        'TP_LAHIR_KELUARGA',
        'TGL_LAHIR_KELUARGA',
        'PENDIDIKAN_KELUARGA',
        'PEKERJAAN_KELUARGA',
        'APPROVED'
      );

      foreach ($FIELDS as $F) {
        $INSERT_VAL[$F] = "'" . db_escape(get_input($F)) . "'";
      }

      $sql = db_execute(" INSERT INTO keluarga_karyawan (KARYAWAN_ID, " . implode(',', $FIELDS) . ", JENIS_KELUARGA) VALUES ('$ID', " . implode(',', $INSERT_VAL) . ", 'BESAR') ");

      if ($sql) {
        flash('success', 'Susunan Keluarga Besar berhasil disimpan', FLASH_SUCCESS);
        header('location: dokumen-keluarga.php?id=' . $ID . '#besar');
      }

      exit;
    }
  }
}

if ($OP == 'edit_besar') {
  $KELUARGA_KARYAWAN_ID = get_input('keluarga_karyawan_id');
  $EDIT_BESAR = db_first(" SELECT * FROM keluarga_karyawan WHERE KELUARGA_KARYAWAN_ID = '$KELUARGA_KARYAWAN_ID' AND KARYAWAN_ID = '$ID' AND JENIS_KELUARGA = 'BESAR' ");
}

if ($OP == 'update_besar') {
  $REQUIRE = array('ANGGOTA_KELUARGA', 'NAMA_KELUARGA', 'GENDER', 'TP_LAHIR_KELUARGA', 'TGL_LAHIR_KELUARGA', 'PENDIDIKAN_KELUARGA', 'APPROVED');
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
      'ANGGOTA_KELUARGA',
      'NAMA_KELUARGA',
      'GENDER',
      'TP_LAHIR_KELUARGA',
      'TGL_LAHIR_KELUARGA',
      'PENDIDIKAN_KELUARGA',
      'PEKERJAAN_KELUARGA',
      'APPROVED'
    );

    foreach ($FIELDS as $F) {
      $UPDATE_VAL[$F] = $F . "='" . db_escape(get_input($F)) . "'";
    }

    $KELUARGA_KARYAWAN_ID = get_input('keluarga_karyawan_id');
    $ID = get_input('id');

    $sql = db_execute(" UPDATE keluarga_karyawan SET " . implode(',', $UPDATE_VAL) . " WHERE KELUARGA_KARYAWAN_ID='$KELUARGA_KARYAWAN_ID' AND KARYAWAN_ID = '$ID' AND JENIS_KELUARGA = 'BESAR' ");

    if ($sql) {
      flash('success', 'Susunan Keluarga Besar berhasil diperbarui', FLASH_SUCCESS);
      header('location: dokumen-keluarga.php?id=' . $ID . '#besar');
    }

    exit;
  }
}

if ($OP == 'approve_besar') {
  $KELUARGA_KARYAWAN_ID = get_input('keluarga_karyawan_id');
  $ID = get_input('id');
  $sql = db_execute(" UPDATE keluarga_karyawan SET APPROVED='APPROVED' WHERE KELUARGA_KARYAWAN_ID = '$KELUARGA_KARYAWAN_ID' AND KARYAWAN_ID = '$ID' AND JENIS_KELUARGA = 'BESAR' ");
  if ($sql) {
    flash('success', 'Susunan Keluarga Besar berhasil disetujui', FLASH_SUCCESS);
    header('location: dokumen-keluarga.php?id=' . $ID . '#besar');
  }
  exit;
}

if ($OP == 'delete_besar') {
  $KELUARGA_KARYAWAN_ID = get_input('keluarga_karyawan_id');
  $ID = get_input('id');
  $sql = db_execute(" DELETE FROM keluarga_karyawan WHERE KELUARGA_KARYAWAN_ID = '$KELUARGA_KARYAWAN_ID' AND KARYAWAN_ID = '$ID' AND JENIS_KELUARGA = 'BESAR' ");
  if ($sql) {
    flash('success', 'Susunan Keluarga Besar berhasil dihapus', FLASH_SUCCESS);
    header('location: dokumen-keluarga.php?id=' . $ID . '#besar');
  }
  exit;
}
/* END OF KELUARGA BESAR */

$JS[] = 'static/sweetalert/sweetalert2.all.min.js';
$CSS[] = 'static/sweetalert/sweetalert2.min.css';
include 'header.php';
?>

<section class="container-fluid" style="margin-top: 25px;">
  <a href="karyawan-action.php?op=edit&id=<?= $ID; ?>" class="btn btn-warning">&laquo; Back to Form</a>

  <?php flash('success'); ?>

  <h1 style="margin-top:20px;" class="border-title">
    Dokumen Keluarga
    &nbsp;&nbsp;<span class="text-primary"><?php echo isset($EDIT->NAMA) ? strtoupper($EDIT->NAMA) : ''; ?></span>
    &nbsp;&nbsp;&nbsp;<?php echo isset($EDIT->NIK) ? '[NIK : ' . $EDIT->NIK . ']' : ''; ?>
    <?php echo isset($EDIT->KARYAWAN_ID) ? ' &nbsp;&nbsp;&nbsp; [PIN : ' . $EDIT->KARYAWAN_ID . ']' : ''; ?>
  </h1>

  <div id="inti" style="margin-bottom: 35px;">
    <form id="form-inti" class="form-horizontal" action="dokumen-keluarga.php?op=add_inti&id=<?= $ID; ?>" method="POST" enctype="multipart/form-data">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group bg-info text-center" style="margin: 10px 0 20px; border-bottom: 2px solid #eee;">
            <label class="control-label" style="padding-bottom: 5px;">
              SUSUNAN KELUARGA INTI (ISTRI / SUAMI DAN ANAK-ANAK)
            </label>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label for="anggota_keluarga_inti" class="col-sm-3 control-label">Anggota</label>
            <div class="col-sm-9">
              <?php
              echo dropdown(
                'ANGGOTA_KELUARGA',
                array(
                  '' => '-- PILIH ANGGOTA KELUARGA --',
                  'SUAMI' => 'SUAMI',
                  'ISTRI' => 'ISTRI',
                  'ANAK1' => 'ANAK 1',
                  'ANAK2' => 'ANAK 2',
                  'ANAK3' => 'ANAK 3',
                ),
                set_value('ANGGOTA_KELUARGA', $EDIT_INTI->ANGGOTA_KELUARGA),
                ' id="anggota_keluarga_inti" class="form-control" required'
              );
              ?>
            </div>
          </div>

          <div class="form-group">
            <label for="nama_inti" class="col-sm-3 control-label">Nama</label>
            <div class="col-sm-9">
              <input type="text" name="NAMA_KELUARGA" id="nama_inti" class="form-control" value="<?= set_value('NAMA_KELUARGA', $EDIT_INTI->NAMA_KELUARGA); ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label for="gender_inti" class="col-sm-3 control-label">Gender</label>
            <div class="col-sm-9">
              <?php
              echo dropdown(
                'GENDER',
                array(
                  '' => '-- PILIH GENDER --',
                  'L' => 'LAKI-LAKI',
                  'P' => 'PEREMPUAN',
                ),
                set_value('GENDER', $EDIT_INTI->GENDER),
                ' id="gender_inti" class="form-control" required'
              );
              ?>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label for="tempat_lahir_inti" class="col-sm-3 control-label">Tempat Lahir</label>
            <div class="col-sm-9">
              <input type="text" name="TP_LAHIR_KELUARGA" id="tempat_lahir_inti" class="form-control" value="<?= set_value('TP_LAHIR_KELUARGA', $EDIT_INTI->TP_LAHIR_KELUARGA); ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label for="tgl_lahir_inti" class="col-sm-3 control-label">Tanggal Lahir</label>
            <div class="col-sm-9">
              <input type="date" name="TGL_LAHIR_KELUARGA" id="tgl_lahir_inti" class="form-control" value="<?= set_value('TGL_LAHIR_KELUARGA', $EDIT_INTI->TGL_LAHIR_KELUARGA); ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label for="pendidikan_inti" class="col-sm-3 control-label">Pendidikan</label>
            <div class="col-sm-9">
              <input type="text" name="PENDIDIKAN_KELUARGA" id="pendidikan_inti" class="form-control" value="<?= set_value('PENDIDIKAN_KELUARGA', $EDIT_INTI->PENDIDIKAN_KELUARGA); ?>" required>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label for="pekerjaan_inti" class="col-sm-3 control-label">Pekerjaan</label>
            <div class="col-sm-9">
              <input type="text" name="PEKERJAAN_KELUARGA" id="pekerjaan_inti" class="form-control" value="<?= set_value('PEKERJAAN_KELUARGA', $EDIT_INTI->PEKERJAAN_KELUARGA); ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label for="file_akte" class="col-sm-3 control-label">Akte</label>
            <div class="col-sm-9">
              <input type="file" name="FILE_AKTE" id="file_akte" class="form-control">
            </div>
          </div>

          <div class="form-group">
            <label for="approved_inti" class="col-sm-3 control-label">Status</label>
            <div class="col-sm-9">
              <?php
              echo dropdown(
                'APPROVED',
                array(
                  '' => '-- PILIH STATUS --',
                  'PENDING' => 'PENDING',
                  'APPROVED' => 'APPROVED',
                ),
                set_value('APPROVED', $EDIT_INTI->APPROVED),
                ' id="approved_inti" class="form-control" required'
              );
              ?>
            </div>
          </div>

          <?php if ($OP == 'edit_inti' && $KELUARGA_KARYAWAN_ID != '') { ?>
            <div class="form-group" style="display: none;" id="unapproved_inti">
              <label for="keterangan_inti" class="col-sm-3 control-label">Unapproved</label>
              <div class="col-sm-9">
                <input type="text" name="KETERANGAN_APPROVED" id="keterangan_inti" class="form-control" value="<?= set_value('KETERANGAN_APPROVED', $EDIT_INTI->KETERANGAN_APPROVED); ?>">
              </div>
            </div>
          <?php } ?>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12">
          <div class="pull-right">
            <button type="submit" class="btn btn-success btn-submit"><i class="fa fa-hdd-o" style="padding-right: 5px;"></i> Simpan</button>
            <a href="dokumen-keluarga.php?id=<?= $ID ?>#inti" class="btn btn-warning btn-reset"><i class="fa fa-times" style="padding-right: 5px;"></i> Reset</a>
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
                <th>Anggota</th>
                <th>Nama</th>
                <th class="text-center">Gender</th>
                <th>Tempat Lahir</th>
                <th class="text-center">Tgl Lahir</th>
                <th>Pendidikan</th>
                <th>Pekerjaan</th>
                <th class="text-center">Akte</th>
                <th class="text-right"></th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($KELUARGA_INTI) > 0) {
                foreach ($KELUARGA_INTI as $row) {
                  $no_inti = $no_inti + 1; ?>
                  <tr>
                    <td class="text-center"><?= $no_inti; ?></td>
                    <td><?= $row->ANGGOTA_KELUARGA; ?></td>
                    <td><?= $row->NAMA_KELUARGA; ?></td>
                    <td class="text-center"><?= $row->GENDER; ?></td>
                    <td><?= $row->TP_LAHIR_KELUARGA; ?></td>
                    <td class="text-center"><?= $row->TGL_LAHIR_KELUARGA; ?></td>
                    <td><?= $row->PENDIDIKAN_KELUARGA; ?></td>
                    <td><?= $row->PEKERJAAN_KELUARGA; ?></td>
                    <td class="text-center"><a href="<?= base_url() . "uploads/karyawan/" . $row->FILE_AKTE; ?>" download>Unduh</a></td>
                    <td class="text-right">
                      <?php if ($row->APPROVED == 'PENDING') { ?>
                        <a href="dokumen-keluarga.php?op=approve_inti&keluarga_karyawan_id=<?= $row->KELUARGA_KARYAWAN_ID; ?>&id=<?= $row->KARYAWAN_ID; ?>" class="btn btn-success btn-xs btn-approve-inti" title="<?= empty($row->KETERANGAN_APPROVED) ? '' : 'Keterangan tidak disetujui : ' . $row->KETERANGAN_APPROVED; ?>">
                          <i class="fa fa-check-square-o"></i> Approve
                        </a>
                      <?php } ?>
                      <a href="dokumen-keluarga.php?op=edit_inti&keluarga_karyawan_id=<?= $row->KELUARGA_KARYAWAN_ID; ?>&id=<?= $row->KARYAWAN_ID; ?>#inti" class="btn btn-primary btn-xs btn-edit-inti">
                        <i class="fa fa-edit"></i> Edit
                      </a>
                      <a href="#" class="btn btn-danger btn-xs btn-delete-inti" data-keluarga_karyawan_id="<?= $row->KELUARGA_KARYAWAN_ID; ?>" data-karyawan_id="<?= $row->KARYAWAN_ID; ?>">
                        <i class="fa fa-trash"></i> Del
                      </a>
                    </td>
                  </tr>
                <?php }
              } else { ?>
                <tr>
                  <td colspan="9" class="text-center text-warning">Data Keluarga Inti Karyawan masih kosong</td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div id="besar" style="margin-bottom: 35px;">
    <form id="form-besar" class="form-horizontal" action="dokumen-keluarga.php?op=add_besar&id=<?= $ID; ?>" method="POST" enctype="multipart/form-data">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group bg-info text-center" style="margin: 10px 0 20px; border-bottom: 2px solid #eee;">
            <label class="control-label" style="padding-bottom: 5px;">
              SUSUNAN KELUARGA BESAR (AYAH, IBU, SAUDARA KANDUNG, TERMASUK ANDA)
            </label>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label for="anggota_keluarga_besar" class="col-sm-3 control-label">Anggota</label>
            <div class="col-sm-9">
              <?php
              echo dropdown(
                'ANGGOTA_KELUARGA',
                array(
                  '' => '-- PILIH ANGGOTA KELUARGA --',
                  'AYAH' => 'AYAH',
                  'IBU' => 'IBU',
                  'ANAK1' => 'ANAK 1',
                  'ANAK2' => 'ANAK 2',
                  'ANAK3' => 'ANAK 3',
                  'ANAK4' => 'ANAK 4',
                  'ANAK5' => 'ANAK 5',
                ),
                set_value('ANGGOTA_KELUARGA', $EDIT_BESAR->ANGGOTA_KELUARGA),
                ' id="anggota_keluarga_besar" class="form-control" required'
              );
              ?>
            </div>
          </div>

          <div class="form-group">
            <label for="nama_besar" class="col-sm-3 control-label">Nama</label>
            <div class="col-sm-9">
              <input type="text" name="NAMA_KELUARGA" id="nama_besar" class="form-control" value="<?= set_value('NAMA_KELUARGA', $EDIT_BESAR->NAMA_KELUARGA); ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label for="gender_besar" class="col-sm-3 control-label">Gender</label>
            <div class="col-sm-9">
              <?php
              echo dropdown(
                'GENDER',
                array(
                  '' => '-- PILIH GENDER --',
                  'L' => 'LAKI-LAKI',
                  'P' => 'PEREMPUAN',
                ),
                set_value('GENDER', $EDIT_BESAR->GENDER),
                ' id="gender_besar" class="form-control" required'
              );
              ?>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label for="tempat_lahir_besar" class="col-sm-3 control-label">Tempat Lahir</label>
            <div class="col-sm-9">
              <input type="text" name="TP_LAHIR_KELUARGA" id="tempat_lahir_besar" class="form-control" value="<?= set_value('TP_LAHIR_KELUARGA', $EDIT_BESAR->TP_LAHIR_KELUARGA); ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label for="tgl_lahir_besar" class="col-sm-3 control-label">Tanggal Lahir</label>
            <div class="col-sm-9">
              <input type="date" name="TGL_LAHIR_KELUARGA" id="tgl_lahir_besar" class="form-control" value="<?= set_value('TGL_LAHIR_KELUARGA', $EDIT_BESAR->TGL_LAHIR_KELUARGA); ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label for="pendidikan_besar" class="col-sm-3 control-label">Pendidikan</label>
            <div class="col-sm-9">
              <input type="text" name="PENDIDIKAN_KELUARGA" id="pendidikan_besar" class="form-control" value="<?= set_value('PENDIDIKAN_KELUARGA', $EDIT_BESAR->PENDIDIKAN_KELUARGA); ?>" required>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label for="pekerjaan_besar" class="col-sm-3 control-label">Pekerjaan</label>
            <div class="col-sm-9">
              <input type="text" name="PEKERJAAN_KELUARGA" id="pekerjaan_besar" class="form-control" value="<?= set_value('PEKERJAAN_KELUARGA', $EDIT_BESAR->PEKERJAAN_KELUARGA); ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label for="approved_besar" class="col-sm-3 control-label">Status</label>
            <div class="col-sm-9">
              <?php
              echo dropdown(
                'APPROVED',
                array(
                  '' => '-- PILIH STATUS --',
                  'PENDING' => 'PENDING',
                  'APPROVED' => 'APPROVED',
                ),
                set_value('APPROVED', $EDIT_BESAR->APPROVED),
                ' id="approved_besar" class="form-control" required'
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
            <a href="dokumen-keluarga.php?id=<?= $ID ?>#besar" class="btn btn-warning btn-reset"><i class="fa fa-times" style="padding-right: 5px;"></i> Reset</a>
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
                <th>Anggota</th>
                <th>Nama</th>
                <th class="text-center">Gender</th>
                <th>Tempat Lahir</th>
                <th class="text-center">Tgl Lahir</th>
                <th>Pendidikan</th>
                <th>Pekerjaan</th>
                <th class="text-right"></th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($KELUARGA_BESAR) > 0) {
                foreach ($KELUARGA_BESAR as $row) {
                  $no_besar = $no_besar + 1; ?>
                  <tr>
                    <td class="text-center"><?= $no_besar; ?></td>
                    <td><?= $row->ANGGOTA_KELUARGA; ?></td>
                    <td><?= $row->NAMA_KELUARGA; ?></td>
                    <td class="text-center"><?= $row->GENDER; ?></td>
                    <td><?= $row->TP_LAHIR_KELUARGA; ?></td>
                    <td class="text-center"><?= $row->TGL_LAHIR_KELUARGA; ?></td>
                    <td><?= $row->PENDIDIKAN_KELUARGA; ?></td>
                    <td><?= $row->PEKERJAAN_KELUARGA; ?></td>
                    <td class="text-right">
                      <?php if ($row->APPROVED == 'PENDING') { ?>
                        <a href="dokumen-keluarga.php?op=approve_besar&keluarga_karyawan_id=<?= $row->KELUARGA_KARYAWAN_ID; ?>&id=<?= $row->KARYAWAN_ID; ?>" class="btn btn-success btn-xs btn-approve-besar" title="<?= empty($row->KETERANGAN_APPROVED) ? '' : 'Keterangan tidak disetujui : ' . $row->KETERANGAN_APPROVED; ?>">
                          <i class="fa fa-check-square-o"></i> Approve
                        </a>
                      <?php } ?>
                      <a href="dokumen-keluarga.php?op=edit_besar&keluarga_karyawan_id=<?= $row->KELUARGA_KARYAWAN_ID; ?>&id=<?= $row->KARYAWAN_ID; ?>#besar" class="btn btn-primary btn-xs btn-edit-besar">
                        <i class="fa fa-edit"></i> Edit
                      </a>
                      <a href="#" class="btn btn-danger btn-xs btn-delete-besar" data-keluarga_karyawan_id="<?= $row->KELUARGA_KARYAWAN_ID; ?>" data-karyawan_id="<?= $row->KARYAWAN_ID; ?>">
                        <i class="fa fa-trash"></i> Del
                      </a>
                    </td>
                  </tr>
                <?php }
              } else { ?>
                <tr>
                  <td colspan="9" class="text-center text-warning">Data Keluarga Besar Karyawan masih kosong</td>
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
    $('#form-inti, #form-besar').on('submit', function() {
      $('.btn-submit').hide();
      $('.btn-reset').hide();
    });

    // inti
    <?php if ($OP == 'edit_inti') { ?>
      console.log('edit keluarga inti karyawan...');
      let keluarga_karyawan_id = <?= get_input('keluarga_karyawan_id'); ?>;
      let id = <?= get_input('id'); ?>;

      $("#form-inti").attr('action', 'dokumen-keluarga.php?op=update_inti&keluarga_karyawan_id=' + keluarga_karyawan_id + '&id=' + id);

      let status = $('#approved_inti').val();
      if (status == 'PENDING') {
        $('#unapproved_inti').show();
      } else {
        $('#unapproved_inti').hide();
        $('#keterangan_inti').val('');
      }

      $('#approved_inti').on('change', function() {
        let status = this.value;
        if (status == 'PENDING') {
          $('#unapproved_inti').show();
        } else {
          $('#unapproved_inti').hide();
          $('#keterangan_inti').val('');
        }
      });
    <?php } ?>

    $('.btn-delete-inti').click(function() {
      console.log('delete keluarga inti karyawan...');
      let keluarga_karyawan_id = $(this).data("keluarga_karyawan_id");
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
          window.location = 'dokumen-keluarga.php?op=delete_inti&keluarga_karyawan_id=' + keluarga_karyawan_id + '&id=' + id;
        }
      });
      return false;
    });

    // besar
    <?php if ($OP == 'edit_besar') { ?>
      console.log('edit keluarga besar karyawan...');
      let keluarga_karyawan_id = <?= get_input('keluarga_karyawan_id'); ?>;
      let id = <?= get_input('id'); ?>;

      $("#form-besar").attr('action', 'dokumen-keluarga.php?op=update_besar&keluarga_karyawan_id=' + keluarga_karyawan_id + '&id=' + id);
    <?php } ?>

    $('.btn-delete-besar').click(function() {
      console.log('delete pendidikan besar karyawan...');
      let keluarga_karyawan_id = $(this).data("keluarga_karyawan_id");
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
          window.location = 'dokumen-keluarga.php?op=delete_besar&keluarga_karyawan_id=' + keluarga_karyawan_id + '&id=' + id;
        }
      });
      return false;
    });
  });
</script>
<?php include 'footer.php'; ?>