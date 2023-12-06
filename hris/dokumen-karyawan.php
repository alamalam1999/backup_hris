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

$DOKUMEN_KARYAWAN = db_fetch(" SELECT * FROM dok_karyawan WHERE KARYAWAN_ID = '$ID' ");
$OP = get_input('op');


/* DOKUMEN KARYAWAN */
if ($OP == 'add_dokumen') {
  if (isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
    $file_akte_allow_ext = array('png', 'jpg', 'pdf');
    $file_akte_name = isset($_FILES['FILE_DOK']['name']) ? $_FILES['FILE_DOK']['name'] : '';
    $file_akte_tmp = isset($_FILES['FILE_DOK']['tmp_name']) ? $_FILES['FILE_DOK']['tmp_name'] : '';
    $file_akte_ext = strtolower(substr(strrchr($file_akte_name, "."), 1));
    $file_akte_new = 'FILE_DOK_' . $ID . '_' . rand(11111, 99999) . '_' . $file_akte_name;
    $file_akte_dest = 'uploads/karyawan/' . $file_akte_new;

    $REQUIRE = array('DOK_KARYAWAN', 'MASA_BERLAKU', 'APPROVED');
    $ERROR_REQUIRE = 0;
    $ERROR = array();

    foreach ($REQUIRE as $REQ) {
      $IREQ = get_input($REQ);
      if ($IREQ == "") {
        $ERROR_REQUIRE = 1;
      }

      if (!file_exists($_FILES["FILE_DOK"]["tmp_name"])) {
        $REQUIRE[] = 'DOKUMEN KARYAWAN';
        $ERROR_REQUIRE = 1;
      }
    }

    if ($ERROR_REQUIRE) {
      $ERROR[] = 'Kolom <strong>' . implode(', ', $REQUIRE) . '</strong> wajib di isi.';
    } else {
      $FIELDS = array(
        'DOK_KARYAWAN',
        'MASA_BERLAKU',
        'APPROVED'
      );

      $NEW_FILE_DOK = 0;
      if (is_uploaded_file($file_akte_tmp)) {
        if (move_uploaded_file($file_akte_tmp, $file_akte_dest)) {
          $FIELDS[] = 'FILE_DOK';
          $NEW_FILE_DOK = 1;
        }
      }

      foreach ($FIELDS as $F) {
        if ($F == 'FILE_DOK') {
          if ($NEW_FILE_DOK == '1') {
            $INSERT_VAL[$F] = "'" . db_escape($file_akte_new) . "'";
          }
        } else {
          $INSERT_VAL[$F] = "'" . db_escape(get_input($F)) . "'";
        }
      }

      $sql = db_execute(" INSERT INTO dok_karyawan (KARYAWAN_ID, " . implode(',', $FIELDS) . ") VALUES ('$ID', " . implode(',', $INSERT_VAL) . ") ");

      if ($sql) {
        flash('success', 'Dokumen Karyawan berhasil disimpan', FLASH_SUCCESS);
        header('location: dokumen-karyawan.php?id=' . $ID . '#dokumen');
      }

      exit;
    }
  }
}

if ($OP == 'edit_dokumen') {
  $DOK_KARYAWAN_ID = get_input('dok_karyawan_id');
  $EDIT_DOKUMEN = db_first(" SELECT * FROM dok_karyawan WHERE DOK_KARYAWAN_ID = '$DOK_KARYAWAN_ID' AND KARYAWAN_ID = '$ID' ");
}

if ($OP == 'update_dokumen') {
  $file_akte_allow_ext = array('png', 'jpg', 'pdf');
  $file_akte_name = isset($_FILES['FILE_DOK']['name']) ? $_FILES['FILE_DOK']['name'] : '';
  $file_akte_tmp = isset($_FILES['FILE_DOK']['tmp_name']) ? $_FILES['FILE_DOK']['tmp_name'] : '';
  $file_akte_ext = strtolower(substr(strrchr($file_akte_name, "."), 1));
  $file_akte_new = 'FILE_DOK_' . $ID . '_' . rand(11111, 99999) . '_' . $file_akte_name;
  $file_akte_dest = 'uploads/karyawan/' . $file_akte_new;

  $REQUIRE = array('DOK_KARYAWAN', 'MASA_BERLAKU', 'APPROVED');
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
      'DOK_KARYAWAN',
      'MASA_BERLAKU',
      'APPROVED',
      'KETERANGAN_APPROVED',
    );

    $NEW_FILE_DOK = 0;
    if (is_uploaded_file($file_akte_tmp)) {
      if (move_uploaded_file($file_akte_tmp, $file_akte_dest)) {
        $FIELDS[] = 'FILE_DOK';
        $NEW_FILE_DOK = 1;
      }
    }

    foreach ($FIELDS as $F) {
      if ($F == 'FILE_DOK') {
        if ($NEW_FILE_DOK == '1') {
          $UPDATE_VAL[$F] = $F . "='" . db_escape($file_akte_new) . "'";
        }
      } else if ($F == 'KETERANGAN_APPROVED') {
        $KETERANGAN_APPROVED = get_input('KETERANGAN_APPROVED');
        $UPDATE_VAL[$F] = $F . "='" . db_escape($KETERANGAN_APPROVED) . "'";
      } else {
        $UPDATE_VAL[$F] = $F . "='" . db_escape(get_input($F)) . "'";
      }
    }

    $DOK_KARYAWAN_ID = get_input('dok_karyawan_id');
    $ID = get_input('id');

    $sql = db_execute(" UPDATE dok_karyawan SET " . implode(',', $UPDATE_VAL) . " WHERE DOK_KARYAWAN_ID='$DOK_KARYAWAN_ID' AND KARYAWAN_ID = '$ID' ");

    if ($sql) {
      flash('success', 'Dokumen Karyawan berhasil diperbarui', FLASH_SUCCESS);
      header('location: dokumen-karyawan.php?id=' . $ID . '#dokumen');
    }

    exit;
  }
}

if ($OP == 'approve_dokumen') {
  $DOK_KARYAWAN_ID = get_input('dok_karyawan_id');
  $ID = get_input('id');
  $sql = db_execute(" UPDATE dok_karyawan SET APPROVED='APPROVED', KETERANGAN_APPROVED = '' WHERE DOK_KARYAWAN_ID = '$DOK_KARYAWAN_ID' AND KARYAWAN_ID = '$ID' ");
  if ($sql) {
    flash('success', 'Dokumen Karyawan berhasil disetujui', FLASH_SUCCESS);
    header('location: dokumen-karyawan.php?id=' . $ID . '#dokumen');
  }
  exit;
}

if ($OP == 'delete_dokumen') {
  $DOK_KARYAWAN_ID = get_input('dok_karyawan_id');
  $ID = get_input('id');
  $sql = db_execute(" DELETE FROM dok_karyawan WHERE DOK_KARYAWAN_ID = '$DOK_KARYAWAN_ID' AND KARYAWAN_ID = '$ID' ");
  if ($sql) {
    flash('success', 'Dokumen Karyawan berhasil dihapus', FLASH_SUCCESS);
    header('location: dokumen-karyawan.php?id=' . $ID . '#dokumen');
  }
  exit;
}
/* END OF DOKUMEN KARYAWAN */

$JS[] = 'static/sweetalert/sweetalert2.all.min.js';
$CSS[] = 'static/sweetalert/sweetalert2.min.css';
include 'header.php';
?>

<section class="container-fluid" style="margin-top: 25px;">
  <a href="karyawan-action.php?op=edit&id=<?= $ID; ?>" class="btn btn-warning">&laquo; Back to Form</a>

  <?php flash('success'); ?>

  <h1 style="margin-top:20px;" class="border-title">
    Dokumen Karyawan
    &nbsp;&nbsp;<span class="text-primary"><?php echo isset($EDIT->NAMA) ? strtoupper($EDIT->NAMA) : ''; ?></span>
    &nbsp;&nbsp;&nbsp;<?php echo isset($EDIT->NIK) ? '[NIK : ' . $EDIT->NIK . ']' : ''; ?>
    <?php echo isset($EDIT->KARYAWAN_ID) ? ' &nbsp;&nbsp;&nbsp; [PIN : ' . $EDIT->KARYAWAN_ID . ']' : ''; ?>
  </h1>

  <div id="dokumen" style="margin-bottom: 35px;">
    <form id="form-dokumen" class="form-horizontal" action="dokumen-karyawan.php?op=add_dokumen&id=<?= $ID; ?>" method="POST" enctype="multipart/form-data">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group bg-info text-center" style="margin: 10px 0 20px; border-bottom: 2px solid #eee;">
            <label class="control-label" style="padding-bottom: 5px;">
              DOKUMEN KARYAWAN
            </label>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label for="dok_karyawan" class="col-sm-3 control-label">Dokumen</label>
            <div class="col-sm-9">
              <?php
              echo dropdown(
                'DOK_KARYAWAN',
                array(
                  '' => '-- PILIH DOKUMEN --',
                  'SERTIFIKAT' => 'SERTIFIKAT',
                  'IJASAH' => 'IJASAH',
                  'SK TETAP' => 'SK TETAP',
                  'SK KONTRAK' => 'SK KONTRAK',
                  'SIM' => 'SIM',
                  'BUKU NIKAH' => 'BUKU NIKAH',
                  'LAINNYA' => 'LAINNYA',
                ),
                set_value('DOK_KARYAWAN', $EDIT_DOKUMEN->DOK_KARYAWAN),
                ' id="dok_karyawan" class="form-control" required'
              );
              ?>
            </div>
          </div>

          <?php
          /*
          <div class="form-group">
            <label for="judul_dokumen" class="col-sm-3 control-label">Judul Dokumen</label>
            <div class="col-sm-9">
              <input type="text" name="JUDUL_DOK" id="judul_dokumen" class="form-control" value="<?= set_value('JUDUL_DOK', $EDIT_DOKUMEN->JUDUL_DOK); ?>" required>
            </div>
          </div>
          */
          ?>

          <div class="form-group">
            <label for="file_dok" class="col-sm-3 control-label">File Dok</label>
            <div class="col-sm-9">
              <input type="file" name="FILE_DOK" id="file_dok" class="form-control">
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label for="masa_berlaku" class="col-sm-3 control-label">Masa Berlaku</label>
            <div class="col-sm-9">
              <input type="number" name="MASA_BERLAKU" id="masa_berlaku" class="form-control" value="<?= set_value('MASA_BERLAKU', $EDIT_DOKUMEN->MASA_BERLAKU); ?>" required>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <label for="approved_dokumen" class="col-sm-3 control-label">Status</label>
            <div class="col-sm-9">
              <?php
              echo dropdown(
                'APPROVED',
                array(
                  '' => '-- PILIH STATUS --',
                  'PENDING' => 'PENDING',
                  'APPROVED' => 'APPROVED',
                ),
                set_value('APPROVED', $EDIT_DOKUMEN->APPROVED),
                ' id="approved_dokumen" class="form-control" required'
              );
              ?>
            </div>
          </div>

          <?php if ($OP == 'edit_dokumen' && $DOK_KARYAWAN_ID != '') { ?>
            <div class="form-group" style="display: none;" id="unapproved_dokumen">
              <label for="keterangan_dokumen" class="col-sm-3 control-label">Unapproved</label>
              <div class="col-sm-9">
                <input type="text" name="KETERANGAN_APPROVED" id="keterangan_dokumen" class="form-control" value="<?= set_value('KETERANGAN_APPROVED', $EDIT_DOKUMEN->KETERANGAN_APPROVED); ?>">
              </div>
            </div>
          <?php } ?>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12">
          <div class="pull-right">
            <button type="submit" class="btn btn-success btn-submit"><i class="fa fa-hdd-o" style="padding-right: 5px;"></i> Simpan</button>
            <a href="dokumen-karyawan.php?id=<?= $ID ?>#dokumen" class="btn btn-warning btn-reset"><i class="fa fa-times" style="padding-right: 5px;"></i> Reset</a>
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
                <th>Dokumen</th>
                <th class="text-center">Masa Berlaku</th>
                <th class="text-center">File</th>
                <th class="text-right"></th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($DOKUMEN_KARYAWAN) > 0) {
                foreach ($DOKUMEN_KARYAWAN as $row) {
                  $no_dokumen = $no_dokumen + 1; ?>
                  <tr>
                    <td class="text-center"><?= $no_dokumen; ?></td>
                    <td><?= $row->DOK_KARYAWAN; ?></td>
                    <td class="text-center"><?= $row->MASA_BERLAKU; ?></td>
                    <td class="text-center"><a href="<?= base_url() . "uploads/karyawan/" . $row->FILE_DOK; ?>" download>Unduh</a></td>
                    <td class="text-right">
                      <?php if ($row->APPROVED == 'PENDING') { ?>
                        <a href="dokumen-karyawan.php?op=approve_dokumen&dok_karyawan_id=<?= $row->DOK_KARYAWAN_ID; ?>&id=<?= $row->KARYAWAN_ID; ?>" class="btn btn-success btn-xs btn-approve-dokumen" title="<?= empty($row->KETERANGAN_APPROVED) ? '' : 'Keterangan tidak disetujui : ' . $row->KETERANGAN_APPROVED; ?>">
                          <i class="fa fa-check-square-o"></i> Approve
                        </a>
                      <?php } ?>
                      <a href="dokumen-karyawan.php?op=edit_dokumen&dok_karyawan_id=<?= $row->DOK_KARYAWAN_ID; ?>&id=<?= $row->KARYAWAN_ID; ?>#dokumen" class="btn btn-primary btn-xs btn-edit-dokumen">
                        <i class="fa fa-edit"></i> Edit
                      </a>
                      <a href="#" class="btn btn-danger btn-xs btn-delete-dokumen" data-dok_karyawan_id="<?= $row->DOK_KARYAWAN_ID; ?>" data-karyawan_id="<?= $row->KARYAWAN_ID; ?>">
                        <i class="fa fa-trash"></i> Del
                      </a>
                    </td>
                  </tr>
                <?php }
              } else { ?>
                <tr>
                  <td colspan="9" class="text-center text-warning">Data Dokumen Karyawan masih kosong</td>
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
    $('#form-dokumen').on('submit', function() {
      $('.btn-submit').hide();
      $('.btn-reset').hide();
    });

    // dokumen
    <?php if ($OP == 'edit_dokumen') { ?>
      console.log('edit dokumen karyawan...');
      let dok_karyawan_id = <?= get_input('dok_karyawan_id'); ?>;
      let id = <?= get_input('id'); ?>;

      $("#form-dokumen").attr('action', 'dokumen-karyawan.php?op=update_dokumen&dok_karyawan_id=' + dok_karyawan_id + '&id=' + id);

      let status = $('#approved_dokumen').val();
      if (status == 'PENDING') {
        $('#unapproved_dokumen').show();
      } else {
        $('#unapproved_dokumen').hide();
        $('#keterangan_dokumen').val('');
      }

      $('#approved_dokumen').on('change', function() {
        let status = this.value;
        if (status == 'PENDING') {
          $('#unapproved_dokumen').show();
        } else {
          $('#unapproved_dokumen').hide();
          $('#keterangan_dokumen').val('');
        }
      });
    <?php } ?>

    $('.btn-delete-dokumen').click(function() {
      console.log('delete dokumen karyawan...');
      let dok_karyawan_id = $(this).data("dok_karyawan_id");
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
          window.location = 'dokumen-karyawan.php?op=delete_dokumen&dok_karyawan_id=' + dok_karyawan_id + '&id=' + id;
        }
      });
      return false;
    });
  });
</script>
<?php include 'footer.php'; ?>