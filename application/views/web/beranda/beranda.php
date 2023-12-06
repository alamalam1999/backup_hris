<!-- Main Content -->
<div class="main-content">
  <section class="section">
    <div class="row">

      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <a href="<?php echo site_url('absen_in_out') ?>">
          <div class="card card-statistic-1 rounded-curve">
            <div class="card-icon bg-danger rounded-circle card-curve">

              <i class="far fa-clock pt-3 pr-1"></i>
            </div>
            <div class="card-wrap">
              <div class="card-header">
                <h6 class="title-header text-danger">Absen</h6>
              </div>
            </div>
          </div>
        </a>
      </div>

      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <a href="<?php echo site_url('jadwal') ?>">
          <div class="card card-statistic-1 rounded-curve">
            <div class="card-icon bg-success rounded-circle card-curve">
              <i class="far fa-calendar-alt pt-3 pr-1"></i>
            </div>
            <div class="card-wrap">
              <div class="card-header">
                <h6 class="title-header text-success">Jadwal</h6>
              </div>
            </div>
          </div>
        </a>
      </div>

      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <a href="<?php echo site_url('penggajian') ?>">
          <div class="card card-statistic-1 rounded-curve">
            <div class="card-icon bg-primary rounded-circle card-curve">
              <i class="far fa-money-bill-alt pt-3 pr-1"></i>
            </div>
            <div class="card-wrap">
              <div class="card-header">
                <h6 class="title-header">Penggajian</h6>
              </div>
            </div>
          </div>
        </a>
      </div>

      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <a href="<?php echo site_url('profile') ?>">
          <div class="card card-statistic-1 rounded-curve">
            <div class="card-icon bg-warning rounded-circle card-curve">
              <i class="far fa-user pt-3 pr-1"></i>
            </div>
            <div class="card-wrap">
              <div class="card-header">
                <h6 class="title-header text-warning">Profile</h6>
              </div>
            </div>
          </div>
        </a>
      </div>

    </div>
    <div class="row">

      <div class="col-md-7">
        <div class="card shadow" style="min-height: 477px;">
          <div class="card-header">
            <h4 class="text-warning">Data Absensi</h4>
            <div class="card-header-action">
              <!-- 
              <form  action="<?php /* echo site_url("absensi") */ ?>" id="myFrom" method="post">
                <div class="form-group">
                  <?php
                  /* 
                  echo dropdown('PERIODE_ID', dropdown_option('periode', 'PERIODE_ID', 'PERIODE', 'ORDER BY PERIODE_ID DESC'), $periode, ' id="PERIODE_ID" class="form-control" '); 
                  */
                  ?>
                </div>
              </form> 
              -->
              <div>
                <a href="<?php echo site_url('absensi'); ?>" class="btn btn-warning mr-2">View More <i class="fas fa-chevron-right"></i></a>
              </div>
            </div>
          </div>
          <div class="card-body">

            <div id="t-responsive" class="table-responsive">
              <table id="tabel_hadir" class="table table-sm table-bordered table-striped" style="font-size: 12px;">
                <thead>
                  <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Persetujuan</th>
                    <th>Tgl Jadwal</th>
                    <th>Tgl Absen</th>
                    <th class="text-center">Jenis</th>
                    <th class="text-center">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  foreach ($absen as $key => $row) {
                    if ($row->APPROVE == 0) {
                      $setuju = "<i class='badge badge-warning'>Proses</i>";
                    } else if ($row->APPROVE == 1) {
                      $setuju = "<i class='badge badge-success'>Disetujui</i>";
                    } else if ($row->APPROVE == 2) {
                      $setuju = "<i class='badge badge-danger'>Ditolak</i>";
                    }

                    if ($row->JENIS == 1) {
                      $jenis = "Masuk";
                    } else if ($row->JENIS == 2) {
                      $jenis = "Keluar";
                    } else {
                      $jenis = "-";
                    }

                    if ($row->STATUS == 1) {
                      if ($row->JENIS_EKSEPSI == 'IJIN_LATE') {
                        $status = "<strong class='text-success'>HADIR - IJIN LATE/EARLY</strong>";
                      } else if ($row->JENIS_EKSEPSI == 'DINAS') {
                        $status = "<strong class='text-success'>HADIR - DINAS</strong>";
                      } else if ($row->JENIS_EKSEPSI == 'SM') {
                        $status = "<strong class='text-success'>HADIR - SCAN MANUAL</strong>";
                      } else {
                        $status = "<strong class='text-success'>HADIR</strong>";
                      }
                    } else if ($row->STATUS == 0){
                      if ($row->JENIS_EKSEPSI == 'SAKIT') {
                        $status = "<strong class='text-warning'>SAKIT</strong>";
                      } else if ($row->JENIS_EKSEPSI == 'IJIN') {
                        $status = "<strong class='text-warning'>IJIN</strong>";
                      } else if ($row->JENIS_EKSEPSI == 'SKD') {
                        $status = "<strong class='text-warning'>SKD</strong>";
                      } else {
                        $status = "<strong class='text-danger'>ABSEN</strong>";
                      }
                    }

                    if (!empty($row->FOTO) and url_exists(hris('url') . 'uploads/absen/' . rawurlencode($row->FOTO))) {
                      $file = "<a href='" . hris('url') . 'uploads/absen/' . rawurlencode($row->FOTO) . "' class='btn btn-warning btn-sm' target='_blank' download><i class='fa fa-photo'></i> Foto</a>";
                    } else{
                      $file = "-";
                    } 

                    echo "
                      <tr>
                        <td class='text-center'>" . $no++ . "</td>
                        <td class='text-center'>$setuju</td>
                        <td>" . date_indo($row->TGL_JADWAL, 'd-M-Y') . "</td>
                        <td>" . date_indo_full($row->TGL_ABSEN, 'd-M-Y H:i') . "</td>
                        <td class='text-center'>$jenis</td>
                        <td class='text-center'>$status</td>
                      </tr>
                    ";
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-5">
        <div class="card shadow">
          <div class="card-header py-0 bg-danger">
            <h6 class="py-4 text-white">Data Jadwal</h6>
          </div>
          <div class="card-body p-0">

            <div id="t-responsive" class="table-responsive">
              <table id="tabel_hadir" class="table table-sm table-striped" style="font-size:12px;">
                <thead>
                  <tr>
                    <th class="text-center">No</th>
                    <th>Tgl</th>
                    <th class="text-center">Start</th>
                    <th class="text-center">Finish</th>
                    <th class="text-center">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  foreach ($jadwal as $key => $row) {
                    echo "
                      <tr>
                        <td class='text-center'>" . $no++ . "</td>
                        <td>" . date_indo($row->DATE, 'd-M-Y') . "</td>
                        <td class='text-center'>$row->START_TIME</td>
                        <td class='text-center'>$row->FINISH_TIME</td>
                        <td class='text-center'>$row->STATUS</td>
                      </tr>
                    ";
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>

<script type="text/javascript">
  /*
  $('#PERIODE_ID').change(function() {
    // var p = $('#PERIODE_ID').val();
    // window.location.href = "<?php /* echo base_url("absensi/index/") */ ?>"+p;

    $("#myFrom").submit();

    return false;
  });
  */

  $(function() {
    $('#tabel_hadir').DataTable({
      "paging": true,
      "pageLength": 50,
      "lengthChange": true,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>