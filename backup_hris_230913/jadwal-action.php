<?php
include 'app-load.php';
is_login('jadwal.view');

$OP = get_input('op');

$PERIODE_ID = get_input('PERIODE_ID');
$KARYAWAN_ID = get_input('KARYAWAN_ID');
$KARYAWAN = db_first(" SELECT PROJECT_ID FROM karyawan WHERE KARYAWAN_ID = '$KARYAWAN_ID' ");
$PROJECT_ID = isset($KARYAWAN->PROJECT_ID) ? $KARYAWAN->PROJECT_ID : '';

$SHIFT_CODE = get_input('SHIFT_CODE');
$START_DATE = get_input('START_DATE');
$END_DATE = get_input('END_DATE');

if (isset($_SERVER['REQUEST_METHOD']) and strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
  $REQUIRE = array('PERIODE_ID', 'KARYAWAN_ID', 'START_DATE', 'END_DATE', 'SHIFT_CODE');
  $ERROR_REQUIRE = 0;
  foreach ($REQUIRE as $REQ) {
    $IREQ = get_input($REQ);
    if ($IREQ == "") $ERROR_REQUIRE = 1;
  }

  if ($ERROR_REQUIRE) {
    $ERROR[] = 'Kolom ' . implode(',', $REQUIRE) . ' wajib di isi.';
  } else {
    $FIELDS = array(
      'PERIODE_ID', 'KARYAWAN_ID', 'PROJECT_ID', 'SHIFT_CODE'
    );

    if ($OP == '' or $OP == 'add') {
      is_login('jadwal.add');

      $startDate = DateTime::createFromFormat("Y-m-d", $START_DATE);
      $endDate = DateTime::createFromFormat("Y-m-d", $END_DATE);

      while ($startDate <= $endDate) {
        $DATE = $startDate->format("Y-m-d");

        //echo $DATE; die();

        if (db_exists(" SELECT 1 FROM shift_karyawan WHERE KARYAWAN_ID = '$KARYAWAN_ID' AND DATE = '$DATE' ")) {
          db_execute(" DELETE FROM shift_karyawan WHERE KARYAWAN_ID='$KARYAWAN_ID' AND DATE = '$DATE' ");
        }

        db_execute(" INSERT INTO shift_karyawan (PERIODE_ID, KARYAWAN_ID, PROJECT_ID, SHIFT_CODE, DATE) 
          VALUES 
          ('$PERIODE_ID', '$KARYAWAN_ID', '$PROJECT_ID', '$SHIFT_CODE', '$DATE');
        ");
        $startDate->modify('+1 day');
      }

      $OP = 'add';
      /*
      $ID = $DB->Insert_ID();
      header('location: ' . $_SERVER['PHP_SELF'] . '?op=edit&m=1&id=' . $ID);
      */
      header('location: ' . $_SERVER['PHP_SELF'] . '?op=add&m=1');
      exit;
    }
  }
}

if (get_input('m') == '1') {
  $SUCCESS = 'Data berhasil di simpan.';
}

$JS[] = 'static/select2/js/select2.min.js';
$CSS[] = 'static/select2/css/select2.min.css';
$CSS[] = 'static/select2/css/select2-bootstrap.min.css';

$JS[] = 'static/date/jquery.plugin.min.js';
$JS[] = 'static/date/jquery.datepick.min.js';
$JS[] = 'static/date/jquery.datepick-id.js';
$CSS[] = 'static/date/jquery.datepick.css';

include 'header.php';
?>

<section class="container" style="margin-top:25px;">
  <h1 style="margin-top:0px;" class="border-title">
    <?php echo ucfirst($OP) ?> Jadwal
    <a href="jadwal.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
    <button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
    <?php if ($OP == 'edit') {
      echo '<a href="jadwal-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>';
    } ?>
  </h1>

  <?php include 'msg.php' ?>

  <div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
  <form id="form" class="form-horizontal" action="jadwal-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
    <input type="hidden" name="CURRENT_ID" value="<?php echo $ID ?>">
    <div class="row">
      <div class="col-md-8">
        <div class="form-group">
          <label for="" class="col-sm-2 control-label">Periode</label>
          <div class="col-sm-10">
            <?php echo dropdown('PERIODE_ID', periode_option(), set_value('PERIODE_ID', $EDIT->PERIODE_ID), ' id="PERIODE_ID" class="form-control" ') ?>
          </div>
        </div>
        <div class="form-group">
          <label for="" class="col-sm-2 control-label">Karyawan</label>
          <div class="col-sm-10">
            <select name="KARYAWAN_ID" id="KARYAWAN_ID" class="form-control">
              <?php
              $K = db_first(" SELECT KARYAWAN_ID,NIK,NAMA FROM karyawan WHERE KARYAWAN_ID='" . db_escape(set_value('KARYAWAN_ID', $EDIT->KARYAWAN_ID)) . "' ");
              if (isset($K->KARYAWAN_ID)) {
                echo '<option value="' . $K->KARYAWAN_ID . '" selected="selected">' . $K->NIK . ' - ' . $K->NAMA . '</option>';
              }
              ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="" class="col-sm-2 control-label">Range Tanggal</label>
          <div class="col-sm-10">
            <div class="input-group">
              <input type="text" name="START_DATE" value="<?php echo set_value('START_DATE') ?>" class="form-control datepicker2" autocomplete="off">
              <div class="input-group-addon">to</div>
              <input type="text" name="END_DATE" value="<?php echo set_value('END_DATE') ?>" class="form-control datepicker2" autocomplete="off">
            </div>
          </div>
        </div>
        <div class="form-group">
          <label for="" class="col-sm-2 control-label">Shift Code</label>

          <!-- <div class="col-sm-10">
            <select name="SHIFT_CODE" id="SHIFT_CODE" class="form-control">
              <?php
              /* $K = db_first(" SELECT SHIFT_CODE, STATUS FROM shift WHERE SHIFT_CODE='" . db_escape(set_value('SHIFT_CODE', $EDIT->SHIFT_CODE)) . "' ");
              if (isset($K->SHIFT_CODE)) {
                echo '<option value="' . $K->SHIFT_CODE . '" selected="selected">' . $K->SHIFT_CODE . ' (' . $row->STATUS . ')</option>';
              } */
              ?>
            </select>
          </div> -->

          <div class="col-sm-10">
            <div class="input-group">
              <select name="SHIFT_CODE" id="SHIFT_CODE" class="form-control">
                <?php
                $K = db_first(" SELECT SHIFT_CODE, STATUS FROM shift WHERE SHIFT_CODE='" . db_escape(set_value('SHIFT_CODE', $EDIT->SHIFT_CODE)) . "' ");
                if (isset($K->SHIFT_CODE)) {
                  echo '<option value="' . $K->SHIFT_CODE . '" selected="selected">' . $K->SHIFT_CODE . ' (' . $row->STATUS . ')</option>';
                }
                ?>
              </select>
              <span class="input-group-btn">
                <a href="shift-action.php" target="_blank" class="btn btn-info">Create New Shift Code</a>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>

    <div class="alert alert-warning" role="alert">
      <strong>Perhatian</strong> Data lama akan di replace jika terdapat data baru yang sama berdasarkan karyawan dan tanggal jadwal (yang pernah diinput).
    </div>
  </form>
</section>


<script>
  $(document).ready(function() {
    $('input').keypress(function(e) {
      if (e.which == 13) {
        e.preventDefault();
        $('#form').submit();
      }
    });

    $('#KARYAWAN_ID').select2({
      theme: "bootstrap",
      ajax: {
        url: 'karyawan-ac.php',
        dataType: 'json',
      }
    });

    $('#SHIFT_CODE').select2({
      theme: "bootstrap",
      ajax: {
        url: 'jadwal-ac.php',
        dataType: 'json',
      }
    });

    $('.datepicker2').datepick({
      dateFormat: 'yyyy-mm-dd',
      monthsToShow: 2,
      monthsOffset: 1
    });

    load_date();
    $('#PERIODE_ID').change(function() {
      load_date();
    });
  });

  function load_date() {
    $.ajax({
      url: 'periode-ajax.php',
      data: {
        'PERIODE_ID': $('#PERIODE_ID').val()
      },
      dataType: 'json',
      method: 'POST',
      success: function(r) {
        $('.datepicker2').val('');
        $('.datepicker2').datepick('option', {
          minDate: r.TANGGAL_MULAI,
          maxDate: r.TANGGAL_SELESAI2
        });
      }
    });
  }
</script>

<?php
include 'footer.php';
?>