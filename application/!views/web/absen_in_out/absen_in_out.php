<!-- Main Content -->
<div class="main-content">
  <section class="section">
    <div class="card shadow">
      <?php if (!empty($data)) { ?>
        <div class="col-md-12 mt-4">
          <p class="title-header col-md-12 text-danger font-weight-bold" style="height: 10px !important; font-size: 28px !important;"><?= $data->NAMA ?></p>
        </div>
        <div class="col-md-12 mt-0 pt-0">
          <p class="col-md-12" style="height: 10px !important;"><?= $data->JABATAN ?></p>
        </div>
      <?php } ?>

      <div class="card-body">
        <?php if (!empty($data)) { ?>
          <div class="card-body">
            <div class="card card-danger">
              <div class="row mb-2 mt-4">
                <div class="col-md-12 col-sm-12 ">
                  <label class="col-md-12 col-form-label pr-0 pt-0">
                    <h5>Jadwal Kerja ( <?= date_indo($data->DATE, 'd-M-Y') ?> )</h5>
                  </label>
                </div>
              </div>

              <div class="row mb-2 mt-0">
                <div class="col-md-12 col-sm-12 ">
                  <h4 class="text-dark text-center"><?= $data->START_TIME ?> - <?= $data->FINISH_TIME ?></h4>
                </div>
                <div class="col-md-12 col-sm-12 ">
                  <h4 class="text-center text-sm"><?= ucfirst($data->STATUS) ?></h4>
                </div>
              </div>

              <hr>

              <div class="row mt-0 mb-4">
                <div class="col-md-12 col-sm-12 text-center mb-2">
                  <?php
                  $check_time = check_time_range($data->DATE, $data->START_TIME, $data->START_BEGIN, $data->START_END);
                  if ($check_time) {
                    $date = date('Y-m-d');

                    $Q = "SELECT LOG_ONLINE_ID 
                      FROM log_online  
                      WHERE PIN = '$data->KARYAWAN_ID' AND DATE(TANGGAL_ABSEN) = '$date' AND JENIS_ABSEN = 'IN'
                    ";

                    $check_in_absent = $this->crud->set_query($Q)->result();
                    if (!$check_in_absent) {
                      echo "<a href='" . site_url("absen_in_out/attendance/checkin") . "' class='btn btn-success'>Check In</a>";
                    } else {
                      echo "<i class='badge badge-success'>Anda sudah melakukan absen masuk</i>";
                    }
                  }
                  ?>
                </div>

                <div class="col-md-12 col-sm-12 col-sm-12 text-center mb-2">
                  <?php
                  $check_time = check_time_range($data->DATE, $data->FINISH_TIME, $data->FINISH_BEGIN, $data->FINISH_END);
                  if ($check_time) {
                    $date = date('Y-m-d');

                    $Q = "SELECT LOG_ONLINE_ID 
                      FROM log_online 
                      WHERE PIN = '$data->KARYAWAN_ID' AND DATE(TANGGAL_ABSEN) = '$date' AND JENIS_ABSEN = 'OUT'
                    ";

                    $check_in_absent = $this->crud->set_query($Q)->result();
                    if (!$check_in_absent){
                      echo "<a href='" . site_url("absen_in_out/attendance/checkout") . "' class='btn btn-danger'>Check Out</a>";
                    } else {
                      echo "<i class='badge badge-info'>Sudah absen pulang</i>";
                    }
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>

        <?php } else { ?>
          <strong>Anda tidak memiliki jadwal untuk saat ini</strong>
        <?php } ?>
      </div>
    </div>
  </section>
</div>